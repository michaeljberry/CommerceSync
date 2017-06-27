<?php

class Crypt
{

    private static $key;
    private static $initialized = false;
    private static $cryptKey;

    public static function initialize()
    {
        if (self::$initialized)
            return;
        if (file_exists(ROOT . '.env')) {
            $iniArray = parse_ini_file(WEBROOT . '.env');
        } else {
            $iniArray = parse_ini_file(WEBROOT . '.env');
        }
        self::$cryptKey = $iniArray['CRYPT_KEY'];
        self::setKey(self::$cryptKey);
        self::$initialized = true;
    }

    public static function encrypt($encrypt)
    {
        self::initialize();
        $encrypt = serialize($encrypt);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
        $key = pack('H*', self::$key);
        $mac = hash_hmac('sha256', $encrypt, substr(self::$key, -32));
        $passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt . $mac, MCRYPT_MODE_CBC, $iv);
        $encoded = base64_encode($passcrypt) . '|' . base64_encode($iv);
        return $encoded;
    }

    public static function decrypt($decrypt)
    {
        self::initialize();
        $decrypt = explode('|', $decrypt . '|');
        $decoded = base64_decode($decrypt[0]);
        $iv = base64_decode($decrypt[1]);
        if (strlen($iv) !== mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)) {
            return false;
        }
        $key = pack('H*', self::$key);
        $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
        $mac = substr($decrypted, -64);
        $decrypted = substr($decrypted, 0, -64);
        $calcmac = hash_hmac('sha256', $decrypted, substr(self::$key, -32));
        if (md5($calcmac) !== md5($mac)) {
            return false;
        }
        $decrypted = unserialize($decrypted);
        return $decrypted;
    }

    public static function setKey($key)
    {
        if (ctype_xdigit($key) && strlen($key) === 64) {
            self::$key = $key;
        } else {
            trigger_error('Invalid key. Key must be a 32-byte (64 character) hexadecimal string.', E_USER_ERROR);
        }
    }
}