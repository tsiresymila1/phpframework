<?php

namespace Core\Http\Security;

use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Core\Http\Security\UserAuthenticatorInterface;
use Core\Session\Session;
use Core\Utils\Encryption;

class UserAuthenticator implements UserAuthenticatorInterface
{

    protected  $model;
    protected $username;
    protected $password;
    protected $url;
    protected $authenticator;
    protected $config;
    protected $roles = [];
    protected $rememberme = true;
    public $login = "/login";

    public function __construct()
    {
        $security = require APP_PATH . 'config/security.php';
        $this->authenticator = $security['authenticator'];
        $this->username = $security['config']['username'];
        $this->password = $security['config']['password'];
        $this->model = new $security['model']();
        $this->url = $security['url'];
        $this->config = $security['config'];
        $roles =  $this->config['roles'];
        if (gettype($roles) == "array") {
            $this->roles = $roles;
        } else {
            $this->roles = [$roles];
        }
    }


    protected function isAuthUser($username, $password)
    {
        $encryption = new Encryption();
        $passwordcrypted = $encryption->encode($password);
        $params = array($this->username => $username, $this->password => $passwordcrypted);
        $user = $this->model->findOneBy($params);
        if ($user && count(array_intersect($this->roles, $user->getRoles())) > 0) {
            if ($this->rememberme) {
                Session::set($this->username, $username);
                Session::set($this->password, $passwordcrypted);
            }
            return true;
        } else {
            return false;
        }
    }

    protected function isAuthSessionUser($username, $password)
    {
        $params = array($this->username => $username, $this->password => $password);
        $user = $this->model->findOneBy($params);
        if ($user && count(array_intersect($this->roles, $user->getRoles())) > 0) {
            if ($this->rememberme) {
                Session::set($this->username, $username);
                Session::set($this->password, $password);
            }
            return true;
        } else {
            return false;
        }
    }

    public function isVerifyPost()
    {
        $data = Request::post();
        if (isset($data[$this->username], $data[$this->password])) {
            $usernamep = $data[$this->username];
            $passwordp = $data[$this->password];
            return $this->isAuthUser($usernamep, $passwordp);
        } else {
            return false;
        }
    }

    public function IsVerifySession()
    {
        $usernamep = Session::Get($this->username);
        $passwordp = Session::Get($this->password);
        return $this->isAuthSessionUser($usernamep, $passwordp);
    }

    public function onAuthenticateSuccess()
    {
        
        $this->pass();
    }

    public function onAuthenticateFail()
    {
        return Response::Send('Not authentified');
    }

    public function pass()
    {
        Router::$isFound = false;
        $controller = Router::find();
        if (!Router::$isFound) {
            return $controller->errorUrlNotFound();
        }
    }

    public function authenticate()
    {
        $path = rtrim(Request::getPath(), '/') . '/';
        if (isset($this->url) && isset($this->authenticator) && isset($this->model) && isset($this->config)) {
            if(preg_match("#^" . $this->login.'/$#', $path)){
                if (Request::isPost()) {
                    if ($this->isverifyPost()) {
                        $ins = Request::getInstance();
                        $ins->Set('auth',true);
                        $this->onAuthenticateSuccess();
                    } else {
                        $this->onAuthenticateFail();
                    }
                }
                else{
                    $this->onAuthenticateSuccess();
                }
            }
            else{
                if (preg_match("#^" . $this->url . "/#", $path) === 1) {
                    // $_SESSION['token'] = "hello";
                    if ($this->IsverifySession()) {
                        $ins = Request::getInstance();
                        $ins->Set('auth',true);
                        $this->onAuthenticateSuccess();
                    } else {
                        $this->onAuthenticateFail();
                    }
                } else {
                    $this->onAuthenticateSuccess();
                }
            }
           
        } else {
            $this->onAuthenticateSuccess();
        }
    }

    public function eraseCredentials()
    {
        Session::remove($this->username);
        Session::remove($this->password);
    }

    public function notAuthentificated()
    {
        Response::Send("Not authentificated");
    }
}
