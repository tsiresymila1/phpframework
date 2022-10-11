<?php
namespace App\Model;
use App\Model\File;
use Core\Database\BaseModel;

class Program extends BaseModel{

    protected $_table = "programs";

    public function files(){
        $this->hasMany(File::class);
    }
}
