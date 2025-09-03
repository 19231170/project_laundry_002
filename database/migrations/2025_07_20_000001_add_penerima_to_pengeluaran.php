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
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->string('penerima')->nullable()->after('bukti_pembayaran');
            $table->foreignId('supplier_id')->nullable()->after('kategori_pengeluaran_id')
                  ->constrained('supplier')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['penerima', 'supplier_id']);
        });
    }
};
