<?php

namespace App\Controllers\Admin\User;

use Slim\Views\Twig;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Flash\Messages as Flash;

use Slim\Interfaces\RouterInterface as Router;

use App\Validation\Validator;

use Respect\Validation\Validator as v;

use App\Models\User;

class UserController{
    protected $validator;
    protected $router;
    public function __construct(Validator $validator, Router $router, Flash $flash){
        $this->validator = $validator;
        $this->router = $router;
        $this->flash = $flash;
    }

    public function getAddUser(Response $response, Twig $view){
        return $view->render($response, 'admin/user/adduser.twig');
    }
    public function postAddUser(Request $request, Response $response){
        $this->validator->validate($request, [
            'username' => v::notEmpty(),
            'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'password' => v::noWhitespace()->notEmpty(),
            // 'confirm_password' => v::noWhitespace()->notEmpty()->confirmPassword($request->getParam('password'))
        ]);
        if ($this->validator->hasFailed()) {
            return $response->withRedirect($this->router->pathFor('admin.user.add'));
        }
        User::create([
            'email'=> $request->getParam('email'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
            'name' => $request->getParam('username'),
            'is_admin' => 1
        ]);
        return $response->withRedirect($this->router->pathFor('admin.user.index'));
    }
    public function getEditUser($id,Response $response, Twig $view){
        $user = User::find($id);
        return $view->render($response, 'admin/user/edituser.twig',compact('user'));
    }
    public function postEditUser($id, Request $request, Response $response){
        $this->validator->validate($request, [
            'username' => v::notEmpty(),
            'email' => v::noWhitespace()->notEmpty()->email()->emailAvailableEdit($request->getParam('old_email')),
            'password' => v::noWhitespace()->notEmpty(),
            // 'confirm_password' => v::noWhitespace()->notEmpty()->confirmPassword($request->getParam('password'))
        ]);
        if ($this->validator->hasFailed()) {
            return $response->withRedirect($this->router->pathFor('admin.user.edit',['id' => $id]));
        }
        User::where('id',$id)->update([
            'email'=> $request->getParam('email'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
            'name' => $request->getParam('username'),
            'is_admin' => 1
        ]);
        return $response->withRedirect($this->router->pathFor('admin.user.index'));
    }
    public function index(Response $response, Twig $view){
        $users = User::all();

        return $view->render($response,'admin/user/index.twig',compact('users'));
    }

    public function postDeleteUser($id, Request $request, Response $response){
        User::find($id)->delete();
        return $response->withRedirect($this->router->pathFor('admin.user.index'));
    }
}