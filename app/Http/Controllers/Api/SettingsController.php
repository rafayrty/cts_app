<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'promotion_text' => app(GeneralSettings::class)->promotion_text,
            'promotion_link' => app(GeneralSettings::class)->promotion_link,
            'about' => app(GeneralSettings::class)->about,
            'phone' => app(GeneralSettings::class)->phone,
            'address' => app(GeneralSettings::class)->address,
            'email' => app(GeneralSettings::class)->email,
            'social_medias' => app(GeneralSettings::class)->social_medias,
            'faqs' => app(GeneralSettings::class)->faqs,
        ];

        return $settings;
    }
}
