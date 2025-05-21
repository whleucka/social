<?php

use Echo\Interface\Database\Migration;
use Echo\Framework\Database\{Schema, Blueprint};

return new class implements Migration
{
    private string $table = "posts";

    public function up(): string
    {
         return Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->uuid("uuid")->default("(UUID())");
            $table->text("comment");
            $table->timestamps();
            $table->primaryKey("id");
            $table->foreignKey("user_id")->references("users", "id")->onDelete("CASCADE");
        });
    }

    public function down(): string
    {
         return Schema::drop($this->table);
    }
};
