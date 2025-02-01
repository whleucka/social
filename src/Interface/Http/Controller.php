<?php

namespace Echo\Interface\Http;

interface Controller
{
    public function setRequest(Request $request): void;
    public function getRequest(): Request;
}
