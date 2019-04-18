<?php

use Interop\Container\ContainerInterface;

use GuzzleHttp\Client;

use Slim\Interfaces\RouterInterface;

use Slim\Csrf\Guard;

use Slim\Views\Twig;

use Slim\Flash\Messages as Flash;

use Slim\Views\TwigExtension;

use App\Extension\YoutubeEmbedExtension;

use App\Validation\Validator;

use App\Support\Xml\Contracts\OwnerPopulateInterface;

use App\Support\Xml\OwnerPopulate;

use App\Support\Xml\Contracts\PropertyPopulateInterface;

use App\Support\Xml\PropertyPopulate;

use App\Support\Rest\Contracts\ApiInterface;

use App\Support\Rest\PalaceApi;

use App\Support\FileHandling\Contracts\FileHandlingInterface;

use App\Support\FileHandling\FileHandling;

use App\View\Factory;

use App\Support\Purifier\Purify;

use App\Mail\Mailer\Mailer;

use App\AdminAuth\AdminAuth;

use App\Auth\Auth;

use App\Helper\Uploader;


return [

	RouterInterface::class => function(ContainerInterface $c){

		return $c->get('router');

	},

	Purify::class => function(ContainerInterface $c){

		return new Purify;

	},

	Validator::class => function(ContainerInterface $c){

		return new Validator;

	},

	FileHandlingInterface::class => function(ContainerInterface $c){

		return new FileHandling;

	},

	Uploader::class => function(ContainerInterface $c){

		return new Uploader($c->get(FileHandlingInterface::class));
	
	},

	OwnerPopulateInterface::class => function (ContainerInterface $c){

		return new OwnerPopulate;

	},

	PropertyPopulateInterface::class => function (ContainerInterface $c){

		return new PropertyPopulate;

	},

	ApiInterface::class => function (ContainerInterface $c){

		$client = new Client([

			'base_uri' => 'https://serviceapi.realbaselive.com/',

			'timeout'  => 6.0

		]);

		return new PalaceApi($client, 'tommyspm@tps.co.nz', 'TMYtpsjdhKJG8skj98664');

	},


	Flash::class => function(ContainerInterface $c){

		return new Flash;

	},

	AdminAuth::class => function(ContainerInterface $c){
		return new AdminAuth;
	},

	Auth::class => function(ContainerInterface $c){
		return new Auth;
	},

	Twig::class => function(ContainerInterface $c){

        $twig = Factory::getEngine($c);


        $twig->addExtension(new TwigExtension(

            $c->get('router'),

            $c->get('request')->getUri()

        ));

        $twig->addExtension( new YoutubeEmbedExtension );

        $twig->getEnvironment()->addGlobal('auth', [
        	'check' => $c->get(AdminAuth::class)->check(),
        	'user' => $c->get(AdminAuth::class)->user()
        ]);

        $twig->getEnvironment()->addGlobal('ownerauth',[
        	'check' => $c->get(Auth::class)->check(),
        	'owner' => $c->get(Auth::class)->owner()
        ]);

        $twig->getEnvironment()->addGlobal('flash', $c->get(Flash::class));

        return $twig;

	},

	Guard::class => function(ContainerInterface $c){

		return new Guard;

	},

	Mailer::class => function(ContainerInterface $c){

		// Create the Transport
		$transport = (new Swift_SmtpTransport($c->get('mail.host'), $c->get('mail.port')))
					->setUserName($c->get('mail.username'))
					->setPassword($c->get('mail.password'));

		// Create the Mailer using your created Transport
        $swift = new Swift_Mailer($transport);

		return (new Mailer($swift, $c->get(Twig::class)))->alwaysFrom($c->get('mail.from')['address'], $c->get('mail.from')['name']);

	},

	
];