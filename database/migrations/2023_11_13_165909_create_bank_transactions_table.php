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
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('bank_accounts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('visa_id')->constrained('visas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->double('amount');
            $table->enum('type', ['DEPOSIT', 'TRANSFER']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
