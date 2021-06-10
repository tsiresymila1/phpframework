<?php

namespace Core\Utils;

class Encryption
{

    private String $ciphering = "aes-256-cbc";
    private String $encryption_key      = "SuPerEncKey2010";
    private String $encryption_iv = '1234567891011121';
    private int $options = 0;
    public function set_key($key)
    {
        $this->encryption_key = $key;
    }

    public  function safe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    public function safe_b64decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public  function encode($value)
    {
        if (!$value) {
            return false;
        }
        $text = $value;
        $crypttext = openssl_encrypt($text, $this->ciphering, $this->encryption_key, $this->options, $this->encryption_iv);
        return trim($this->safe_b64encode($crypttext));
    }

    public function decode($value)
    {
        if (!$value) {
            return false;
        }
        $crypttext = $this->safe_b64decode($value);
        $decrypttext = openssl_decrypt($crypttext, $this->ciphering, $this->encryption_key, $this->options, $this->encryption_iv);
        return trim($decrypttext);
    }
}
