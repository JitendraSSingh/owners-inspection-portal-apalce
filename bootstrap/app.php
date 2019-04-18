<?php

	require __DIR__ . '/../vendor/autoload.php';

	session_start();

	use Respect\Validation\Validator as v;

	try {
		(new Dotenv\Dotenv(__DIR__.'/../'))->load();
	} catch (Dotenv\Exception\InvalidPathException $e) {
		echo "Not Found";
		die();
	}

	use App\App;

	use Illuminate\Database\Capsule\Manager as Capsule;

	use Illuminate\Pagination\LengthAwarePaginator;

	use Slim\Views\Twig;

	use Slim\Csrf\Guard;

	use App\Middleware\CsrfViewMiddleware;

	use App\Middleware\ValidationErrorsMiddleware;

	use App\Middleware\OldInputMiddleware;

	use App\View\Factory;

	$config = [];

	$config['displayErrorDetails'] = false;

	$app = new App(['settings' => $config]);

	$container = $app->getContainer();

	$capsule = new Capsule;

	$capsule->addConnection([

		'driver' => 'mysql',

		'host' => $container->get('db')['host'],

		'database' => $container->get('db')['name'],

		'username' => $container->get('db')['username'],

		'password' => $container->get('db')['password'],

		'charset' => 'utf8',

		'collation' => 'utf8_unicode_ci',

		'prefix' => ''
	]);

	$capsule->setAsGlobal();

	$capsule->bootEloquent();

	$app->add( new CsrfViewMiddleware( $container->get( Twig::class ), $container->get( Guard::class ) ) );

	$app->add(new ValidationErrorsMiddleware( $container->get( Twig::class ) ) );

	$app->add(new OldInputMiddleware( $container->get( Twig::class ) ) );

	$app->add($container->get( Guard::class ));

	LengthAwarePaginator::viewFactoryResolver( function () {

		return new Factory;

	});

	v::with('App\\Validation\\Rules\\');


	require __DIR__ . '/../app/routes.php';
