<?php

namespace App\Modules;

use App\Modules\Book;

class User {
    
    public function __construct()
    {
        $book = new Book();
        
        echo "<br/> user constructor";
    }
}
