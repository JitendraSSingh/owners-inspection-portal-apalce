<?php

namespace App\View;

class Factory{

    protected $view;

    public static function getEngine(){
        $view = new \Slim\Views\Twig(__DIR__ . '/../../resources/views', [
                    'cache' => false,
        ]);
        return $view;
    }

    public function make( $view, $data = [] ){

        $this->view = static::getEngine()->fetch($view, $data);

        return $this;

    }

    public function render(){

        return $this->view;

    }

}