<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('sites.site_name', '');
        $this->migrator->add('sites.site_description', '');
        $this->migrator->add('sites.site_keywords', '');
        $this->migrator->add('sites.site_profile', '');
        $this->migrator->add('sites.site_logo', '');
        $this->migrator->add('sites.site_author', '');
        $this->migrator->add('sites.site_email', '');
        $this->migrator->add('sites.site_phone', '');
        $this->migrator->add('sites.site_social', []);
    }
};
