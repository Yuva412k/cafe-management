<?php

/**
 * Encryption Class
 */

 namespace app\core;

 class Encryption{

    /**
     * Encrypt an id
     */
    public static function encryptId($id)
    {
        return base64_encode($id);
    }

    /**
     * Decrypt for Id
     */
    public static function decryptId($id)
    {
        return base64_decode($id);
    }

    public static function encrypt($text){

        // --- Encrypt --- //
        $key = openssl_digest(Config::get("ENCRYPTION_KEY"), 'SHA256', TRUE);
        $plaintext = $text;
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);

        // binary cipher
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        // or replace OPENSSL_RAW_DATA & $iv with 0 & bin2hex($iv) for hex cipher (eg. for transmission over internet)

        // or increase security with hashed cipher; (hex or base64 printable eg. for transmission over internet)
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );

        return $ciphertext;
    }

    public static function decrypt($cipher)
    {
        // --- Decrypt --- //
        $key = Config::get("ENCRYPTION_KEY");

        $ciphertext = base64_decode($cipher);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");

        $iv = substr($ciphertext, 0, $ivlen);
        $hmac = substr($ciphertext, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($ciphertext, $ivlen+$sha2len);
        
        $decryptedText = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        if (hash_equals($hmac, $calcmac)){
            
            return $decryptedText;
        }
        return false;
    }

 }