<?php

    namespace App\Model;
    use Core\Database\Model;

    class UserModel extends Model {

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
        public  $nb_connextion ;
    }

?>