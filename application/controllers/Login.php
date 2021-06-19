<?php

namespace app\application\controllers;

use app\core\Config;
use app\core\Controller;
use app\core\Encryption;
use app\core\Session;

class Login extends Controller{

    public function initialize()
    {
        $this->loadComponents([
            'Auth',
            'Security'
        ]);

        $this->loadModel('loginModel');
    }

    public function beforeAction()
    {
        parent::beforeAction();
        Config::setJsConfig('curPage','login');

        $action = $this->request->param('action');
        $actions = ['login', 'forgotPassword', 'updatePassword'];
        $this->Security->requirePost($actions);
        $this->Security->requireGet(['index','verifyUser', 'resetPassword','logout']);

        switch($action){
            case "login":
                $this->Security->config("form", ['fields' => ['username', 'password'],  'exclude' =>['remember_me','redirect']]);
                break;
            case "forgotPassword":
                $this->Security->config("form", ['fields' => ['email', 'password'],]);
                break;
            case "updatePassword":
                $this->Security->config("form", ['fields'=>['password', 'confirm_password', 'id', 'token']]);
                break;
        }       
     }

    public function index()
    {
        //check first if user is already logged in via session of cookie
        if($this->Auth->isLoggedIn()){
            return $this->redirector->to(PUBLIC_ROOT.'dashboard');
        }else{            
            // Clearing the sesion won't allow user(un-trusted) to open more than one login form,
            // as every time the page loads, it generates a new CSRF Token.
            // Destroying the sesion won't allow accessing sesssion data (i.e. $_SESSION["csrf_token"]).

            //get redirect url if any
            $redirect = $this->request->query('redirect');

           return $this->view->render(Config::get('VIEWS_PATH').'login', ['redirect'=>$redirect]);
        }
    }

    /**
     * verify user token
     * this token was sent by email as soon as user creates a new account
     * it will expire after 24 hour
     */
    public function verifyUser()
    {
        $userId = $this->request->query('id');
        $userId = empty($userId) ? null : Encryption::decryptId($this->request->query('id'));
        $token = $this->request->query('token');

        $result = $this->loginModel->isEmailVerificationTokenValid($userId, $token);

        if(!$result){
            return $this->error(404);
        }else{
            $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "login/", Config::get('LOGIN_PATH') . 'userVerified.php');
        }
    }


    /**
     * login
     */
    public function login()
    {
        $username = $this->request->data("username");
        $password = $this->request->data("password");
        $rememberMe = $this->request->data('remember_me');
        $redirect = $this->request->data('redirect');


        $result = $this->loginModel->doLogin($username, $password, $rememberMe, $this->request->clientIp(), $this->request->userAgent());

        if(!$result){

            Session::setFlashData('login-failed', $this->loginModel->errors());
            return $this->redirector->to(PUBLIC_ROOT.'login');
        }else{
            Session::setFlashData('welcome', 'Welcome to Cafe Management System');
            //check if redirect url exists, then construct full url
            if(!empty($redirect))
            {
                $redirect = $this->request->getProtocolAndHost().$redirect;
                return $this->redirector->to($redirect);
            }
            return $this->redirector->root();
        }
    }

    /**
     * if user forgot his password,
     * then we will send him an eamil with token
     * (expiered after 24 hours)
     */
    public function forgotPassword()
    {
        $email = $this->request->data("email");
        $result = $this->loginModel->forgotPassword($email);

        Session::set('disaply-form', 'forgot-password');

        if(!$result){
            Session::set('forgot-password-erros', $this->loginModel->errors());
        }else{
            Session::set('fogot-password-success', "Email has been sent to you. Please check your email");
        }

        return $this->redirector->to(PUBLIC_ROOT.'/login');
    }

    /**
     * update user's password after reset password request
     *
     */
    public function updatePassword(){

        $password        = $this->request->data("password");
        $confirmPassword = $this->request->data("confirm_password");
        $userId          = Session::get("user_id_reset_password");

        $result =  $this->login->updatePassword($userId, $password, $confirmPassword);

        if(!$result){

            Session::set('update-password-errors', $this->login->errors());
            return $this->redirector->to(PUBLIC_ROOT . "Login/resetPassword", ['id' => $this->request->data("id"), 'token' => $this->request->data("token")]);

        } else {

            // logout, and clear any existing session and cookies
            $this->login->logOut(Session::getUserId());

            $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "login/", Config::get('LOGIN_PATH') . 'passwordUpdated.php');
        }
    }


    /**
     * If password token valid, then show update password form
     *
     */
    public function resetPassword()
    {
        $userId = $this->request->query('id');
        $userId = empty($userId) ? null : Encryption::decryptId($userId);
        $token = $this->request->query('token');

        $result = $this->loginModel->isForgotPasswordTokenValid($userId, $token);

        if(!$result){

            return $this->error(404);

        }else{

             // If there is a user already logged in, then log out.
            // It not necessary for the logged in user to be the same as user_id in the requested reset password URL.

            // But, this won't allow user to open more than one update password form,
            // because every time it loads, it generates a new CSRF Token
            // So, keep it commented
            // $this->loginModel->logOut(Session::getUserId(), true);

            // don't store the user id in a hidden field in the update password form,
            // because user can easily open inspector and change it,
            // so you will ending up using updatePassword() on an invalid user id.
            Session::set("user_id_reset_password", $userId);

            $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/login/", Config::get('LOGIN_PATH') . 'updatePassword.php');
        }
    }

    public function isAuthorized(){return true;}

    /**
     * logout
     */
    public function logout()
    {
        $this->loginModel->logout(Session::getUserId());
        return $this->redirector->to(PUBLIC_ROOT.'login');
    }
 }