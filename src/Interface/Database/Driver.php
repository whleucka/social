<?php

namespace Echo\Interface\Database;

interface Driver
{
    public function getDsn(): string;
    public function getUsername(): string;
    public function getPassword(): string;
    public function getOptions(): array;
}
