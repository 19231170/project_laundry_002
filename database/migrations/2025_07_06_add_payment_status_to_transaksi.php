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
        Schema::table('transaksi', function (Blueprint $table) {
            $table->enum('status_pembayaran', ['belum_lunas', 'lunas'])->default('belum_lunas')->after('status');
            $table->date('tanggal_pembayaran')->nullable()->after('status_pembayaran');
            $table->decimal('jumlah_dibayar', 10, 2)->default(0)->after('tanggal_pembayaran');
            $table->decimal('sisa_pembayaran', 10, 2)->default(0)->after('jumlah_dibayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('status_pembayaran');
            $table->dropColumn('tanggal_pembayaran');
            $table->dropColumn('jumlah_dibayar');
            $table->dropColumn('sisa_pembayaran');
        });
    }
};
