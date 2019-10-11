<?php

namespace App\Utility;

class AppUtility {
 
    public static function encryptString($myText) {
        $key = 'my secret key';
        $iv = '12345678';

        $cipher = mcrypt_module_open(MCRYPT_BLOWFISH,'','cbc','');

        mcrypt_generic_init($cipher, $key, $iv);
        $encrypted = mcrypt_generic($cipher,$myText);
        mcrypt_generic_deinit($cipher);
        
        return $encrypted;
    }

    public static function dycryptString($encrypted) {

        $key = 'my secret key';
        $iv = '12345678';

        mcrypt_generic_init($cipher, $key, $iv);
        $decrypted = mdecrypt_generic($cipher,$encrypted);
        mcrypt_generic_deinit($cipher);

        return $decrypted;
    }

}