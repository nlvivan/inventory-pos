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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('critical_stock')->nullable();
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('critical_stock');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->foreignId('production_batch_id')->constrained('production_batches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('critical_stock');
        });
    }
};
