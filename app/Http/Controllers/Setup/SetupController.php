<?php

namespace App\Http\Controllers\Setup;

use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\Get;

class SetupController extends Controller
{
    #[Get("/setup", "setup.index")] 
    public function index(): string
    {
        return $this->render("setup/index.html.twig");
    }
}
