<?php

namespace App\Mail\Mailer;

use Swift_Mailer;

use Swift_Message;

use Slim\Views\Twig;

class Mailer{

    protected $swift;

    protected $twig;

    protected $from = [];

    public function __construct(Swift_Mailer $swift, Twig $twig){
        $this->swift = $swift;
        $this->twig = $twig;
    }

    public function alwaysFrom($address, $name = null){
        $this->from = compact('address','name');
        return $this;
    }

    public function send($twigView, $data, Callable $callback = null){

        $message = $this->buildMessage();

        call_user_func($callback, $message);

        $message->body($this->parseView($twigView, $data));

        $this->swift->send($message->getSwiftMessage());
    }

    protected function buildMessage(){
        return (new MessageBuilder(new Swift_Message))
        ->from($this->from['address'], $this->from['name']);
    }

    protected function parseView($twigView, $data){
        return $this->twig->fetch($twigView,$data);
    }
}