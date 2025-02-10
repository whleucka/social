<?php

namespace Echo\Interface\Database;

interface Migration
{
    public function up(): string;
    public function down(): string;
}
