<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SecurityStaticGuardsTest extends TestCase
{
    public function test_controllers_do_not_use_debug_terminators(): void
    {
        $controllerFiles = File::allFiles(app_path('Http/Controllers'));

        foreach ($controllerFiles as $file) {
            $contents = File::get($file->getRealPath());

            $this->assertDoesNotMatchRegularExpression(
                '/\b(dd|dump)\s*\(/',
                $contents,
                sprintf('Debug helper found in %s', $file->getRelativePathname())
            );
        }
    }

    public function test_profile_view_does_not_reference_discord_client_secret(): void
    {
        $profileView = File::get(base_path('themes/default/views/profile/index.blade.php'));

        $this->assertStringNotContainsString('discord_client_secret', $profileView);
    }

    public function test_admin_datatables_do_not_use_direct_unescaped_dynamic_fields(): void
    {
        $controllerPaths = [
            app_path('Http/Controllers/Admin/TicketsController.php'),
            app_path('Http/Controllers/Admin/PaymentController.php'),
            app_path('Http/Controllers/Admin/PartnerController.php'),
            app_path('Http/Controllers/Admin/VoucherController.php'),
            app_path('Http/Controllers/Admin/UserController.php'),
            app_path('Http/Controllers/Admin/UsefulLinkController.php'),
        ];

        $dangerousDynamicPattern = '/\.\s*\$[A-Za-z_]\w*->(?:name|reason|title|description|icon|color)\s*\./';

        foreach ($controllerPaths as $path) {
            $contents = File::get($path);

            $this->assertDoesNotMatchRegularExpression(
                $dangerousDynamicPattern,
                $contents,
                sprintf('Potential unescaped dynamic datatable field in %s', basename($path))
            );
        }
    }

    public function test_admin_route_group_has_defense_in_depth_middleware(): void
    {
        $routes = File::get(base_path('routes/web.php'));

        $this->assertStringContainsString(
            "Route::prefix('admin')->name('admin.')->middleware('admin.access')->group(function () {",
            $routes
        );
    }

    public function test_socialite_callback_does_not_mass_assign_raw_discord_payload(): void
    {
        $controller = File::get(app_path('Http/Controllers/Auth/SocialiteController.php'));

        $this->assertStringNotContainsString('array_merge($discord->user', $controller);
        $this->assertStringNotContainsString('->update($discord->user)', $controller);
    }
}
