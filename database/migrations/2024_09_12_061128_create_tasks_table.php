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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->enum('priority', ['low', 'medium', 'height']);
            $table->date('due_date')->nullable();
            $table->enum('status', ['in-progress', 'done', 'pending']);
            $table->foreignId('project_id')->constrained('projects', 'id');
            $table->foreignId('assigned_to')->constrained('users', 'id');
            $table->foreignId('owner_id')->constrained('users', 'id');
            $table->dateTime('deadline');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
