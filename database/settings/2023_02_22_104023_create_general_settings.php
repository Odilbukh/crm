<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'CRM');
        $this->migrator->add('general.site_active', true);
        $this->migrator->add('general.site_currency', 'usd');
        $this->migrator->add('general.site_country', 'us');
    }
}
