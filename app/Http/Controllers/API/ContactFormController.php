<?php

namespace App\Http\Controllers\Api;
use App\Mail\NotifyMail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;

class ContactFormController extends Controller
{
    public function ContactForm(Request $request) {

        // Form validation
         $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'subject'=>'required',
            'message' => 'required'
         ]);
         if ($validator->fails()) {
     return response()->json(['error'=>$validator->errors()], 401);
 }
        //  Store data in database
        Contact::create($request->all());

        //  Send mail to Application Admin
        /*\Mail::send('contact_email', array(
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'subject' => $request->get('subject'),
            'bodyMessage' => $request->get('message'),
        ), function($message) use ($request){
            $message->from($request->email);
            Mail::to('youssefsalahcs@gmail.com')->send(new NotifyMail($request->get('name'),$request->get('email'),$request->get('subject'),$request->get('message')));
            $message->to('youssefsalahcs@gmail.com', 'Youssef')->subject($request->get('subject'));
        });*/
        //dd($request->get('name'),$request->get('email'),$request->get('subject'),$request->get('message'));
        //$request->get('name'),$request->get('email'),$request->get('subject'),$request->get('message')
        Mail::to('youssefsalahcs@gmail.com')->send(new NotifyMail($request->get('name'),$request->get('email'),$request->get('subject'),$request->get('message')));

        return response()->json(['success' => 'The email has been sent.']);
    }
}
