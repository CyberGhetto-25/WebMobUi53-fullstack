<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('poll_votes', function (Blueprint $table) {
            $table->unique(['user_id', 'poll_option_id']);
        });
    }

    public function down(): void
    {
        Schema::table('poll_votes', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'poll_option_id']);
        });
    }
};
