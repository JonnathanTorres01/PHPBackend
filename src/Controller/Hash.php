<?php

namespace App\Controller;

class hash {

public static function hash(string $texto) : string{

return password_hash($texto , PASSWORD_BCRYPT, ['cost' => 10]);

}



}