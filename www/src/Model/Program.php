<?php

use App\Model\File;
use Core\Database\Model;

class Program extends Model{

    public function files(){
        $this->hasMany(File::class);
    }
}
