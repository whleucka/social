<?php

namespace Echo\Interface\Http;

interface Router
{
    public function dispatch(Request $request): ?array;
}
