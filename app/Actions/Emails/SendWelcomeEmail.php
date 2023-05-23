<?php

namespace App\Actions;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    public function __invoke(User $user)
    {
        return Mail::to($user->email)->queue(new WelcomeMail());
    }
}
