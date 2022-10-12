<?php
namespace App\Model;

use Core\Database\BaseModel;

class File extends BaseModel {


    protected $_table = "files";

    public $name;

    public $size;

    public $secure_name;
    
    public $type;

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
