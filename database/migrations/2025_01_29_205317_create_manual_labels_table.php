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
        Schema::create('manual_labels', function (Blueprint $table) {
            $table->id();
            $table->string('years', 255)->nullable();
            $table->string('card', 255)->nullable();
            $table->string('info')->nullable();
            $table->integer('card_number')->nullable();
            $table->double('grade')->nullable();
            $table->string('grade_name')->nullable();
            $table->string('qr_link', 500)->nullable();
            $table->double('surface')->nullable();
            $table->double('centering')->nullable();
            $table->double('corners')->nullable();
            $table->double('edges')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_labels');
    }
};
