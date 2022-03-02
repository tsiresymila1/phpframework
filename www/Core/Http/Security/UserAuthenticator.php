<?php

namespace Core\Http\Security;

use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Core\Http\Security\UserAuthenticatorInterface;
use Core\Http\CoreControllers\Controller as CoreController;
use Core\Http\Handler;
use Core\Session\Session;
use Core\Utils\Encryption;
use Core\Utils\JWT;
use Exception;

class UserAuthenticator implements UserAuthenticatorInterface
{

    protected  $model;
    protected $username;
    protected $password;
    protected $urls;
    protected $authenticator;
    protected $config;
    protected $roles = [];
    protected $rememberme = true;
    public $login = "/login";
    public $logout = "/logout";
    private $content;

    public function __construct()
    {
        $security = require APP_PATH . 'config/security.php';
        $this->authenticator = $security['authenticator'];
        $this->username = $security['config']['username'];
        $this->password = $security['config']['password'];
        $this->model = new $security['model']();
        if (gettype($security['url']) !== "array") {
            $this->urls = [$security['url']];
        } else {
            $this->urls = $security['url'];
        }
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
            return $user;
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
        $data = Request::Post();
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

    public function IsverifyToken()
    {
        $jwt = new JWT(SECRET);
        $token = Request::GetToken();
        return $jwt->verify($token);
    }

    public function pass()
    {
        Handler::DoRouting();
    }

    public function verifyPost($successcallback, $errorcalback)
    {
        if (Request::isPost()) {
            $data = $this->isverifyPost();
            if ($data) {
                $ins = Request::instance();
                $ins->Set('auth', true);
                $successcallback($data);
            } else {
                $errorcalback();
            }
        } else {
            $this->pass();
        }
    }

    public function authenticate()
    {
        $path = rtrim(Request::getPath(), '/') . '/';
        if (isset($this->urls) && isset($this->authenticator) && isset($this->model) && isset($this->config)) {
            //verify if logout
            if (preg_match("#^" . $this->logout . '/$#', $path)) {
                $this->eraseCredentials();
                $this->content = $this->onAuthenticateFail();
            }
            // verify if login 
            else if (preg_match("#^" . $this->login . '/$#', $path)) {
                $this->eraseCredentials();
                $this->verifyPost(function ($user) {
                    Handler::renderViewContent($this->onAuthenticateSuccess($user));
                }, function () {
                    Handler::renderViewContent($this->onAuthenticateFail());
                });
            }
            // verify if api login
            else if (preg_match("#^" . API_PREFIX . $this->login . '/$#', $path)) {
                $this->verifyPost(function ($user){
                    $jwt = new JWT(SECRET);
                    $token = $jwt->generate($user->id, $user->getRoles());
                    Response::AddHeader('token', $token);
                    Handler::renderViewContent($this->onApiAuthenticateSuccess($user));
                }, function () {
                    Handler::renderViewContent($this->onApiAuthenticateFail());
                });
            } else {
                // veirfy if current path like  url 
                $found = false;
                foreach ($this->urls as $url) {
                    if (preg_match("#^" . $url . "/#", $path) === 1) {
                        $found = true;
                        if ($this->IsverifySession()) {
                            $ins = Request::instance();
                            $ins->Set('auth', true);
                            $this->pass();
                        } else {
                            Handler::renderViewContent($this->onAuthenticateFail());
                        }
                        break;
                    }
                    //verify if current path like api 
                    else if (preg_match("#^" . API_PREFIX . $url . "/#", $path) === 1) {
                        $found = true;
                        if ($this->IsverifyToken()) {
                            $ins = Request::instance();
                            $ins->Set('auth', true);
                            $this->pass();
                        } else {
                            Handler::renderViewContent($this->onApiAuthenticateFail());
                        }
                        break;
                    }
                }
                if (!$found) {
                    $this->pass();
                }
            }
        } else {
            $this->pass();
        }
    }

    public function eraseCredentials()
    {
        Session::Remove($this->username);
        Session::Remove($this->password);
    }


    public function onAuthenticateSuccess($data)
    {
        return Response::RedirectToRoute('/' . $_SERVER['HTTP_REFERER'] ?? '');
    }

    public function onAuthenticateFail()
    {
        return Response::Json(['Not authentified']);
    }


    public function  onApiAuthenticateSuccess($data)
    {
        return Response::Json(['data' => (array)$data]);
    }
    public function  onApiAuthenticateFail()
    {
        return Response::Json(['message' => 'Not authentified'], 403);
    }
    public function onAuthenticate($data, $next){
    }
}
