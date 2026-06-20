<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop detail_pengeluaran table
        Schema::dropIfExists('detail_pengeluaran');

        // Drop foreign keys on pengeluaran
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropForeign(['kategori_pengeluaran_id']);
            $table->dropForeign(['user_id']);
        });

        // Simplify pengeluaran table
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->renameColumn('tanggal', 'date');
            $table->renameColumn('total_biaya', 'amount');
            $table->dropColumn([
                'kategori_pengeluaran_id',
                'keterangan',
                'bukti_pembayaran',
                'supplier_id',
                'penerima',
                'user_id',
            ]);
        });
    }

    public function down(): void
    {
        // Recreate columns on pengeluaran
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->renameColumn('date', 'tanggal');
            $table->renameColumn('amount', 'total_biaya');
            $table->foreignId('kategori_pengeluaran_id')->nullable()->constrained('kategori_pengeluaran')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('supplier')->nullOnDelete();
            $table->string('penerima', 100)->nullable();
        });

        // Recreate detail_pengeluaran
        Schema::create('detail_pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengeluaran_id')->constrained('pengeluaran')->cascadeOnDelete();
            $table->string('nama_item');
            $table->decimal('jumlah', 10, 2);
            $table->string('satuan');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->foreignId('supplier_id')->nullable()->constrained('supplier')->nullOnDelete();
            $table->timestamps();
        });
    }
};
