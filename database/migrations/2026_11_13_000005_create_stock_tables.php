<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('direction', 3)->index();
            $table->string('activity')->index();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->date('transaction_date')->index();
            $table->text('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['direction', 'transaction_date']);
            $table->index(['customer_id', 'transaction_date']);
        });

        Schema::create('stock_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transaction_id')->constrained('stock_transactions')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();

            $table->index(['item_id', 'stock_transaction_id']);
        });

        Schema::create('serial_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('serial_number')->unique();
            $table->string('status')->default('available')->index();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('stock_transaction_item_id')->nullable()->constrained('stock_transaction_items')->nullOnDelete();
            $table->date('received_at')->nullable();
            $table->date('issued_at')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serial_numbers');
        Schema::dropIfExists('stock_transaction_items');
        Schema::dropIfExists('stock_transactions');
    }
};
