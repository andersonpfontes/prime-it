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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('person_name');
            $table->string('email');
            $table->string('animal_name');
            $table->string('animal_type'); // ex: cão, gato, etc.
            $table->integer('age');
            $table->text('symptoms');
            $table->date('appointment_date');
            $table->enum('period', ['morning', 'afternoon']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('veterinarian_id')->nullable();

            $table->foreign('veterinarian_id')->references('id')->on('veterinarians')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
