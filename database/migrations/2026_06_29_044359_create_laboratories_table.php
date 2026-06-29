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
        Schema::create('laboratories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patient_id')->constrained()->onDelete('cascade');

            $table->string('hematocrito')->nullable()->comment('Unidad: %');
            $table->string('hemoglobina')->nullable()->comment('Unidad: g/dl');

            // --- QUÍMICA SANGUÍNEA Y ELECTROLITOS ---
            $table->string('urea_pre')->nullable()->comment('Unidad: mg/dl');
            $table->string('urea_post')->nullable()->comment('Unidad: mg/dl');
            $table->string('cloro')->nullable()->comment('Unidad: mmol/L');
            $table->string('sodio')->nullable()->comment('Unidad: mmol/L');
            $table->string('potasio')->nullable()->comment('Unidad: mmol/L');
            $table->string('fosforo')->nullable()->comment('Unidad: mg/dl | Dosaje de Fósforo inorganico');
            $table->string('calcio_total')->nullable()->comment('Unidad: mg/dl | Dosaje de Calcio; total');
            $table->string('tgo')->nullable()->comment('Varones: menor a 50 - Mujeres: menor a 35 | Unidad: U/L | Dosaje de transaminasa glutámico oxalacética');
            $table->string('tgp')->nullable()->comment('Varones: menor a 50 - Mujeres: menor a 36 | Unidad: U/L | Dosaje de transaminasa glutámico pirúvica');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratories');
    }
};
