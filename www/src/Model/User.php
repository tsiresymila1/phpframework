<?php

namespace App\Model;

use Core\Database\UserAuthenticatorModel;

class User extends UserAuthenticatorModel
{

    protected $_table = "users";

    /**
     * @type varchar(250)
     * @default tsiresymila@gmail.com
     * @notnull true
     */
    public  $name;
    /**
     * @type varchar(250)
     * @default tsiresymila@gmail.com
     * @notnull true
     */
    public  $email;
    /**
     * @type varchar(250)
     * @notnull true
     */
    public  $password;
//    /**
//     * @type tinyint(1)
//     * @notnull true
//     *
//     */
//    public  $isAdmin;
    /**
     * @type json
     * @notnull true
     */
    public   $roles;
//    /**
//     * @type int(11)
//     */
//    public  $nb_connexion;

    public function getRoles()
    {
        return explode(',', $this->roles);
    }

    public function files(){
        return $this->hasMany(File::class,'user_id');
    }
}
