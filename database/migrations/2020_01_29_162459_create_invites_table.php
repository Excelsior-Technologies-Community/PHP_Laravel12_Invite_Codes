<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('max_uses')->default(1);
            $table->integer('uses')->default(0);
            $table->string('email')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->foreignId('invited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('registered_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};