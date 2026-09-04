<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Str;

class ExtensionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $extensionsBasePath = realpath(app_path('Extensions'));
        if ($extensionsBasePath === false) {
            return;
        }

        $namespaceDirectories = glob($extensionsBasePath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [];

        foreach ($namespaceDirectories as $namespaceDirectory) {
            $namespaceName = basename($namespaceDirectory);
            $extensionDirectories = glob($namespaceDirectory . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [];

            foreach ($extensionDirectories as $extensionDirectory) {
                $extensionName = basename($extensionDirectory);

                // Load Routes
                $routesFile = $extensionDirectory . DIRECTORY_SEPARATOR . 'routes.php';
                if (is_file($routesFile)) {
                    $resolvedPath = realpath($routesFile);
                    $basePath = realpath($extensionsBasePath);
                    if ($resolvedPath && $basePath && str_starts_with($resolvedPath, $basePath . DIRECTORY_SEPARATOR)) {
                        $this->loadRoutesFrom($resolvedPath);
                    }
                }

                // Load Views
                $viewsDirectory = $extensionDirectory . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';
                if (is_dir($viewsDirectory)) {
                    $viewNamespace = Str::lower($namespaceName . '_' . $extensionName);
                    $this->loadViewsFrom($viewsDirectory, $viewNamespace);
                }

                // Load Translations
                $langDirectory = $extensionDirectory . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'lang';
                if (is_dir($langDirectory)) {
                    $this->loadJsonTranslationsFrom($langDirectory);
                    $this->loadTranslationsFrom($langDirectory, Str::lower($extensionName));
                }

                // Load Migrations
                $migrationsDirectory = $extensionDirectory . DIRECTORY_SEPARATOR . 'migrations';
                if (is_dir($migrationsDirectory)) {
                    $this->loadMigrationsFrom($migrationsDirectory);
                }

                // Load Artisan Commands
                $commandsDirectory = $extensionDirectory . DIRECTORY_SEPARATOR . 'Commands';
                if (is_dir($commandsDirectory) && $this->app->runningInConsole()) {
                    $this->loadCommandsFromDirectory($commandsDirectory, "App\\Extensions\\{$namespaceName}\\{$extensionName}\\Commands");
                }
            }
        }

        // Boot Extension Schedules
        if ($this->app->runningInConsole()) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $this->scheduleExtensions($schedule);
            });
        }
    }

    /**
     * Automatically discover and register Artisan commands from an extension's Commands directory.
     */
    protected function loadCommandsFromDirectory(string $directory, string $namespace): void
    {
        $finder = (new Finder())->in($directory)->files()->name('*.php');

        foreach ($finder as $file) {
            $class = $namespace . '\\' . str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());

            if (is_subclass_of($class, \Illuminate\Console\Command::class) && !(new \ReflectionClass($class))->isAbstract()) {
                $this->commands([$class]);
            }
        }
    }

    /**
     * Dispatch schedule registration to any extension defining a schedule() method on its main class or scheduler handler.
     */
    protected function scheduleExtensions(Schedule $schedule): void
    {
        if (class_exists(\App\Helpers\ExtensionHelper::class)) {
            $extensionClasses = \App\Helpers\ExtensionHelper::getAllExtensionClasses();
            foreach ($extensionClasses as $extensionClass) {
                if (method_exists($extensionClass, 'schedule')) {
                    $extensionClass::schedule($schedule);
                }
            }
        }
    }
}
