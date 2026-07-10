<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Pehla column add karo
            $table->unsignedBigInteger('referred_by')->nullable()->after('id');
            $table->integer('referral_count')->default(0)->after('referred_by');
            $table->integer('referral_points')->default(0)->after('referral_count');
            
            // Phir foreign key add karo
            $table->foreign('referred_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['referred_by', 'referral_count', 'referral_points']);
        });
    }
};