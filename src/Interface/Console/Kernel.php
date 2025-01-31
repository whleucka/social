<?php

namespace Echo\Interface\Console;

interface Kernel
{
    public function handle(string $command): void;
}
