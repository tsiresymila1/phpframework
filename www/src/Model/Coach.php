<?php

namespace App\Model;

use Core\Database\BaseModel;

class Coach extends BaseModel {

    protected $_table = "caochs";
    
    public $id;
    public $username;
    public $name;
    public $lastname;
    public $phone;
    public $email;
    public $matricule;
    public $category;
    public $image;
    public $role;
    public $present;
    public $missing;
}
