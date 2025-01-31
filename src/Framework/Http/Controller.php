<?php

namespace Echo\Framework\Http;

use Echo\Interface\Http\Controller as HttpController;
use Echo\Interface\Http\Request;

class Controller implements HttpController
{
    public function __construct(protected Request $request)
    {
    }
}
