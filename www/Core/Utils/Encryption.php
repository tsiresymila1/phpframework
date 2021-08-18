<?php

namespace Core\Utils;

class Encryption
{

    private  $ciphering ;
    private  $encryption_key ;
    private  $encryption_iv ;
    private  $options;

    public function __construct($ciphering="aes-256-cbc",$encryption_key="SuPerEncKey2010",$encryption_iv="1234567891011121",$options=0)
    {
        $this->ciphering = $ciphering;
        $this->encryption_key = $encryption_key;
        $this->encryption_iv = $encryption_iv;
        $this->options = $options;
    }
    
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
