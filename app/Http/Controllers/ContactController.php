<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display the contact form.
     */
    public function show()
    {
        return view('contact');
    }

    /**
     * Process contact form submission and send email.
     */
    public function send(ContactRequest $request)
    {
        try {
            // Send email to support
            Mail::send('emails.contact-message', [
                'contactName' => $request->name,
                'contactEmail' => $request->email,
                'contactSubject' => $request->subject,
                'contactMessage' => $request->message,
            ], function ($message) use ($request) {
                $message->to('support@dreambet.ht')
                    ->subject('Contact Form: ' . $request->subject)
                    ->replyTo($request->email, $request->name);
            });

            return redirect()->route('contact')->with('success', __('Thank you for contacting us! We will get back to you soon.'));
        } catch (\Exception $e) {
            return redirect()->route('contact')->with('error', __('Sorry, there was an error sending your message. Please try again later.'));
        }
    }
}
