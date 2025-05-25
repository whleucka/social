<?php

use Echo\Interface\Database\Migration;
use Echo\Framework\Database\{Schema, Blueprint};

return new class implements Migration
{
    private string $table = "users";

    public function up(): string
    {
         return Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid")->default("(UUID())");
            $table->unsignedTinyInteger("bot")->default(0);
            $table->varchar("first_name");
            $table->varchar("surname");
            $table->varchar("username");
            $table->varchar("email");
            $table->binary("password", 96);
            $table->char("lang", 2)->default("'en'");
            $table->varchar("avatar")->nullable();
            $table->varchar("banner")->nullable();
            $table->varchar("description")->nullable();
            $table->timestamps();
            $table->unique("email");
            $table->unique("username");
            $table->primaryKey("id");
        });
    }

    public function down(): string
    {
         return Schema::drop($this->table);
    }
};
