<?php

namespace App\Model;

use Core\Database\Model;
use Core\Http\Security\AuthenticatorModelInterface;

class UserModel extends Model  implements AuthenticatorModelInterface
{

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
    /**
     * @type tinyint(1)
     * @notnull true
     * 
     */
    public  $isAdmin;
    /**
     * @type json
     * @notnull true
     */
    public   $roles;
    /**
     * @type int(11)
     */
    public  $nb_connextion;

    public function getRoles()
    {
        return explode(',', $this->roles);
    }
}
