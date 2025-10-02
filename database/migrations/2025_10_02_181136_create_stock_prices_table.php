<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->cascadeOnDelete();   // if company is deleted, prices go too
            $table->date('date')->index();
            $table->decimal('price', 14, 4);
            $table->timestamps();

            $table->unique(['company_id', 'date']); // avoid duplicates per day/company
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_prices');
    }
};
