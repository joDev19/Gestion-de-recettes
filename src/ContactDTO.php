<?php
namespace App;
class ContactDTO{
    public function __construct(
        public string $nom =  "",
        public string $email =  "",
        public string $message =  ""
    )
    {
       
    }
}