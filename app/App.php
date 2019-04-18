<?php

namespace App;

use DI\ContainerBuilder;

use DI\Bridge\Slim\App as DiBridge;

use App\View\Factory;

use Illuminate\Pagination\AbstractPaginator;

use Illuminate\Pagination\Paginator;

AbstractPaginator::viewFactoryResolver(function(){

    return new Factory;

});

AbstractPaginator::defaultView('pagination/bootstrap.twig');

Paginator::currentPathResolver( function(){

    return isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '?') : "/";

});

Paginator::currentPageResolver( function(){

    return isset($_GET['page']) ? $_GET['page'] : 1;

});

class App extends DIBridge{

	protected function configureContainer(ContainerBuilder $builder){

		$builder->addDefinitions(
			[
				'settings.displayErrorDetails' => false,
                'db' => [
                    'name' => getenv('DB'),
                    'host' => getenv('DB_HOST'),
                    'username' => getenv('DB_USERNAME'),
                    'password' => getenv('DB_PASSWORD')
                ],
                'mail.host' => getenv('MAIL_HOST'),
                'mail.port' => getenv('MAIL_PORT'),
                'mail.from' => [
                    'name' => getenv('MAIL_FROM_NAME'),
                    'address' => getenv('MAIL_FROM_ADDRESS')
                ],
                'mail.username' => getenv('MAIL_USERNAME'),
                'mail.password' => getenv('MAIL_PASSWORD'),
			]
		);

		$builder->addDefinitions(__DIR__.'/container.php');
	}
}