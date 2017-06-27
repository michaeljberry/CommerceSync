<?php

class Bcrypt
{

    private $rounds;

    public function __construct($rounds = 12)
    {
        if (CRYPT_BLOWFISH != 1) {
            throw new Exception("Bcrypt is not supported on this server, please see the following to learn more: http://php.net/crypt");
        }
        $this->rounds = $rounds;
    }
//    private function genSalt(){
//        $string = str_shuffle(mt_rand());
//        $salt = uniqid($string, true);
//        return $salt;
//    }
//    public function genHash($password){
//        $hash = crypt($password, '$2y$' . $this->rounds . '$' . $this->genSalt());
//        return $hash;
//    }
//    public function verify($password, $existingHash){
//        $hash = crypt($password, $existingHash);;
//        if($hash === $existingHash){
//            return true;
//        }else{
//            return false;
//        }
//    }
    public static function hashPass($password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return $hash;
    }

    public static function verifyPass($password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);;
        if (password_verify($password, $hash)) {
            return true;
        } else {
            return false;
        }
    }
}