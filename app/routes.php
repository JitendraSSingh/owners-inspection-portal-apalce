<?php

	use App\Middleware\AdminAuthMiddleware;

	use App\Middleware\GuestMiddleware;

	use App\Middleware\AdminGuestMiddleware;

	use App\Middleware\AuthMiddleware;

	use App\AdminAuth\AdminAuth;

	use App\Auth\Auth;

	use Slim\Interfaces\RouterInterface;

	use Slim\Flash\Messages as Flash;

	$app->group('/', function(){
		$this->get('', ['App\Controllers\HomeController', 'index'])->setName('home');
		$this->post('', ['App\Controllers\HomeController', 'postLogin']);
		$this->post('owner/reset/password', ['App\ForgotPassword\Owner\ForgotPasswordOwner','sendPasswordLink'])->setName('owner.reset.password');
		
		$this->get('owner/reset/password/ownerid/{ownerid}/ownercode/{ownercode}/token/{token}', ['App\ForgotPassword\Owner\ForgotPasswordOwner','getPasswordSetFromLink'])->setName('owner.reset.password.ownerid.ownercode');

		$this->post('owner/reset/password/ownerid/{ownerid}/ownercode/{ownercode}/token/{token}', ['App\ForgotPassword\Owner\ForgotPasswordOwner','postPasswordSetFromLink']);

	})->add(new GuestMiddleware($container->get(RouterInterface::class), $container->get(Auth::class)));



	/*----------------------------------OWNER-------------------------------*/
	$app->get('/owner/emailaddress/{emailaddress}', ['App\Controllers\Owner\OwnerController','getOwnerIdNameByEmail'])->setName('owner.byemail');

	$app->group('/owner', function(){
		
		$this->get('/index', ['App\Controllers\Owner\OwnerController','index'])->setName('owner.index');


		$this->get('/logout',['App\Controllers\Owner\OwnerController', 'getSignOut'])->setName('owner.logout');
		
		$this->get('/property/id/{id}/propertycode/{propertycode}',['App\Controllers\Owner\OwnerController', 'getProperty'])->setName('owner.property');
		
		$this->get('/inspection/propertyid/{propertyid}/propertycode/{propertycode}/inspectionid/{inspectionid}',['App\Controllers\Owner\OwnerController','getInspection'])->setName('owner.inspection');
		
		$this->get('/inspection/file/{filename}', ['App\Controllers\Owner\OwnerController','getFile'])->setName('owner.inspection.file');	

	})->add(new AuthMiddleware($container->get(RouterInterface::class), $container->get(Auth::class), $container->get(Flash::class)));


	/*-----------------------------------------------------------------------*/
	$app->group('/login/auth/admin', function(){
		
		$this->get('',['App\Controllers\Admin\Auth\AuthController','getSignIn'])->setName('login.auth.admin');
	    
	    $this->post('',['App\Controllers\Admin\Auth\AuthController','postSignIn']);
	    
	    $this->post('/reset/password', ['App\ForgotPassword\Admin\ForgotPasswordAdmin','sendPasswordLink'])->setName('login.auth.admin.reset.password');
	    
	    $this->get('/reset/password/userid/{userid}/token/{token}',['App\ForgotPassword\Admin\ForgotPasswordAdmin','getPasswordSetFromLink'])->setName('login.auth.admin.reset.password.userid.token');
	    
	    $this->post('/reset/password/userid/{userid}/token/{token}',['App\ForgotPassword\Admin\ForgotPasswordAdmin','postPasswordSetFromLink']);

	})->add(new AdminGuestMiddleware($container->get(RouterInterface::class), $container->get(AdminAuth::class)));

	$app->get('/cronproperty', ['App\Controllers\Cron\PropertyCronController', 'index'])->setName('cronproperty');

	$app->get('/cronowner', ['App\Controllers\Cron\OwnerCronController', 'index'])->setName('cronowner');

	$app->group('/admin', function(){

	/*-------------------------------ADMIN USER-----------------------------*/
		$this->get('/user/index', ['App\Controllers\Admin\User\UserController','index'])->setName('admin.user.index');

		$this->get('/user/add', ['App\Controllers\Admin\User\UserController','getAddUser'])->setName('admin.user.add');

		$this->post('/user/add', ['App\Controllers\Admin\User\UserController','postAddUser']);

		$this->post('/user/delete/{id}', ['App\Controllers\Admin\User\UserController','postDeleteUser'])->setName('admin.user.delete');

		$this->get('/user/edit/{id}', ['App\Controllers\Admin\User\UserController','getEditUser'])->setName('admin.user.edit');

		$this->post('/user/edit/{id}', ['App\Controllers\Admin\User\UserController','postEditUser']);

		$this->get('/user/logout', ['App\Controllers\Admin\Auth\AuthController', 'getSignOut'])->setName('admin.user.logout');
	/*------------------------------------------------------------------------*/

		$this->get('/dashboard', ['App\Controllers\Admin\HomeController', 'index'])->setName('admin.dashboard');

		$this->get('/rest/property/all', ['App\Controllers\Admin\Rest\PropertyController', 'allByFullAddress'])->setName('admin.rest.property.all');

		$this->get('/rest/property/fulladdress/{fulladdress}', ['App\Controllers\Admin\Rest\PropertyController', 'getByFullAddress'])->setName('admin.rest.property.fulladdress');

		$this->get('/search/property', ['App\Controllers\Admin\Search\PropertyController', 'index'])->setName('admin.search.property');

		$this->get('/search/property/view', ['App\Controllers\Admin\Search\PropertyController', 'getByFullAddress'])->setName('admin.search.property.view');

		$this->get('/search/property/view/propertyid/{propertyid}', ['App\Controllers\Admin\Search\PropertyController', 'getByPropId'])->setName('admin.search.property.view.propertyid');


		$this->get('/properties', ['App\Controllers\Admin\Property\PropertyController', 'index'])->setName('admin.properties');

		$this->get('/landlords', ['App\Controllers\Admin\Landlord\LandlordController', 'index'])->setName('admin.landlords');

		$this->get('/landlord/ownercode/{ownercode}/viewproperties', ['App\Controllers\Admin\Landlord\LandlordController','getLandlordProperties'])->setName('admin.landlords.viewproperties');

		$this->get('/inspections', ['App\Controllers\Admin\Inspection\InspectionController', 'index'])->setName('admin.inspections');

		$this->get('/inspection/add/propertyid/[{propertyid}]', ['App\Controllers\Admin\Inspection\InspectionController', 'getAddInspection'])->setName('admin.inspections.add');

		$this->get('/file/view/{filename}', ['App\Controllers\Admin\Inspection\InspectionController', 'getFile'])->setName('admin.file.view');

        /*------------------------------Inspection Controller------------------------------*/
		$this->post('/inspections/add', ['App\Controllers\Admin\Inspection\InspectionController', 'postAddInspection'])->setName('admin.inspection.add.post');

		$this->get('/inspection/view/id/{id}/propertyid/{propertyid}', ['App\Controllers\Admin\Inspection\InspectionController', 'getViewInspection'])->setName('admin.inspections.view');

		$this->post('/inspection/delete/{id}/propertyid/{propertyid}', ['App\Controllers\Admin\Inspection\InspectionController', 'postDeleteInspection'])->setName('admin.inspection.delete');

		$this->get('/inspection/edit/id/{id}/propertyid/{propertyid}', ['App\Controllers\Admin\Inspection\InspectionController', 'getEditInspection'])->setName('admin.inspections.edit');

		$this->post('/inspection/edit/id/{id}/propertyid/{propertyid}', ['App\Controllers\Admin\Inspection\InspectionController', 'postEditInspection'])->setName('admin.inspections.edit');

		/*---------------Password Set Controller------------------------------------------------*/
		$this->get('/setpassword/id/{id}/ownercode/{ownercode}', ['App\Controllers\Admin\Auth\PasswordController', 'getPasswordSet'])->setName('admin.setpassword');

		$this->post('/setpassword/id/{id}/ownercode/{ownercode}', ['App\Controllers\Admin\Auth\PasswordController', 'postPasswordSet']);

		/*--Welcome Message Controller--*/
		$this->get('/sendwelcome/ownerid/{ownerId}/ownercode/{ownerCode}', ['App\Controllers\Admin\Message\WelcomeController','sendWelcome'])->setName('admin.sendwelcome');


		/*----------------Password Generate Link Controller-------------------------------------*/
		$this->get('/sendpasswordlink/ownerid/{ownerId}/ownercode/{ownerCode}', ['App\Controllers\Admin\Auth\PasswordController','sendPasswordLink'])->setName('admin.sendpasswordlink');
	})->add(new AdminAuthMiddleware($container->get( AdminAuth::class ), $container->get(RouterInterface::class), $container->get(Flash::class)));
	
	$app->get('/password/ownerid/{ownerid}/ownercode/{ownercode}/token/{token}',['App\Controllers\Admin\Auth\PasswordController','getPasswordSetFromLink'])->setName('passwordSetFromLink');

	$app->post('/password/ownerid/{ownerid}/ownercode/{ownercode}/token/{token}',['App\Controllers\Admin\Auth\PasswordController','postPasswordSetFromLink']);

		

	//add(new AdminAuthMiddleware($container->get( AdminAuth::class ), $container->get(RouterInterface::class)));




