<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
   public $name;
    public $email;
    public $subject;
    public $content;
   public function __construct($name,$email,$subject,$content)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;

    }


    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        return $this->from('youssefsalahcs@gmail.com')->view('emails.notify-mail');
    }
}
