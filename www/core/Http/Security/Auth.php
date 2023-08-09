<?php

namespace Core\Http\Security;

use Core\Database\UserAuthenticatorModel;

class Auth
{
    protected static $_instance = null;
    protected $user ;
    
    /**
     * instance
     *
     * @return Auth
     */
    private static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Auth();
        }
        return self::$_instance;
    }
    
    /**
     * check
     *
     * @return bool
     */
    public static function check(){
        $ins = self::instance();
        return $ins->user != null;
    }
    
    /**
     * attemp
     *
     * @param  UserAuthenticatorModel $user
     * @return Auth
     */
    public static function attemp($user){
        $ins = self::instance();
        $ins->user = $user;
        return $ins;
    }
    
    /**
     * user
     *
     * @return UserAuthenticatorModel
     */
    public static function user(){
        $ins = self::instance();
        return $ins->user; 
        
    }

}
