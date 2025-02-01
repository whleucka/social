<?php

namespace App\Http\Controllers;

use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\Get;

class WelcomeController extends Controller
{
    #[Get("/", "welcome.index")] 
    public function index(): string
    {
        return "Hello, world!";
    }
}
