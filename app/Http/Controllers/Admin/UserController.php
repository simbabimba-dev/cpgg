<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserUpdateCreditsEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\DynamicNotification;
use App\Settings\LocaleSettings;
use App\Settings\PterodactylSettings;
use App\Classes\PterodactylClient;
use App\Helpers\CurrencyHelper;
use App\Settings\GeneralSettings;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    const READ_PERMISSION = "admin.users.read";
    const WRITE_PERMISSION = "admin.users.write";
    const SUSPEND_PERMISSION = "admin.users.suspend";
    const CHANGE_EMAIL_PERMISSION = "admin.users.write.email";
    const CHANGE_CREDITS_PERMISSION = "admin.users.write.credits";
    const CHANGE_USERNAME_PERMISSION = "admin.users.write.username";
    const CHANGE_PASSWORD_PERMISSION = "admin.users.write.password";
    const CHANGE_ROLE_PERMISSION ="admin.users.write.role";
    const CHANGE_REFERRAL_PERMISSION ="admin.users.write.referral";
    const CHANGE_PTERO_PERMISSION = "admin.users.write.pterodactyl";

    const CHANGE_SERVERLIMIT_PERMISSION = "admin.users.write.serverlimit";
    const DELETE_PERMISSION = "admin.users.delete";
    const NOTIFY_PERMISSION = "admin.users.notify";
    const LOGIN_PERMISSION = "admin.users.login_as";


    private $pterodactyl;

    public function __construct(PterodactylSettings $ptero_settings)
    {
        $this->pterodactyl = new PterodactylClient($ptero_settings);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return Application|Factory|View|Response
     */
    public function index(LocaleSettings $locale_settings, GeneralSettings $general_settings)
    {
        $allConstants = (new \ReflectionClass(__CLASS__))->getConstants();
        $this->checkAnyPermission($allConstants);

        //$this->checkPermission(self::READ_PERMISSION);

        return view('admin.users.index', [
            'locale_datatables' => $locale_settings->datatables,
            'credits_display_name' => $general_settings->credits_display_name
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  User  $user
     * @return Application|Factory|View|Response
     */
    public function show(User $user, LocaleSettings $locale_settings, GeneralSettings $general_settings)
    {
        $this->checkPermission(self::READ_PERMISSION);

        return view('admin.users.show')->with([
            'user' => $user,
            'referrals' => $this->getReferralsForUser($user),
            'locale_datatables' => $locale_settings->datatables,
            'credits_display_name' => $general_settings->credits_display_name
        ]);
    }

    /**
     * Get a JSON response of users.
     *
     * @return \Illuminate\Support\Collection|\App\models\User
     */
    public function json(Request $request)
    {
        $this->checkPermission(self::READ_PERMISSION);

        if ($request->query('user_id')) {
            $request->validate(['user_id' => 'required|integer|exists:users,id']);

            $user = User::query()->findOrFail($request->input('user_id'));
            return $this->formatUserJson($user);
        }

        $users = QueryBuilder::for(User::query()->select(['id', 'name', 'email', 'pterodactyl_id']))
            ->allowedFilters(['id', 'name', 'pterodactyl_id', 'email'])
            ->paginate(25);

        return $users->getCollection()
            ->map(fn (User $item) => $this->formatUserJson($item))
            ->values();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  User  $user
     * @return Application|Factory|View|Response
     */
    public function edit(User $user, GeneralSettings $general_settings)
    {
        $allConstants = (new \ReflectionClass(__CLASS__))->getConstants();
        $permissions = array_filter($allConstants, fn($key) => str_starts_with($key, 'admin.users.write'));
        $this->checkAnyPermission($permissions);

        $roles = Role::all();
        return view('admin.users.edit')->with([
            'user' => $user,
            'credits_display_name' => $general_settings->credits_display_name,
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  User  $user
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function update(Request $request, User $user)
    {
        $this->checkAnyPermission([
            self::WRITE_PERMISSION,
            self::CHANGE_EMAIL_PERMISSION,
            self::CHANGE_CREDITS_PERMISSION,
            self::CHANGE_USERNAME_PERMISSION,
            self::CHANGE_PASSWORD_PERMISSION,
            self::CHANGE_ROLE_PERMISSION,
            self::CHANGE_REFERRAL_PERMISSION,
            self::CHANGE_PTERO_PERMISSION,
            self::CHANGE_SERVERLIMIT_PERMISSION,
        ]);

        $data = $request->validate([
            'name' => 'required|string|min:4|max:30',
            'pterodactyl_id' => "required|numeric|unique:users,pterodactyl_id,{$user->id}",
            'email' => 'required|string|email',
            'credits' => 'required|numeric|min:0|max:99999999',
            'server_limit' => 'required|numeric|min:0|max:1000000',
            'referral_code' => "required|string|min:2|max:32|unique:users,referral_code,{$user->id}",
        ]);

        //update roles
        if ($request->roles && $this->can(self::CHANGE_ROLE_PERMISSION)) {
            $collectedRoles = collect($request->roles)->map(fn($val)=>(int)$val);
            $user->syncRoles($collectedRoles);
        }

        if (isset($this->pterodactyl->getUser($request->input('pterodactyl_id'))['errors'])) {
            throw ValidationException::withMessages([
                'pterodactyl_id' => [__("User does not exists on pterodactyl's panel")],
            ]);
        }

        $dataArray = [];

        if ($this->canAny([self::CHANGE_USERNAME_PERMISSION, self::WRITE_PERMISSION]) && $request->filled('name')) {
            $dataArray['name'] = $request->input('name');
        }

        if ($this->canAny([self::CHANGE_CREDITS_PERMISSION, self::WRITE_PERMISSION]) && $request->filled('credits')) {
            $dataArray['credits'] = $request->input('credits');
        }

        if ($this->canAny([self::CHANGE_PTERO_PERMISSION, self::WRITE_PERMISSION]) && $request->filled('pterodactyl_id')) {
            $dataArray['pterodactyl_id'] = $request->input('pterodactyl_id');
        }

        if ($this->canAny([self::CHANGE_REFERRAL_PERMISSION, self::WRITE_PERMISSION]) && $request->filled('referral_code')) {
            $dataArray['referral_code'] = $request->input('referral_code');
        }

        if ($this->canAny([self::CHANGE_EMAIL_PERMISSION, self::WRITE_PERMISSION]) && $request->filled('email')) {
            $dataArray['email'] = $request->input('email');
        }

        if ($this->canAny([self::CHANGE_SERVERLIMIT_PERMISSION, self::WRITE_PERMISSION]) && $request->filled('server_limit')) {
            $dataArray['server_limit'] = $request->input('server_limit');
        }


        // Update password separately with validation, if permission is granted
        if (!is_null($request->input('new_password')) && $this->canAny([self::CHANGE_PASSWORD_PERMISSION, self::WRITE_PERMISSION])) {
            $request->validate([
                'new_password' => 'required|string|min:8',
                'new_password_confirmation' => 'required|same:new_password',
            ]);

            $dataArray['password'] = Hash::make($request->input('new_password'));
        }

        // Only update with the collected data
        if (!empty($dataArray)) {
            $user->update($dataArray);

            try {
                $pteroData = array_filter([
                    "email" => $user->email,
                    "username" => $user->name,
                    "first_name" => $user->name,
                    "last_name" => $user->name,
                    "language" => "en",
                    "password" => $request->filled('new_password') ? $request->input('new_password') : null
                ]);

                $this->pterodactyl->updateUser($user->pterodactyl_id, $pteroData);
            } catch (Exception $e) {
                $errorId = (string) Str::uuid();
                Log::error('Failed to update user on pterodactyl', [
                    'error_id' => $errorId,
                    'user_id' => $user->id,
                    'exception' => $e,
                ]);

                return redirect()->back()->with('error', __('User updated, but syncing with pterodactyl failed. Reference: :id', ['id' => $errorId]));
            }
        }

        event(new UserUpdateCreditsEvent($user));

        return redirect()->route('admin.users.index')->with('success', 'User updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     * @return RedirectResponse
     */
    public function destroy(User $user)
    {
        $this->checkPermission(self::DELETE_PERMISSION);

        if ($user->hasRole(1) && User::role(1)->count() === 1) {
            return redirect()->back()->with('error', __('You can not delete the last admin!'));
        }

        $user->delete();

        return redirect()->back()->with('success', __('user has been removed!'));
    }

    /**
     * Verifys the users email
     *
     * @param  User  $user
     * @return RedirectResponse
     */
    public function verifyEmail(User $user)
    {
        $this->checkPermission(self::WRITE_PERMISSION);

        $user->verifyEmail();

        return redirect()->back()->with('success', __('Email has been verified!'));
    }

    /**
     * @param  Request  $request
     * @param  User  $user
     * @return RedirectResponse
     */
    public function loginAs(Request $request, User $user)
    {
        $this->checkPermission(self::LOGIN_PERMISSION);

        $request->session()->put('previousUser', Auth::user()->id);
        Auth::login($user);

        return redirect()->route('home');
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function logBackIn(Request $request)
    {
        $this->checkPermission(self::LOGIN_PERMISSION);

        Auth::loginUsingId($request->session()->get('previousUser'), true);
        $request->session()->remove('previousUser');

        return redirect()->route('admin.users.index');
    }

    /**
     * Show the form for seding notifications to the specified resource.
     *
     * @param  User  $user
     * @return Application|Factory|View|Response
     */
    public function notifications()
    {
        $this->checkPermission(self::NOTIFY_PERMISSION);

        $roles = Role::all();

        return view('admin.users.notifications')->with(["roles" => $roles]);
    }

    /**
     * Notify the specified resource.
     *
     * @param  Request  $request
     * @param  User  $user
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function notify(Request $request)
    {
        $this->checkPermission(self::NOTIFY_PERMISSION);

        $data = $request->validate([
            'via' => 'required|min:1|array',
            'via.*' => 'required|string|in:mail,database',
            'all' => 'boolean',
            'users' => 'sometimes|array|min:1',
            'users.*' => 'integer|exists:users,id',
            'roles' => 'sometimes|array|min:1',
            'roles.*' => 'integer|exists:roles,id',
            'title' => 'required|string|min:1',
            'content' => 'required|string|min:1',
        ]);

        $all = $data['all'] ?? false;
        $roles = $data['roles'] ?? [];
        $targetUserIds = $data['users'] ?? [];
        if (!$all && empty($roles) && empty($targetUserIds)) {
            throw ValidationException::withMessages([
                'users' => [__('Please select at least one target user, role, or send to all users.')],
            ]);
        }

        $title = $this->sanitizeNotificationText($data['title']);
        $content = $this->sanitizeNotificationText($data['content']);

        $mail = null;
        $database = null;
        if (in_array('database', $data['via'])) {
            $database = [
                'title' => $title,
                'content' => $content,
            ];
        }
        if (in_array('mail', $data['via'])) {
            $mail = (new MailMessage)
                ->subject($title)
                ->markdown('mail.custom', ['content' => $content]);
        }
        if (empty($roles)) {
            $users = $all ? User::where('suspended', false)->get() : User::whereIn('id', $targetUserIds)->get();
        } else{
            // Initialize an empty collection to hold users from all roles
            $users = collect();

            // Loop through each role ID and fetch users
            foreach ($data["roles"] as $roleId) {
                $roleUsers = User::whereHas('roles', function ($query) use ($roleId) {
                    $query->where('id', $roleId);
                })->get();

                // Merge users from this role into the main collection
                $users = $users->merge($roleUsers);
            }

            // Remove duplicate users (if any)
            $users = $users->unique('id');
        }


        $successCount = 0;
        foreach ($users as $user) {
            try {
                $user->notify(new DynamicNotification($data['via'], $database, $mail));
                $successCount++;
            } catch (\Throwable $e) {
                Log::error('Mass notification error for user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.users.notifications.index')->with('success', __('Notification sent to :count users!', ['count' => $successCount]));
    }

    /**
     * @param  User  $user
     * @return RedirectResponse
     */
    public function toggleSuspended(User $user)
    {
        $this->checkPermission(self::SUSPEND_PERMISSION);

        if (Auth::user()->id === $user->id) {
            return redirect()->back()->with('error', __('You can not suspend yourself!'));
        }

        try {
            !$user->isSuspended() ? $user->suspend() : $user->unSuspend();
        } catch (Exception $exception) {
            $errorId = (string) Str::uuid();
            Log::error('Failed to toggle user suspension', [
                'error_id' => $errorId,
                'user_id' => $user->id,
                'exception' => $exception,
            ]);

            return redirect()->back()->with('error', __('Unable to update user status. Reference: :id', ['id' => $errorId]));
        }

        return redirect()->back()->with('success', __('User has been updated!'));
    }

    /**
     * @throws Exception
     */
    public function dataTable(Request $request)
    {
        $this->checkPermission(self::READ_PERMISSION);

        $referralCounts = DB::table('user_referrals')
            ->select('referral_id', DB::raw('COUNT(*) as referrals_count'))
            ->groupBy('referral_id');

        $query = User::query()
            ->select('users.*')
            ->with('discordUser')
            ->withCount('servers')
            ->leftJoinSub($referralCounts, 'referral_counts', function ($join) {
                $join->on('users.id', '=', 'referral_counts.referral_id');
            })
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->addSelect([
                'roles.name as role_name',
                DB::raw('COALESCE(referral_counts.referrals_count, 0) as referrals_count'),
            ])
            ->where('model_has_roles.model_type', User::class);

        return datatables($query)
            ->addColumn('avatar', function (User $user) {
                return '<img width="28px" height="28px" class="ml-1 rounded-circle" src="' . $user->getAvatar() . '">';
            })
            ->addColumn('credits', function (User $user, CurrencyHelper $currencyHelper) {
                return '<i class="mr-2 fas fa-coins"></i> ' . $currencyHelper->formatForDisplay($user->credits);
            })
            ->addColumn('verified', function (User $user) {
                return $user->getVerifiedStatus();
            })
            ->addColumn('discordId', function (User $user) {
                return $user->discordUser ? $user->discordUser->id : '';
            })
            ->addColumn('actions', function (User $user) {
                $suspendColor = $user->isSuspended() ? 'btn-success' : 'btn-warning';
                $suspendIcon = $user->isSuspended() ? 'fa-play-circle' : 'fa-pause-circle';
                $suspendText = $user->isSuspended() ? __('Unsuspend') : __('Suspend');

                return '
                <a data-content="' . __('Login as User') . '" data-toggle="popover" data-trigger="hover" data-placement="top" href="' . route('admin.users.loginas', $user->id) . '" class="mr-1 btn btn-sm btn-primary"><i class="fas fa-sign-in-alt"></i></a>
                <a data-content="' . __('Verify') . '" data-toggle="popover" data-trigger="hover" data-placement="top" href="' . route('admin.users.verifyEmail', $user->id) . '" class="mr-1 btn btn-sm btn-info"><i class="fas fa-envelope"></i></a>
                <a data-content="' . __('Show') . '" data-toggle="popover" data-trigger="hover" data-placement="top"  href="' . route('admin.users.show', $user->id) . '" class="mr-1 text-white btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                <a data-content="' . __('Edit') . '" data-toggle="popover" data-trigger="hover" data-placement="top"  href="' . route('admin.users.edit', $user->id) . '" class="mr-1 btn btn-sm btn-info"><i class="fas fa-pen"></i></a>
                <form class="d-inline" method="post" action="' . route('admin.users.togglesuspend', $user->id) . '">
                             ' . csrf_field() . '
                            <button data-content="' . $suspendText . '" data-toggle="popover" data-trigger="hover" data-placement="top" class="btn btn-sm ' . $suspendColor . ' text-white mr-1"><i class="fas ' . $suspendIcon . '"></i></button>
                          </form>
                <form class="d-inline" onsubmit="return submitResult();" method="post" action="' . route('admin.users.destroy', $user->id) . '">
                             ' . csrf_field() . '
                             ' . method_field('DELETE') . '
                            <button data-content="' . __('Delete') . '" data-toggle="popover" data-trigger="hover" data-placement="top" class="mr-1 btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                ';
            })
            ->editColumn('role', function (User $user) {
                $html = '';

                foreach ($user->roles as $role) {
                    $color = preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', (string) $role->color) ? $role->color : '#6c757d';
                    $html .= "<span style='background-color: " . e($color) . "' class='badge'>" . e($role->name) . "</span>";
                }

                return $html;
            })
            ->editColumn('last_seen', function (User $user) {
                return $user->last_seen ? $user->last_seen->diffForHumans() : __('Never');
            })
            ->editColumn('name', function (User $user, PterodactylSettings $ptero_settings) {
                $panelUrl = e(rtrim($ptero_settings->panel_url, '/'));
                $pterodactylId = (int) $user->pterodactyl_id;

                return '<a class="text-info" target="_blank" href="' . $panelUrl . '/admin/users/view/' . $pterodactylId . '">' . e($user->name) . '</a>';
            })
            ->orderColumn('role', 'role_name $1')
            ->rawColumns(['avatar', 'name', 'credits', 'role', 'actions'])
            ->make();
    }

    private function sanitizeNotificationText(string $value): string
    {
        $normalized = strip_tags($value);
        return trim(preg_replace('/\s+/', ' ', $normalized) ?? '');
    }

    private function formatUserJson(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'pterodactyl_id' => $user->pterodactyl_id,
            'avatarUrl' => $user->getAvatar(),
        ];
    }

    private function getReferralsForUser(User $user): array
    {
        $referralRecords = DB::table('user_referrals')
            ->where('referral_id', $user->id)
            ->get();

        $activeReferralUsers = User::query()
            ->whereIn('id', $referralRecords->pluck('registered_user_id')->all())
            ->get()
            ->keyBy('id');

        return $referralRecords
            ->map(fn (object $referral) => $this->mapReferralRecord($referral, $activeReferralUsers))
            ->all();
    }

    private function mapReferralRecord(object $referral, $activeReferralUsers): object
    {
        if ($referral->deleted_at !== null) {
            return $this->makeDeletedReferral(
                $referral->deleted_user_id,
                $referral->deleted_username,
                $referral->created_at
            );
        }

        $activeUser = $activeReferralUsers->get($referral->registered_user_id);
        if ($activeUser instanceof User) {
            return (object) [
                'id' => $activeUser->id,
                'name' => $activeUser->name,
                'created_at' => $activeUser->created_at,
                'deleted' => false,
            ];
        }

        if ($referral->deleted_user_id) {
            return $this->makeDeletedReferral(
                $referral->deleted_user_id,
                $referral->deleted_username,
                $referral->created_at
            );
        }

        return $this->makeDeletedReferral('N/A', null, $referral->created_at, 'Unknown (deleted)');
    }

    private function makeDeletedReferral(
        mixed $id,
        ?string $deletedUsername,
        mixed $createdAt,
        string $fallbackName = 'Deleted User'
    ): object {
        $name = $deletedUsername ? $deletedUsername . ' (deleted)' : $fallbackName;

        return (object) [
            'id' => $id,
            'name' => $name,
            'created_at' => \Carbon\Carbon::parse($createdAt),
            'deleted' => true,
        ];
    }
}
