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
            $table->unsignedBigInteger("parent_id")->nullable();
            $table->uuid("uuid")->default("(UUID())");
            $table->text("content");
            $table->text("url")->nullable();
            $table->text("image")->nullable();
            $table->unsignedTinyInteger("deleted")->default(0);
            $table->timestamps();
            $table->primaryKey("id");
            $table->foreignKey("user_id")->references("users", "id")->onDelete("CASCADE");
            $table->foreignKey("parent_id")->references("posts", "id")->onDelete("SET NULL");
        });
    }

    public function down(): string
    {
         return Schema::drop($this->table);
    }
};
