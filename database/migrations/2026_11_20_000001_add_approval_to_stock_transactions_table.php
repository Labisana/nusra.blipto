<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->string('status')->default('approved')->index()->after('activity');
            $table->foreignId('approved_by')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_note')->nullable()->after('approved_at');
        });

        Schema::table('stock_transaction_items', function (Blueprint $table) {
            $table->json('serials_json')->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('stock_transaction_items', function (Blueprint $table) {
            $table->dropColumn('serials_json');
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status', 'approved_by', 'approved_at', 'rejection_note']);
        });
    }
};
