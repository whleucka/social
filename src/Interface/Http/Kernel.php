<?php

namespace Echo\Interface\Http;

interface Kernel
{
    public function handle(Request $request): void;
}

