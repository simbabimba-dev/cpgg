<?php

namespace App\Http\Controllers;

use App\Models\Pterodactyl\Egg;
use App\Models\Pterodactyl\Location;
use App\Models\Pterodactyl\Nest;
use App\Models\Pterodactyl\Node;
use App\Models\Product;
use App\Models\Server;
use App\Models\User;
use App\Notifications\ServerCreationError;
use App\Rules\EggBelongsToProduct;
use App\Settings\DiscordSettings;
use Carbon\Carbon;
use App\Settings\UserSettings;
use App\Settings\ServerSettings;
use App\Settings\PterodactylSettings;
use App\Classes\PterodactylClient;
use App\Enums\BillingPriority;
use App\Settings\GeneralSettings;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Enum;
use Throwable;


class ServerController extends Controller
{
    private const CREATE_PERMISSION = 'user.server.create';
    private const UPGRADE_PERMISSION = 'user.server.upgrade';
    private const BILLING_PERIODS = [
        'hourly' => 3600,
        'daily' => 86400,
        'weekly' => 604800,
        'monthly' => 2592000,
        'quarterly' => 7776000,
        'half-annually' => 15552000,
        'annually' => 31104000
    ];

    private PterodactylClient $pterodactyl;
    private PterodactylSettings $pteroSettings;
    private GeneralSettings $generalSettings;
    private ServerSettings $serverSettings;
    private UserSettings $userSettings;
    private DiscordSettings $discordSettings;

    public function __construct(
        PterodactylSettings $pteroSettings,
        GeneralSettings $generalSettings,
        ServerSettings $serverSettings,
        UserSettings $userSettings,
        DiscordSettings $discordSettings
    ) {
        $this->pteroSettings = $pteroSettings;
        $this->pterodactyl = new PterodactylClient($pteroSettings);
        $this->generalSettings = $generalSettings;
        $this->serverSettings = $serverSettings;
        $this->userSettings = $userSettings;
        $this->discordSettings = $discordSettings;
    }

    public function index(): \Illuminate\View\View
    {
        $servers = $this->getServersWithInfo();

        return view('servers.index')->with([
            'servers' => $servers,
            'credits_display_name' => $this->generalSettings->credits_display_name,
            'pterodactyl_url' => $this->pteroSettings->panel_url,
            'phpmyadmin_url' => $this->generalSettings->phpmyadmin_url
        ]);
    }

    public function create(): \Illuminate\View\View|RedirectResponse
    {
        $this->checkPermission(self::CREATE_PERMISSION);

        $validationResult = $this->validateServerCreation(app(Request::class));
        if ($validationResult) {
            return $validationResult;
        }

        return view('servers.create')->with([
            'productCount' => Product::where('disabled', false)->count(),
            'nodeCount' => Node::whereHas('products', function (Builder $builder) {
                $builder->where('disabled', false);
            })->count(),
            'nests' => Nest::whereHas('eggs', function (Builder $builder) {
                $builder->whereHas('products', function (Builder $builder) {
                    $builder->where('disabled', false);
                });
            })->get(),
            'locations' => Location::all(),
            'eggs' => Egg::whereHas('products', function (Builder $builder) {
                $builder->where('disabled', false);
            })->get(),
            'user' => Auth::user(),
            'server_creation_enabled' => $this->serverSettings->creation_enabled,
            'credits_display_name' => $this->generalSettings->credits_display_name,
            'location_description_enabled' => $this->serverSettings->location_description_enabled,
            'store_enabled' => $this->generalSettings->store_enabled
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->checkPermission(self::CREATE_PERMISSION);

        $rateLimiterKey = 'server-create:' . Auth::id();
        if (RateLimiter::tooManyAttempts($rateLimiterKey, 6)) {
            return redirect()->route('servers.index')
                ->with('error', __('Please wait :seconds seconds before creating another server.', [
                    'seconds' => RateLimiter::availableIn($rateLimiterKey),
                ]));
        }

        $lockKey = 'server_create_lock_' . Auth::id();
        if (Cache::has($lockKey)) {
            return redirect()->route('servers.index')
                ->with('error', __('Please wait a moment before creating another server.'));
        }
        Cache::put($lockKey, true, 5);

        $validationResult = $this->validateServerCreation($request);
        if ($validationResult) return $validationResult;

        $request->validate([
            'name' => 'required|max:191',
            'location' => 'required|exists:locations,id',
            'egg' => ['required', 'exists:eggs,id', new EggBelongsToProduct],
            'product' => 'required|exists:products,id',
            'egg_variables' => 'nullable|string',
            'billing_priority' => ['nullable', new Enum(BillingPriority::class)],
        ]);

        $product = Product::findOrFail($request->input('product'));
        $egg = $product->eggs()->findOrFail($request->input('egg'));
        $submittedVariables = $this->parseSubmittedEggVariables($request->input('egg_variables'));
        try {
            $validatedVariables = $this->validateAndNormalizeEggVariables($egg, $submittedVariables);
        } catch (ValidationException $exception) {
            Log::warning('Server creation blocked due to invalid deployment variables', [
                'user_id' => Auth::id(),
                'egg_id' => $egg->id,
                'product_id' => $product->id,
                'submitted_keys' => array_keys($submittedVariables),
                'errors' => $exception->errors(),
            ]);

            throw $exception;
        }
        $request->merge(['egg_variables' => $validatedVariables]);

        $server = $this->createServer($request);

        if (!$server) {
            return redirect()->route('servers.index')
                ->with('error', __('Server creation failed'));
        }

        RateLimiter::hit($rateLimiterKey, 60);
        $this->handlePostCreation($request->user(), $server);

        return redirect()->route('servers.index')
            ->with('success', __('Server created'));
    }

    private function validateServerCreation(Request $request): ?RedirectResponse
    {
        $user = Auth::user();

        if ($user->servers()->count() >= $user->server_limit) {
            return redirect()->route('servers.index')
                ->with('error', __('Server limit reached!'));
        }

        if ($request->has('product')) {
            $product = Product::findOrFail($request->input('product'));

            $validationResult = $this->validateProductRequirements($product, $request);
            if ($validationResult !== true) {
                return redirect()->route('servers.index')
                    ->with('error', $validationResult);
            }
        }

        if (!$this->validateUserRequirements()) {
            return redirect()->route('profile.index')
                ->with('error', __('User requirements not met'));
        }

        return null;
    }

    private function validateProductRequirements(Product $product, Request $request): string|bool
    {
        $location = $request->input('location');
        $availableNode = $this->findAvailableNode($location, $product);

        if (!$availableNode) {
            return __("The chosen location doesn't have the required memory or disk left to allocate this product.");
        }

        $user = Auth::user();
        $productCount = $user->servers()->where("product_id", $product->id)->count();
        if ($productCount >= $product->serverlimit && $product->serverlimit != 0) {
            return __('You can not create any more Servers with this product!');
        }

        // Determine effective minimum credits; fallback to price when the stored
        // value is missing or nonsensical (e.g. a legacy -1 entry).
        $minCredits = ($product->minimum_credits === null || $product->minimum_credits < $product->price)
            ? $product->price
            : $product->minimum_credits;

        if ($user->credits < $minCredits) {
            return __('You do not have the required amount of :credits to use this product!', [
                'credits' => $this->generalSettings->credits_display_name,
            ]);
        }

        return true;
    }

    private function validateUserRequirements(): bool
    {
        $user = Auth::user();

        if ($this->userSettings->force_email_verification && !$user->hasVerifiedEmail()) {
            return false;
        }

        if (!$this->serverSettings->creation_enabled && $user->cannot("admin.servers.bypass_creation_enabled")) {
            return false;
        }

        if ($this->userSettings->force_discord_verification && !$user->discordUser) {
            return false;
        }

        return true;
    }

    private function getServersWithInfo(): \Illuminate\Database\Eloquent\Collection
    {
        $servers = Auth::user()->servers;

        foreach ($servers as $server) {
            $serverInfo = $this->pterodactyl->getServerAttributes($server->pterodactyl_id);
            if (!$serverInfo) continue;

            $this->updateServerInfo($server, $serverInfo);
        }

        return $servers;
    }

    private function updateServerInfo(Server $server, array $serverInfo): void
    {
        try {
            if (!isset($serverInfo['relationships'])) {
                return;
            }

            $relationships = $serverInfo['relationships'];
            $locationAttrs = $relationships['location']['attributes'] ?? [];
            $eggAttrs = $relationships['egg']['attributes'] ?? [];
            $nestAttrs = $relationships['nest']['attributes'] ?? [];
            $nodeAttrs = $relationships['node']['attributes'] ?? [];

            $server->location = $locationAttrs['long'] ?? $locationAttrs['short'] ?? null;
            $server->egg = $eggAttrs['name'] ?? null;
            $server->nest = $nestAttrs['name'] ?? null;
            $server->node = $nodeAttrs['name'] ?? null;

            if (isset($serverInfo['name']) && $server->name !== $serverInfo['name']) {
                $server->name = $serverInfo['name'];
                $server->save();
            }

            if ($server->product_id) {
                $server->setRelation('product', Product::find($server->product_id));
            }
        } catch (Exception $e) {
            Log::error('Failed to update server info', [
                'server_id' => $server->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function createServer(Request $request): ?Server
    {
        $product = Product::findOrFail($request->input('product'));
        $egg = $product->eggs()->findOrFail($request->input('egg'));
        $node = $this->findAvailableNode($request->input('location'), $product);

        if (!$node) return null;

        $server = $request->user()->servers()->create([
            'name' => $request->input('name'),
            'product_id' => $product->id,
            'last_billed' => Carbon::now(),
            'billing_priority' => $request->input('billing_priority', $product->default_billing_priority),
        ]);

        $allocationId = $this->pterodactyl->getFreeAllocationId($node);
        if (!$allocationId) {
            Log::error('No AllocationID found.', [
                'server_id' => $server->id,
                'node_id' => $node->id,
            ]);
            $server->delete();
            return null;
        }

        $response = $this->pterodactyl->createServer($server, $egg, $allocationId, $request->input('egg_variables'));
        if ($response->failed()) {
            Log::error('Failed to create server on Pterodactyl', [
                'server_id' => $server->id,
                'status' => $response->status(),
                'error' => $response->json()
            ]);
            $server->delete();
            return null;
        }

        $serverAttributes = $response->json()['attributes'];
        $server->update([
            'pterodactyl_id' => $serverAttributes['id'],
            'identifier' => $serverAttributes['identifier']
        ]);

        return $server;
    }

    private function handlePostCreation(User $user, Server $server): void
    {
        logger('Product Price: ' . $server->product->price);

        $user->decrement('credits', $server->product->price);
        Cache::forget('user_credits_left:' . $user->id);

        try {
            if ($this->discordSettings->role_for_active_clients &&
                $user->discordUser &&
                $user->servers->count() >= 1
            ) {
                $user->discordUser->addOrRemoveRole(
                    'add',
                    $this->discordSettings->role_id_for_active_clients
                );
            }
        } catch (Exception $e) {
            Log::debug('Discord role update failed: ' . $e->getMessage());
        }
    }

    public function destroy(Server $server): RedirectResponse
    {
        if ($server->user_id !== Auth::id()) {
            return back()->with('error', __('This is not your Server!'));
        }

        try {
            $serverInfo = $this->pterodactyl->getServerAttributes($server->pterodactyl_id);

            if (!$serverInfo) {
                throw new Exception("Server not found on Pterodactyl panel");
            }

            $this->handleServerDeletion($server);

            return redirect()->route('servers.index')
                ->with('success', __('Server removed'));
        } catch (Exception $e) {
            $errorId = (string) Str::uuid();
            Log::error('Server deletion failed', [
                'error_id' => $errorId,
                'server_id' => $server->id,
                'pterodactyl_id' => $server->pterodactyl_id,
                'exception' => $e,
            ]);

            return redirect()->route('servers.index')
                ->with('error', __('Server removal failed. Reference: :id', ['id' => $errorId]));
        }
    }

    private function handleServerDeletion(Server $server): void
    {
        if ($this->discordSettings->role_for_active_clients) {
            $user = User::findOrFail($server->user_id);
            if ($user->discordUser && $user->servers->count() <= 1) {
                $user->discordUser->addOrRemoveRole(
                    'remove',
                    $this->discordSettings->role_id_for_active_clients
                );
            }
        }

        $server->delete();
        Cache::forget('user_credits_left:' . $server->user_id);
    }

    public function cancel(Server $server): RedirectResponse
    {
        if ($server->user_id !== Auth::id()) {
            return back()->with('error', __('This is not your Server!'));
        }

        try {
            $server->update(['canceled' => now()]);
            return redirect()->route('servers.index')
                ->with('success', __('Server canceled'));
        } catch (Exception $e) {
            $errorId = (string) Str::uuid();
            Log::error('Server cancellation failed', [
                'error_id' => $errorId,
                'server_id' => $server->id,
                'exception' => $e,
            ]);

            return redirect()->route('servers.index')
                ->with('error', __('Server cancellation failed. Reference: :id', ['id' => $errorId]));
        }
    }

    public function show(Server $server): \Illuminate\View\View
    {
        if ($server->user_id !== Auth::id()) {
            return back()->with('error', __('This is not your Server!'));
        }

        $serverAttributes = $this->pterodactyl->getServerAttributes($server->pterodactyl_id);
        $upgradeOptions = $this->getUpgradeOptions($server, $serverAttributes);
        return view('servers.settings')->with([
            'server' => $server,
            'serverAttributes' => $serverAttributes,
            'products' => $upgradeOptions,
            'server_enable_upgrade' => $this->serverSettings->enable_upgrade,
            'credits_display_name' => $this->generalSettings->credits_display_name,
            'location_description_enabled' => $this->serverSettings->location_description_enabled,
        ]);
    }

    private function getUpgradeOptions(Server $server, array $serverInfo): \Illuminate\Database\Eloquent\Collection
    {
        $currentProduct = Product::find($server->product_id);
        $nodeId = $serverInfo['relationships']['node']['attributes']['id'];
        $pteroNode = $this->pterodactyl->getNode($nodeId);
        $currentEgg = $serverInfo['egg'];

        //$currentProductEggs = $currentProduct->eggs->pluck('id')->toArray();

        return Product::orderBy('price', 'asc')
            ->with('nodes')->with('eggs')
            ->whereHas('nodes', function (Builder $builder) use ($nodeId) {
                $builder->where('id', $nodeId);
            })
            ->whereHas('eggs', function (Builder $builder) use ($currentEgg) {
                $builder->where('id', $currentEgg);
            })
            ->get()
            ->map(function ($product) use ($currentProduct, $pteroNode) {
                $product->eggs = $product->eggs->pluck('name')->toArray();

                $memoryDiff = $product->memory - $currentProduct->memory;
                $diskDiff = $product->disk - $currentProduct->disk;

                $maxMemory = ($pteroNode['memory'] * ($pteroNode['memory_overallocate'] + 100) / 100);
                $maxDisk = ($pteroNode['disk'] * ($pteroNode['disk_overallocate'] + 100) / 100);

                if ($memoryDiff > $maxMemory - $pteroNode['allocated_resources']['memory'] ||
                    $diskDiff > $maxDisk - $pteroNode['allocated_resources']['disk']) {
                    $product->doesNotFit = true;
                }

                return $product;
            });
    }


    public function upgrade(Server $server, Request $request): RedirectResponse
    {
        $this->checkPermission(self::UPGRADE_PERMISSION);

        if ($server->user_id !== Auth::id()) {
            return redirect()->route('servers.index')
                ->with('error', __('This is not your Server!'));
        }

        if (!$request->has('product_upgrade')) {
            return redirect()->route('servers.show', ['server' => $server->id])
                ->with('error', __('No product selected for upgrade'));
        }

        $user = Auth::user();
        $oldProduct = Product::find($server->product->id);
        $newProduct = Product::find($request->product_upgrade);

        if (!$newProduct) {
            return redirect()->route('servers.show', ['server' => $server->id])
                ->with('error', __('Selected product not found'));
        }

        if (!$this->validateUpgrade($server, $oldProduct, $newProduct)) {
            return redirect()->route('servers.show', ['server' => $server->id])
                ->with('error', __('Insufficient resources or credits for upgrade'));
        }

        try {
            $this->processUpgrade($server, $oldProduct, $newProduct, $user);
            return redirect()->route('servers.show', ['server' => $server->id])
                ->with('success', __('Server Successfully Upgraded'));
        } catch (Exception $e) {
            $errorId = (string) Str::uuid();
            Log::error('Server upgrade failed', [
                'error_id' => $errorId,
                'server_id' => $server->id,
                'old_product' => $oldProduct->id,
                'new_product' => $newProduct->id,
                'exception' => $e,
            ]);

            return redirect()->route('servers.show', ['server' => $server->id])
                ->with('error', __('Upgrade failed. Reference: :id', ['id' => $errorId]));
        }
    }

    public function updateBillingPriority(Server $server, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'billing_priority' => ['required', new Enum(BillingPriority::class)],
        ]);

        if ($server->user_id !== Auth::id()) {
            return redirect()->route('servers.index')
                ->with('error', __('This is not your Server!'));
        }

        $server->update($data);

        return redirect()->route('servers.show', ['server' => $server->id])
            ->with('success', __('Billing priority updated successfully'));
    }

    private function validateUpgrade(Server $server, Product $oldProduct, Product $newProduct): bool
    {
        $user = Auth::user();
        if (!$server->product) {
            return false;
        }

        $serverInfo = $this->pterodactyl->getServerAttributes($server->pterodactyl_id);
        if (!$serverInfo) {
            return false;
        }

        $nodeId = $serverInfo['relationships']['node']['attributes']['id'];
        $node = Node::findOrFail($nodeId);

        // Check node resources
        $requireMemory = $newProduct->memory - $oldProduct->memory;
        $requireDisk = $newProduct->disk - $oldProduct->disk;
        if (!$this->pterodactyl->checkNodeResources($node, $requireMemory, $requireDisk)) {
            return false;
        }

        // Check if user has enough credits after refund
        $refundAmount = $this->calculateRefund($server, $oldProduct);
        if ($user->credits < ($newProduct->price - $refundAmount)) {
            return false;
        }

        return true;
    }

    private function processUpgrade(Server $server, Product $oldProduct, Product $newProduct, User $user): void
    {
        $server->allocation = $this->pterodactyl->getServerAttributes($server->pterodactyl_id)['allocation'];

        $response = $this->pterodactyl->updateServer($server, $newProduct);
        if ($response->failed()) {
            throw new Exception("Failed to update server on Pterodactyl");
        }

        $restartResponse = $this->pterodactyl->powerAction($server, 'restart');
        if ($restartResponse->failed()) {
            throw new Exception('Could not restart the server: ' . $restartResponse->json()['errors'][0]['detail']);
        }

        // Calculate refund
        $refund = $this->calculateRefund($server, $oldProduct);
        if ($refund > 0) {
            $user->increment('credits', $refund);
        }

        // Update server
        unset($server->allocation);
        $server->update([
            'product_id' => $newProduct->id,
            'updated_at' => now(),
            'last_billed' => now(),
            'canceled' => null,
        ]);

        // Charge for new product
        $user->decrement('credits', $newProduct->price);
    }

    private function calculateRefund(Server $server, Product $oldProduct): float
    {
        $billingPeriod = $oldProduct->billing_period;
        $billingPeriodSeconds = self::BILLING_PERIODS[$billingPeriod];
        $timeUsed = now()->diffInSeconds($server->last_billed, true);

        return $oldProduct->price - ($oldProduct->price * ($timeUsed / $billingPeriodSeconds));
    }

    private function findAvailableNode(string $locationId, Product $product): ?Node
    {
        $nodes = Node::where('location_id', $locationId)
            ->whereHas('products', fn($q) => $q->where('product_id', $product->id))
            ->get();

        $availableNodes = $nodes->reject(function ($node) use ($product) {
            return !$this->pterodactyl->checkNodeResources($node, $product->memory, $product->disk);
        });

        return $availableNodes->isEmpty() ? null : $availableNodes->first();
    }

    public function validateDeploymentVariables(Request $request)
    {
        $this->checkPermission(self::CREATE_PERMISSION);

        $validator = Validator::make($request->all(), [
            'egg_id' => ['required', 'integer', 'exists:eggs,id', new EggBelongsToProduct],
            'product_id' => 'required|string|exists:products,id',
            'variables' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $product = Product::find($data['product_id']);
        if (!$product) {
            return response()->json([
                'errors' => [
                    'product_id' => [__('The selected product is invalid.')],
                ],
            ], 422);
        }

        $egg = $product->eggs()->find($data['egg_id']);
        if (!$egg) {
            return response()->json([
                'errors' => [
                    'egg_id' => [__('The selected egg is invalid for this product.')],
                ],
            ], 422);
        }

        try {
            $variables = $this->validateAndNormalizeEggVariables($egg, $data['variables'] ?? []);
        } catch (ValidationException $exception) {
            Log::warning('Deployment variable validation tampering detected', [
                'user_id' => Auth::id(),
                'egg_id' => $egg->id,
                'product_id' => $product->id,
                'submitted_keys' => array_keys($data['variables'] ?? []),
                'errors' => $exception->errors(),
            ]);

            return response()->json([
                'errors' => $exception->errors(),
            ], 422);
        } catch (Throwable $exception) {
            $errorId = (string) Str::uuid();
            Log::error('Unexpected deployment variable validator failure', [
                'error_id' => $errorId,
                'user_id' => Auth::id(),
                'egg_id' => $egg->id,
                'product_id' => $product->id,
                'exception' => $exception,
            ]);

            return response()->json([
                'message' => __('Unable to validate deployment variables right now.'),
                'error_id' => $errorId,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'variables' => $variables,
        ]);
    }

    /**
     * @throws ValidationException
     */
    private function parseSubmittedEggVariables(mixed $rawEggVariables): array
    {
        if (is_null($rawEggVariables) || $rawEggVariables === '') {
            return [];
        }

        $variables = $rawEggVariables;
        if (is_string($rawEggVariables)) {
            $variables = json_decode($rawEggVariables, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($variables)) {
                throw ValidationException::withMessages([
                    'egg_variables' => [__('The deployment variables payload is invalid.')],
                ]);
            }
        }

        if (!is_array($variables)) {
            throw ValidationException::withMessages([
                'egg_variables' => [__('The deployment variables payload must be an object.')],
            ]);
        }

        foreach ($variables as $key => $value) {
            if (!is_string($key) || $key === '') {
                throw ValidationException::withMessages([
                    'egg_variables' => [__('The deployment variables payload contains an invalid key.')],
                ]);
            }

            if (!is_scalar($value) && !is_null($value)) {
                throw ValidationException::withMessages([
                    'egg_variables' => [__('Each deployment variable value must be a string.')],
                ]);
            }
        }

        return $variables;
    }

    /**
     * @throws ValidationException
     */
    private function validateAndNormalizeEggVariables(Egg $egg, array $submittedVariables): array
    {
        $environment = collect($egg->environment ?? [])->keyBy('env_variable');
        $errors = [];
        $normalized = [];

        foreach ($submittedVariables as $envKey => $value) {
            $definition = $environment->get($envKey);

            if (!$definition) {
                $errors[$envKey][] = __('Unknown deployment variable.');
                continue;
            }

            if (empty($definition['user_editable'])) {
                $errors[$envKey][] = __('This deployment variable is not user editable.');
                continue;
            }

            $normalized[$envKey] = is_null($value) ? null : trim((string) $value);
        }

        foreach ($environment as $envKey => $definition) {
            $isEditable = !empty($definition['user_editable']);
            $defaultValue = $definition['default_value'] ?? null;
            $rules = $definition['rules'] ?? 'nullable|string';
            $displayName = $definition['name'] ?? $envKey;

            if (!$isEditable) {
                if ($this->isRequiredWithoutDefault($rules, $defaultValue)) {
                    $errors[$envKey][] = __('This deployment variable is required but not user editable.');
                }
                continue;
            }

            $hasSubmittedValue = array_key_exists($envKey, $normalized);
            if (!$hasSubmittedValue && !$this->isRequiredWithoutDefault($rules, $defaultValue)) {
                continue;
            }

            $value = $hasSubmittedValue ? $normalized[$envKey] : $defaultValue;
            $validator = Validator::make(
                [$envKey => $value],
                [$envKey => $rules]
            );

            $validator->setAttributeNames([
                $envKey => $displayName,
            ]);

            if ($validator->fails()) {
                $errors[$envKey] = $validator->errors()->get($envKey);
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return $normalized;
    }

    private function isRequiredWithoutDefault(string $rules, mixed $defaultValue): bool
    {
        return str_contains($rules, 'required') && empty($defaultValue);
    }
}
