<?php

namespace Echo\Framework\Http;

use Echo\Interface\Http\Controller as HttpController;
use Echo\Interface\Http\Request;

class Controller implements HttpController
{
    protected Request $request;

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
