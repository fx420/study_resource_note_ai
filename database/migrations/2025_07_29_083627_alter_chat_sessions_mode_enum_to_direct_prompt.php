<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Migrate existing rows from 'normal' → 'direct' and 'detailed' → 'prompt'
        DB::table('chat_sessions')
            ->where('mode', 'normal')
            ->update(['mode' => 'direct']);

        DB::table('chat_sessions')
            ->where('mode', 'detailed')
            ->update(['mode' => 'prompt']);

        // 2) Alter the enum definition
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->enum('mode', ['direct','prompt'])
                  ->default('direct')
                  ->change();
        });
    }

    public function down(): void
    {
        // revert enum to normal/detailed
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->enum('mode', ['normal','detailed'])
                  ->default('normal')
                  ->change();
        });

        // convert data back
        DB::table('chat_sessions')
            ->where('mode', 'direct')
            ->update(['mode' => 'normal']);
        DB::table('chat_sessions')
            ->where('mode', 'prompt')
            ->update(['mode' => 'detailed']);
    }
};
