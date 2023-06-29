<?php

$container->set('config_bd', function(){

return (object) [
    "host" => "localhost",
    "bd" => "taller",
    "usr" => "root",
    "pass" => "",
    "charset" => "utf8mb4"
];
});

$container->set('clave', function(){
return "jkhfdjkhgsjkfdhgks*?";
});