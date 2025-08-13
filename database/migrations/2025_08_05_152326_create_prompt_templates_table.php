<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->string('course');
            $table->string('subject');
            $table->string('topic');
            $table->json('metadata');     // stores metadata like education_level, note_level, etc.
            $table->string('mode')->nullable();   // 'direct' or 'prompt'
            $table->text('prompt')->nullable();   // the custom prompt text when mode = 'prompt'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompt_templates');
    }
};
