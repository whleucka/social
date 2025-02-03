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

    #[Get("/api/test", "welcome.api.test", ["api"])] 
    public function test(): string
    {
        return "The Ultimate Answer to Life, The Universe and Everything is...42!";
    }
}
