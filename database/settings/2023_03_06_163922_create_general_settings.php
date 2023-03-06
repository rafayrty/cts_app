<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.promotion_text', '');
        $this->migrator->add('general.promotion_link', '');
        $this->migrator->add('general.about', '');
        $this->migrator->add('general.phone', '');
        $this->migrator->add('general.email', '');
        $this->migrator->add('general.address', '');
        $this->migrator->add('general.social_medias', '');
    }
};
