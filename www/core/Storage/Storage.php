<?php

namespace Core\Storage;

use Core\Utils\Encryption;
use Core\Utils\File;

class Storage
{

    private static $_instance = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Storage();
        }
        return self::$_instance;
    }

    public static function url($filename)
    {
        if (file_exists(Storage::path($filename))) {
            return STATIC_URL . "/" . "public/" . $filename;
        } else {
            return null;
        }

    }
    public static function urlPrivate($filename)
    {
        if (file_exists(Storage::pathPrivate($filename))) {
            $encryption = new Encryption();
            return STATIC_URL . "/" . "private/" . $filename . "?key=" . $encryption->encode($filename);
        } else {
            return null;
        }
    }

    public static function pathPrivate($filename)
    {
        return UPLOADED_FOLDER . DIRECTORY_SEPARATOR . "private" . DIRECTORY_SEPARATOR . $filename;
    }
    public static function path($filename)
    {
        return UPLOADED_FOLDER . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . $filename;
    }

    public static function put(File $file, $secure = false)
    {
        $file->upload("public", $secure);
    }
    public static function putPrivate(File $file, $secure = false)
    {
        $file->upload("private", $secure);
    }
}