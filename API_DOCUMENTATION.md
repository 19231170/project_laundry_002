# Laundry Management System API

Backend API untuk sistem manajemen laundry dengan fitur pencatatan transaksi, pelanggan, layanan, dan laporan.

## Fitur Utama

- **Manajemen Pelanggan**: CRUD pelanggan laundry
- **Manajemen Layanan**: CRUD layanan laundry dengan berbagai satuan (KG, PCS, M)
- **Manajemen Transaksi**: CRUD transaksi dengan detail layanan
- **Laporan**: Dashboard statistik, laporan pemasukan, layanan terlaris
- **Export/Import Excel**: Export laporan dan import data transaksi
- **Cetak Struk Digital**: Generate struk dalam format PDF

## Teknologi

- Laravel 10
- MySQL
- Laravel Excel (Maatwebsite)
- DomPDF
- Laravel Breeze (Authentication)

## Instalasi

1. Clone repository
```bash
git clone [repository-url]
cd project_laundry_withai_002
```

2. Install dependencies
```bash
composer install
npm install
```

3. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database di file `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_laundry_withai_002
DB_USERNAME=root
DB_PASSWORD=DATABASEEDO12
```

5. Run migrations dan seeders
```bash
php artisan migrate --seed
```

6. Start server
```bash
php artisan serve
```

## API Endpoints

### Base URL: `/api/v1`

### Pelanggan
- `GET /pelanggan` - List pelanggan
- `GET /pelanggan/{id}` - Detail pelanggan
- `POST /pelanggan` - Buat pelanggan baru
- `PUT /pelanggan/{id}` - Update pelanggan
- `DELETE /pelanggan/{id}` - Hapus pelanggan

### Layanan
- `GET /layanan` - List layanan
- `GET /layanan/{id}` - Detail layanan
- `POST /layanan` - Buat layanan baru
- `PUT /layanan/{id}` - Update layanan
- `DELETE /layanan/{id}` - Hapus layanan

### Transaksi
- `GET /transaksi` - List transaksi
- `GET /transaksi/{id}` - Detail transaksi
- `POST /transaksi` - Buat transaksi baru
- `PUT /transaksi/{id}` - Update transaksi
- `DELETE /transaksi/{id}` - Hapus transaksi
- `GET /transaksi/{id}/struk` - Generate struk PDF

### Laporan
- `GET /laporan/dashboard` - Dashboard statistik
- `GET /laporan/pemasukan-pengeluaran` - Laporan pemasukan/pengeluaran
- `GET /laporan/layanan-terlaris` - Laporan layanan terlaris
- `GET /laporan/export/transaksi` - Export transaksi ke Excel
- `GET /laporan/export/pemasukan-pengeluaran` - Export laporan ke Excel
- `POST /laporan/import/transaksi` - Import transaksi dari Excel

## Format Data

### Pelanggan
```json
{
  "nama": "string (required)",
  "telepon": "string (optional)",
  "alamat": "string (optional)",
  "email": "string (optional, unique)"
}
```

### Layanan
```json
{
  "nama_layanan": "string (required)",
  "satuan": "enum: KG|PCS|M (required)",
  "harga": "decimal (required)",
  "deskripsi": "string (optional)"
}
```

### Transaksi
```json
{
  "pelanggan_id": "integer (required)",
  "tanggal_masuk": "date (required)",
  "tanggal_selesai": "date (optional)",
  "status": "enum: pending|proses|selesai|diambil (optional)",
  "catatan": "string (optional)",
  "layanan": [
    {
      "layanan_id": "integer (required)",
      "jumlah": "decimal (required)"
    }
  ]
}
```

## Response Format

### Success Response
```json
{
  "status": "success",
  "message": "Optional message",
  "data": {
    // Response data
  }
}
```

### Error Response
```json
{
  "status": "error",
  "message": "Error message",
  "errors": {
    // Validation errors (if any)
  }
}
```

## Database Schema

### Tabel Pelanggan
- `id` - Primary key
- `nama` - Nama pelanggan
- `telepon` - Nomor telepon
- `alamat` - Alamat
- `email` - Email
- `created_at`, `updated_at` - Timestamps

### Tabel Layanan
- `id` - Primary key
- `nama_layanan` - Nama layanan
- `satuan` - Satuan hitung (KG/PCS/M)
- `harga` - Harga per satuan
- `deskripsi` - Deskripsi layanan
- `created_at`, `updated_at` - Timestamps

### Tabel Transaksi
- `id` - Primary key
- `kode_transaksi` - Kode unik transaksi
- `pelanggan_id` - Foreign key ke pelanggan
- `total_harga` - Total harga transaksi
- `tanggal_masuk` - Tanggal masuk
- `tanggal_selesai` - Tanggal selesai
- `status` - Status transaksi
- `catatan` - Catatan tambahan
- `created_at`, `updated_at` - Timestamps

### Tabel Detail Transaksi
- `id` - Primary key
- `transaksi_id` - Foreign key ke transaksi
- `layanan_id` - Foreign key ke layanan
- `jumlah` - Jumlah layanan
- `harga_satuan` - Harga per satuan saat transaksi
- `subtotal` - Subtotal (jumlah × harga_satuan)
- `created_at`, `updated_at` - Timestamps

## Fitur Khusus

### Auto Generate Kode Transaksi
Sistem akan otomatis generate kode transaksi dengan format: `TRX{YYYYMMDD}{0001}`
Contoh: `TRX202507040001`

### Export Excel
Sistem mendukung export data transaksi dan laporan ke format Excel dengan:
- Filter berdasarkan tanggal
- Filter berdasarkan status
- Format yang user-friendly

### Import Excel
Sistem mendukung import data transaksi dari Excel dengan format:
- Header: nama_pelanggan, telepon_pelanggan, layanan, total_harga, dll
- Validasi data otomatis
- Error handling

### Struk Digital
Generate struk transaksi dalam format PDF dengan:
- Informasi lengkap transaksi
- Detail layanan
- Total pembayaran
- Styling yang profesional

## Development

### Testing
```bash
php artisan test
```

### Code Style
```bash
vendor/bin/pint
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## Troubleshooting

### Database Connection Error
Pastikan MySQL server berjalan dan konfigurasi database di `.env` sudah benar.

### Excel Export/Import Error
Pastikan extension PHP yang dibutuhkan sudah terinstall:
- php-zip
- php-xml
- php-gd

### PDF Generation Error
Pastikan DomPDF sudah terinstall dengan benar dan tidak ada konflik dengan library lain.

## Kontribusi

1. Fork repository
2. Buat feature branch
3. Commit changes
4. Push ke branch
5. Buat Pull Request

## Lisensi

MIT License
