<?php

namespace ttm4135\webapp\controllers;
use ttm4135\webapp\Auth;
use ttm4135\webapp\models\User;

class LoginController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if (Auth::check()) {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        } else {
            $hasLog = !empty($_COOKIE['easylog']);
            $cookieName = "";
            if($hasLog){
                $cookieName = $_COOKIE['easylog'];
            }
            $this->render('login.twig', ['title'=>"Login", 'inputUsername'=>$cookieName]);
        }
    }

    function login()
    {
        $request = $this->app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        if ( Auth::checkCredentials($username, $password) ) {
            // Regenerate session id
            session_regenerate_id(true);

            $user = User::findByUser($username);
            $_SESSION['userid'] = $user->getId();

            // Generate random code for the authentication cookie and store it in the session
            $authCode = md5(uniqid(mt_rand(), true));
            $_SESSION['authentication'] = $authCode;


            // Create cookie that stores username, and restrict to https pages
            setcookie('easylog', $username, 0, '/', '', true, true);

            // Create authentication cookie, and restrict to https pages
            setcookie('authentication', $authCode, 0, '/', '', true, true);

            $this->app->flash('info', "You are now successfully logged in as " . $user->getUsername() . ".");
            $this->app->redirect('/');
        } else {
            $this->app->flashNow('error', 'Incorrect username/password combination.');
            $this->render('login.twig', []);
        }
    }

    function logout()
    {   
        Auth::logout();
        $this->app->flashNow('info', 'Logged out successfully!!');
        $this->render('base.twig', []);
        return;
       
    }
}
