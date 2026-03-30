<?php

namespace Tests\Feature;

use App\Models\ApplicationApi;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Pterodactyl\Egg;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SecurityRemediationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_non_privileged_user_cannot_download_invoices(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.invoices.downloadAllInvoices'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.invoices.downloadSingleInvoice', ['id' => 'test']))
            ->assertForbidden();
    }

    public function test_non_privileged_user_cannot_create_server_or_pay(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('servers.store'), [])
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('payment.pay'), [])
            ->assertForbidden();
    }

    public function test_non_privileged_user_cannot_access_admin_coupon_redeem_endpoint(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.coupon.redeem'), [
                'couponCode' => 'TESTCODE',
                'productId' => 'non-existent',
            ])
            ->assertForbidden();
    }

    public function test_profile_view_does_not_expose_discord_client_secret(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.index'))
            ->assertOk()
            ->assertViewMissing('discord_client_secret')
            ->assertViewHas('discord_oauth_configured');
    }

    public function test_api_scopes_enforce_read_write_boundaries(): void
    {
        $token = ApplicationApi::factory()->create([
            'scopes' => ['users.read'],
        ]);

        $headers = $this->apiTokenHeaders($token->token);

        $this->withHeaders($headers)
            ->get('/api/users')
            ->assertOk();

        $this->withHeaders($headers)
            ->post('/api/users', [])
            ->assertForbidden()
            ->assertJsonPath('required_ability', 'users.write');
    }

    public function test_api_scopes_deny_resource_without_scope(): void
    {
        $token = ApplicationApi::factory()->create([
            'scopes' => ['servers.read'],
        ]);

        $this->withHeaders($this->apiTokenHeaders($token->token))
            ->get('/api/users')
            ->assertForbidden()
            ->assertJsonPath('required_ability', 'users.read');
    }

    public function test_legacy_unscoped_tokens_can_be_allowed_in_compat_mode(): void
    {
        config(['security.api_scopes.allow_legacy_unscoped_tokens' => true]);
        $token = ApplicationApi::factory()->create(['scopes' => null]);

        $this->withHeaders($this->apiTokenHeaders($token->token))
            ->get('/api/users')
            ->assertOk();
    }

    public function test_legacy_unscoped_tokens_are_denied_in_strict_mode(): void
    {
        config(['security.api_scopes.allow_legacy_unscoped_tokens' => false]);
        $token = ApplicationApi::factory()->create(['scopes' => null]);

        $this->withHeaders($this->apiTokenHeaders($token->token))
            ->get('/api/users')
            ->assertForbidden()
            ->assertJsonPath('required_ability', 'users.read');
    }

    public function test_validate_deployment_variables_rejects_unknown_or_non_editable_keys(): void
    {
        $user = User::factory()->create();
        $this->grantPermission($user, 'user.server.create');
        [$product, $egg] = $this->createProductWithEgg();

        $payload = [
            'egg_id' => $egg->id,
            'product_id' => $product->id,
            'variables' => [
                'MOTD' => 'Hello',
                'SERVER_PORT' => '25566',
                'HACKED_KEY' => 'evil',
            ],
            // Attempted client-side tampering metadata should be non-authoritative.
            'rules' => ['SERVER_PORT' => 'nullable|string'],
        ];

        $this->actingAs($user)
            ->postJson(route('servers.validateDeploymentVariables'), $payload)
            ->assertStatus(422)
            ->assertJsonStructure(['errors'])
            ->assertJsonPath('errors.SERVER_PORT.0', __('This deployment variable is not user editable.'))
            ->assertJsonPath('errors.HACKED_KEY.0', __('Unknown deployment variable.'));
    }

    public function test_validate_deployment_variables_accepts_valid_user_editable_values(): void
    {
        $user = User::factory()->create();
        $this->grantPermission($user, 'user.server.create');
        [$product, $egg] = $this->createProductWithEgg();

        $payload = [
            'egg_id' => $egg->id,
            'product_id' => $product->id,
            'variables' => [
                'MOTD' => 'Secure hello',
            ],
        ];

        $this->actingAs($user)
            ->postJson(route('servers.validateDeploymentVariables'), $payload)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('variables.MOTD', 'Secure hello');
    }

    private function apiTokenHeaders(string $token): array
    {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];
    }

    private function grantPermission(User $user, string $permission): void
    {
        Permission::findOrCreate($permission, 'web');
        $user->givePermissionTo($permission);
    }

    private function createProductWithEgg(): array
    {
        $nestId = DB::table('nests')->insertGetId([
            'name' => 'Security Test Nest',
            'description' => 'Security test nest',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $egg = Egg::create([
            'id' => random_int(10000, 99999),
            'nest_id' => $nestId,
            'name' => 'Security Test Egg',
            'description' => 'Egg for deployment variable validation tests',
            'docker_image' => 'ghcr.io/pterodactyl/yolks:nodejs_18',
            'startup' => 'npm start',
            'environment' => [
                [
                    'name' => 'MOTD',
                    'description' => 'Server welcome message',
                    'default_value' => null,
                    'env_variable' => 'MOTD',
                    'user_viewable' => true,
                    'user_editable' => true,
                    'rules' => 'required|string|max:64',
                ],
                [
                    'name' => 'SERVER_PORT',
                    'description' => 'Port managed by platform',
                    'default_value' => '25565',
                    'env_variable' => 'SERVER_PORT',
                    'user_viewable' => true,
                    'user_editable' => false,
                    'rules' => 'required|integer|min:1|max:65535',
                ],
            ],
        ]);

        $product = Product::create([
            'name' => 'Security Test Product',
            'description' => 'Test product',
            'price' => 1000,
            'minimum_credits' => 1000,
            'memory' => 1024,
            'cpu' => 100,
            'swap' => 0,
            'disk' => 2048,
            'io' => 500,
            'databases' => 1,
            'backups' => 1,
            'allocations' => 1,
            'serverlimit' => 0,
            'disabled' => false,
            'oom_killer' => true,
            'billing_period' => 'monthly',
            'default_billing_priority' => 2,
        ]);

        $product->eggs()->attach($egg->id);

        return [$product, $egg];
    }
}
