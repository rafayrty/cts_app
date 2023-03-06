<?php

namespace App\Actions;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SendPhoneForgot
{
    public function __invoke(User $user, $token)
    {
        $receiverNumber = $user->phone_number;
        $message = 'Your Forgot Pass Code For Basmti: '.$token;

        try {
            $account_sid = getenv('TWILIO_SID');
            $auth_token = getenv('TWILIO_TOKEN');
            $twilio_number = getenv('TWILIO_FROM');

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'body' => $message]);

            return true;
        } catch (Exception $e) {
            return false;
            Log::error($e);
        }
    }
}
