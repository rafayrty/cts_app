<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $promotion_text;

    public string $promotion_link;

    public string $phone;

    public string $email;

    public string $address;

    public string $about;

    public array | string $social_medias;

    public static function group(): string
    {
        return 'general';
    }
}
