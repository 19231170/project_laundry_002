import requests

BASE_URL = "http://localhost:8000"
TIMEOUT = 30

def test_post_transaksi_with_valid_data_should_create_transaction():
    pelanggan_id = None
    layanan_ids = []

    try:
        # Step 1: Create a new pelanggan to get valid pelanggan_id
        pelanggan_payload = {
            "nama": "Test Pelanggan",
            "telepon": "08123456789",
            "alamat": "Jl. Test No. 1",
            "email": "testpelanggan@example.com"
        }
        res_pelanggan = requests.post(
            f"{BASE_URL}/api/v1/pelanggan",
            json=pelanggan_payload,
            timeout=TIMEOUT
        )
        assert res_pelanggan.status_code == 200, f"Failed to create pelanggan: {res_pelanggan.text}"
        pelanggan_data = res_pelanggan.json()
        pelanggan_id = pelanggan_data.get("id") or pelanggan_data.get("pelanggan_id")
        assert pelanggan_id is not None, "pelanggan_id not found in response"

        # Step 2: Create at least one layanan to use in transaksi
        layanan_payload = {
            "nama_layanan": "Cuci Test",
            "satuan": "kg",
            "harga": 15000,
            "deskripsi": "Layanan cuci test otomatis"
        }
        res_layanan = requests.post(
            f"{BASE_URL}/api/v1/layanan",
            json=layanan_payload,
            timeout=TIMEOUT
        )
        assert res_layanan.status_code == 200, f"Failed to create layanan: {res_layanan.text}"
        layanan_data = res_layanan.json()
        layanan_id = layanan_data.get("id") or layanan_data.get("layanan_id")
        assert layanan_id is not None, "layanan_id not found in response"
        layanan_ids.append(layanan_id)

        # Step 3: Prepare transaksi payload with corrected layanan key
        transaksi_payload = {
            "pelanggan_id": pelanggan_id,
            "tanggal_masuk": "2026-06-15",
            "tanggal_selesai": "2026-06-17",
            "status": "proses",
            "catatan": "Test transaksi creation",
            "layanan": [
                {
                    "id": layanan_id,
                    "jumlah": 2
                }
            ]
        }

        # Step 4: POST transaksi without auth as per PRD (no authentication required)
        res_transaksi = requests.post(
            f"{BASE_URL}/api/v1/transaksi",
            json=transaksi_payload,
            timeout=TIMEOUT
        )
        assert res_transaksi.status_code == 200, f"Expected status 200 but got {res_transaksi.status_code}: {res_transaksi.text}"
        transaksi_data = res_transaksi.json()
        assert transaksi_data.get("pelanggan_id") == pelanggan_id, "pelanggan_id mismatch in response"
        assert transaksi_data.get("status") == transaksi_payload["status"], "status mismatch in response"
        assert transaksi_data.get("catatan") == transaksi_payload["catatan"], "catatan mismatch in response"
        assert isinstance(transaksi_data.get("layanan"), list) and len(transaksi_data["layanan"]) > 0, "layanan array missing or empty in response"

    finally:
        # Cleanup created transaksi if possible (if response gave id)
        try:
            if 'transaksi_data' in locals():
                transaksi_id = transaksi_data.get("id") or transaksi_data.get("transaksi_id")
                if transaksi_id:
                    requests.delete(f"{BASE_URL}/api/v1/transaksi/{transaksi_id}", auth=None, timeout=TIMEOUT)
        except Exception:
            pass

        # Cleanup layanan created
        for lid in layanan_ids:
            try:
                requests.delete(f"{BASE_URL}/api/v1/layanan/{lid}", timeout=TIMEOUT)
            except Exception:
                pass

        # Cleanup pelanggan created
        if pelanggan_id:
            try:
                requests.delete(f"{BASE_URL}/api/v1/pelanggan/{pelanggan_id}", timeout=TIMEOUT)
            except Exception:
                pass

test_post_transaksi_with_valid_data_should_create_transaction()
