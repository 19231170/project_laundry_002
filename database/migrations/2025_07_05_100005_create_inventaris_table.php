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
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_pengeluaran')->nullOnDelete();
            $table->decimal('jumlah_stok', 10, 2)->default(0);
            $table->string('satuan');
            $table->decimal('harga_beli_terakhir', 12, 2)->nullable();
            $table->decimal('minimal_stok', 10, 2)->default(1);
            $table->foreignId('supplier_id')->nullable()->constrained('supplier')->nullOnDelete();
            $table->date('tanggal_beli_terakhir')->nullable();
            $table->string('lokasi_penyimpanan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
