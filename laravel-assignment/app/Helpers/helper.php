<?php
use Illuminate\Support\Facades\Auth;

  function emailSend($to, $name)
  {
    return Mail::send([], [], function ($message) {
      $message->to(auth()->user()->email)
        ->subject('New Post Created')
        ->setBody('Hi '.auth()->user()->name.', Congratulation!!! You have created a new post successfully.');
    });
  }

?>