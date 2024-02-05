<?php

declare(strict_types=1);

namespace App\Services;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

use function PHPUnit\Framework\throwException;

class MailChimpService
{

    /**
     * Authenticate a User For Frontend Dashboard
     *
     * @param  \App\Models\User $user
     * @return array <string,mixed>
     */
    public function add_to_user_audience(User $user){

        $mailchimp = new \MailchimpMarketing\ApiClient();

        $mailchimp->setConfig([
            'apiKey' => config('app.mailchimp_key'),
            'server' => config('app.mailchimp_server_prefix'),
        ]);

        $list_id = "305910516b";

        try {
            $response = $mailchimp->lists->addListMember($list_id, [
                "email_address" => $user->email,
                "status" => "subscribed",
                "tags"  => ['Customer'],
                "merge_fields" => [
                    "FNAME" => $user->first_name,
                    "LNAME" => $user->last_name,
                    "PHONE" => $user->phone_number
                ]
            ]);
            return $response;
        } catch (MailchimpMarketing\ApiException $e) {
            Log::error($e->getMessage());
            User::findOrFail($user->id)->delete();
            abort(422,"An Unknown Error Occurred");
        }
    }

    /**
     * Authenticate a User For Frontend Dashboard
     *
     * @param  \App\Models\User $user
     * @return array <string,mixed>
     */
    public function add_to_customer_w_order(User $user){

        $mailchimp = new \MailchimpMarketing\ApiClient();

        $mailchimp->setConfig([
            'apiKey' => config('app.mailchimp_key'),
            'server' => config('app.mailchimp_server_prefix'),
        ]);

        $list_id = "305910516b";
        $email =  $user->email;
        $subscriber_hash = md5(strtolower($email));
        try {
          $mailchimp->lists->updateListMemberTags($list_id, $subscriber_hash, [
            "tags" => [
              [
                "name" => "Customer W Order",
                "status" => "active"
              ]
            ]
          ]);
        } catch (MailchimpMarketing\ApiException $e) {
            Log::error($e->getMessage());
        }
    }
}
