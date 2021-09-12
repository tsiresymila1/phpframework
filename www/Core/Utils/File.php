<?php
    namespace Utils;

    class File {
        
        private $name;
        private $type;
        private $tmp_name;
        private $size;
        private $fullpath;
        private $securename ;

        public function __construct($file)
        {
            $this->name = $file['name'];
            $this->type = $file['type'];
            $this->tmp_name = $file['tmp_name'];
            $this->size = $file['size'];
        }

        public function getTempFile(){
            return $this->tmp_name;
        }

        public function getType(){
            return $this->type;
        }

        public function getSize(){
            return $this->size;
        }

        public function getSecureName(){
            return $this->securename;
        }

        public function getName(){
            return $this->name;
        }

        public function getFullpath(){
            return $this->fullpath;
        }

        public function upload($path=null,$issecure=false){
            $this->securename = basename($this->name);
            $this->fullpath =  UPLOADED_FOLDER.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$this->securename;
            $imageFileType = strtolower(pathinfo($this->fullpath,PATHINFO_EXTENSION));
            if($issecure){
                $this->securename = uniqid('image_',true).$imageFileType;
                $this->fullpath =  UPLOADED_FOLDER.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$this->securename;
            }
            move_uploaded_file($this->tmp_name,$this->fullpath);
            return $this->fullpath;
        }
    }
?>