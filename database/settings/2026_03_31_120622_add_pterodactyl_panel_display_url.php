<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('pterodactyl.panel_display_url', '');
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('pterodactyl.panel_display_url');
    }
};
