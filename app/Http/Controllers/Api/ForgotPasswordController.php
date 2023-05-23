<?php

namespace App\Http\Controllers\Api;

use App\Actions\SendPhoneForgot;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function __construct(SendPhoneForgot $sendPhoneForgot)
    {
        $this->sendPhoneForgot = $sendPhoneForgot;
    }

    public function forgot_password(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required',
            'country_code' => 'required',
        ]);

        $user_phone = User::where('phone', $request->phone)->where('country_code', $request->country_code)->get()->first();

        if ($user_phone) {
            $this->sendForgotPhone($user_phone);

            return true;
        }
        abort(404, 'No Account was Found');
    }

    //public function forgot_password(Request $request)
    //{
        //$this->validate($request, [
            //'email' => 'required',
        //]);

        //$user_email = User::where('email', $request->email)->get()->first();

        //if ($user_email) {
            //$this->sendForgotEmail($user_email);

            //return true;
        //}

        //abort(404, 'No Account was Found');
    //}

    //public function sendForgotEmail(User $user)
    //{
        //$forgot_code = rand(1000, 9999);
        //DB::table('forgot_passwords')->insert(['user_id' => $user->id, 'type' => 2, 'forgot_code' => $forgot_code, 'created_at' => now(), 'expiry' => now()->addMinutes(10)]);
        //Mail::to($user->email)->queue(new ForgotPassword($user, $forgot_code));

        //return true;
    //}

    public function sendForgotPhone(User $user)
    {
        $forgot_code = rand(1000, 9999);
        DB::table('forgot_passwords')->insert(['user_id' => $user->id, 'type' => 1, 'forgot_code' => $forgot_code, 'created_at' => now(), 'expiry' => now()->addMinutes(10)]);
        ($this->sendPhoneForgot)($user, $forgot_code);

        return true;
    }

    //public function reset_password(Request $request)
    //{
        //$this->validate($request, [
            //'email' => 'required|min:2|max:64',
            //'forgot_code' => 'required',
            //'password' => 'required|min:6|max:64',
        //]);

        //$user_id = null;

        //$user_email = User::where('email', $request->email)->get()->first();
        //if ($user_email) {
            //$user_id = $user_email->id;
        //}

        //if ($user_id == null) {
            //abort(404, 'Code Entered is Incorrect or Expired');
        //}

        //$forgot_pass = DB::table('forgot_passwords')->where('user_id', $user_id)->where('forgot_code', $request->forgot_code)->get()->first();
        //DB::table('forgot_passwords')->where('user_id', $user_id)->delete();

        //if (! $forgot_pass) {
            //abort(404, 'Code Entered is Incorrect or Expired');
        //}

        //return User::findOrFail($user_id)->update(['password' => Hash::make($request->password)]);
    //}

    public function reset_password(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|min:2|max:64',
            'country_code' => 'required|min:2|max:64',
            'forgot_code' => 'required',
            'password' => 'required|min:6|max:64',
        ]);

        $user_id = null;

        $user_phone = User::where('phone', $request->phone)->where('country_code', $request->country_code)->get()->first();
        if ($user_phone) {
            $user_id = $user_phone->id;
        }
        if ($user_id == null) {
            abort(404, 'Code Entered is Incorrect or Expired');
        }

        $forgot_pass = DB::table('forgot_passwords')->where('user_id', $user_id)->where('forgot_code', $request->forgot_code)->get()->first();
        DB::table('forgot_passwords')->where('user_id', $user_id)->delete();
        //$forgot_pass = DB::table('forgot_passwords')->where('user_id', $user_id)->where('forgot_code', $request->forgot_code)->where('expiry', '>', now())->get()->first();

        if (! $forgot_pass) {
            abort(404, 'Code Entered is Incorrect or Expired');
        }

        return User::findOrFail($user_id)->update(['password' => Hash::make($request->password)]);
    }
}