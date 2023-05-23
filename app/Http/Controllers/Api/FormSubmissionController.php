<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormSubmissions;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{
    public function basmti_request(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'num_of_stories' => 'required',
            'notes' => 'required',
        ]);
        $form = FormSubmissions::create(['form' => 'request', 'content' => $request->all()]);

        return $form;
    }

    public function basmti_contact(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required',
        ]);
        $form = FormSubmissions::create(['form' => 'contact', 'content' => $request->all()]);

        return $form;
    }
}
