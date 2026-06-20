# Penjelasan Produk: Sistem Manajemen Laundry (Backend API)

Dokumen ini menjelaskan tentang tujuan, fitur utama, dan bagaimana cara kerja dari proyek **Sistem Manajemen Laundry** ini.

## 🎯 Tujuan Produk (Objectives)

Tujuan utama dari proyek ini adalah menyediakan sebuah **Backend API (Application Programming Interface)** yang tangguh, terstruktur, dan mudah diintegrasikan untuk kebutuhan bisnis laundry. 

Sistem ini dirancang untuk:
1. **Digitalisasi Operasional**: Menggantikan pencatatan manual dengan sistem digital terpusat.
2. **Kemudahan Integrasi**: Menjadi jembatan data yang dapat digunakan oleh berbagai platform *frontend*, baik itu aplikasi kasir berbasis Web, aplikasi Mobile untuk pelanggan, maupun sistem dashboard admin.
3. **Akurasi Data & Pelaporan**: Memastikan setiap transaksi tercatat dengan akurat dan memudahkan pemilik laundry dalam memantau pemasukan serta laporan operasional.

## ✨ Fitur Utama (Key Features)

Produk ini dilengkapi dengan serangkaian modul fitur yang mencakup seluruh alur bisnis laundry:

1. **Manajemen Pelanggan (Customers)**
   - Pendaftaran dan pengelolaan data pelanggan (Nama, Telepon, Alamat, Email).
   - Memudahkan pencarian histori transaksi berdasarkan pelanggan.

2. **Manajemen Layanan (Services)**
   - Pengaturan berbagai jenis layanan laundry yang ditawarkan.
   - Mendukung berbagai satuan hitung fleksibel, seperti Kilogram (KG), Satuan (PCS), atau Meter (M).
   - Penetapan harga dasar per layanan.

3. **Manajemen Transaksi (Transactions)**
   - Pencatatan transaksi harian secara detail.
   - *Auto-generate* nomor struk/kode transaksi (contoh: `TRX202507040001`).
   - Pelacakan status cucian (`pending`, `proses`, `selesai`, `diambil`).
   - Perhitungan subtotal dan total harga secara otomatis.

4. **Laporan & Statistik (Reports & Analytics)**
   - Endpoint untuk menampilkan dashboard metrik harian/bulanan.
   - Laporan pemasukan dan pengeluaran.
   - Analitik layanan terlaris untuk membantu pengambilan keputusan bisnis.

5. **Export & Import Excel**
   - Kemampuan untuk mengunduh (export) data transaksi dan laporan keuangan ke dalam format file Excel.
   - Dukungan untuk mengunggah (import) riwayat transaksi dari file Excel ke dalam sistem secara otomatis.

6. **Cetak Struk Digital (PDF Receipt)**
   - Fitur otomatis untuk menghasilkan struk bukti transaksi dalam format PDF yang profesional dan siap dicetak atau dikirimkan ke pelanggan via WhatsApp/Email.

## ⚙️ Cara Kerja Produk (How it Works)

Sebagai sebuah **Sistem Backend API**, produk ini berjalan di belakang layar (server) dan tidak memiliki User Interface (UI) secara langsung. Cara kerjanya melibatkan komunikasi data (Request/Response) dengan aplikasi Frontend.

### Alur Kerja Bisnis (Business Flow)
1. **Setup Awal**: Pemilik/Admin laundry menginputkan jenis-jenis layanan beserta harga dan satuannya melalui aplikasi.
2. **Penerimaan Cucian**: Saat ada pelanggan datang, kasir membuat transaksi baru. Kasir memilih pelanggan (atau mendaftarkan pelanggan baru), lalu memilih layanan yang digunakan beserta jumlah/beratnya.
3. **Pemrosesan Data**: Sistem (Backend ini) menerima data tersebut, menghitung total harga, membuat `kode_transaksi` unik, dan menyimpan detail pesanan ke dalam *Database*.
4. **Update Status**: Seiring berjalannya waktu, pekerja laundry mengupdate status cucian dari `pending` -> `proses` -> `selesai`.
5. **Pengambilan Cucian**: Saat pelanggan mengambil cucian, status diubah menjadi `diambil` dan transaksi dianggap tuntas. Kasir dapat mengunduh dan mencetak **Struk PDF** melalui sistem kapan saja.

### Alur Kerja Teknis (Technical Flow)
- Aplikasi dibangun menggunakan Framework **Laravel 10** dengan bahasa pemrograman **PHP** dan database **MySQL**.
- Frontend (misal: React, Vue, atau aplikasi Android) mengirimkan **HTTP Request** (GET, POST, PUT, DELETE) ke endpoint API (contoh: `/api/v1/transaksi`).
- Sistem memproses *request* tersebut, melakukan validasi kelengkapan data, menyimpannya di database, lalu mengembalikan **HTTP Response** berformat **JSON** (JavaScript Object Notation).
- Fitur spesifik seperti pembuatan PDF diproses oleh library *DomPDF*, dan pemrosesan Excel ditangani oleh library *Laravel Excel (Maatwebsite)*.
