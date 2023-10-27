<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visas', function (Blueprint $table) {
            $table->id();
            $table->double('selling_price');
            $table->double('execution_price');
            $table->foreignId('category_id')->index()->constrained('categories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('from_company_id')->index()->constrained('companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('to_company_id')->index()->constrained('companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('is_deposit')->default(0);
            $table->boolean('is_transfer')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visas');
    }
};
