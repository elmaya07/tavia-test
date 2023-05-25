<?php

namespace App\Mail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Mail\Mailable;
use App\Helpers\Web;
use Illuminate\Support\Facades\DB;

class ExampleMailable extends Mailable
{   
    public $subject;
    public $toSend;
    function __construct($subject,$to){
        $this->subject = $subject;
        $this->toSend = $to;
    }
    public function build()
    {
        $getUser = DB::table('users')->where('email',$this->toSend)->first();
        return $this->view('email')->with(['param'=>url('account-activation-process',['id'=>$getUser->id,'url'=>Web::enc($this->toSend)])])
                    ->subject($this->subject)
                    ->to($this->toSend);
    }
}

class ExampleController
{
    public function sendEmail()
    {
        Mail::send(new ExampleMailable());
    }
}
