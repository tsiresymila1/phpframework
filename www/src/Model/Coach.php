<?php

namespace App\Model;

use Core\Database\BaseModel;

class Coach extends BaseModel {

    protected $_table = "caochs";
    
    /**
     * @type number
     * @primary
     * @autoincrement
     * @default tsiresymila@gmail.com
     * @notnull true
     */
    public $id;

    /**
     * @type varchar(250)
     * @default tsiresymila@gmail.com
     * @notnull true
     */
    public $username;

    /**
     * @type varchar(250)
     * @default tsiresymila@gmail.com
     * @notnull true
     */
    public $name;

    /**
     * @type varchar(250)
     * @default tsiresymila@gmail.com
     * @notnull true
     */
    public $lastname;

    /**
     * @type varchar(250)
     * @default tsiresymila@gmail.com
     * @notnull true
     */
    public $phone;

    /**
     * @type varchar(250)
     * @default tsiresymila@gmail.com
     * @notnull true
     */
    public $email;

    /**
     * @type varchar(250)
     * @default tsiresymila@gmail.com
     * @notnull true
     */
    public $matricule;

    /**
     * @type varchar(250)
     * @default tsiresymila@gmail.com
     * @notnull true
     */
    public $category;

    /**
     * @type varchar(250)
     * @default tsiresymila@gmail.com
     * @notnull true
     */
    public $image;

    /**
     * @type json
     * @default tsiresymila@gmail.com
     * @notnull true
     */
    public $role;

    /**
     * @type integer
     * @default 0
     * @notnull true
     */
    public $present;

    /**
     * @type integer
     * @default 0
     * @notnull true
     */
    public $missing;
}
