<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->enum('mode', ['normal','detailed'])->default('normal')->change();

            $table->string('prior_knowledge')->nullable()->after('topic');
            $table->string('learning_goal')->nullable()->after('prior_knowledge');
            $table->integer('examples_count')->default(0)->after('difficulty');
            $table->enum('content_format', ['text','bullet','table'])
                  ->default('text')->after('examples_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Revert 'mode' back to ['direct','prompt']
            $table->enum('mode', ['direct','prompt'])->default('direct')->change();

            // Drop the added columns
            $table->dropColumn(['prior_knowledge','learning_goal','examples_count','content_format']);
        });
    }
};
