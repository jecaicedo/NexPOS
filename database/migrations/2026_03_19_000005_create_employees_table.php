<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document_number')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('specialty')->nullable(); // Ej: Motor, Frenos, Electricidad
            $table->decimal('labor_rate', 10, 2)->default(0); // tarifa hora/trabajo
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
