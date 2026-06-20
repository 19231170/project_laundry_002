import requests

BASE_URL = "http://localhost:8000"
TIMEOUT = 30


def test_post_layanan_with_valid_data_should_create_service():
    url = f"{BASE_URL}/api/v1/layanan"
    headers = {
        "Content-Type": "application/json"
    }
    payload = {
        "nama_layanan": "Cuci Kering",
        "satuan": "kg",
        "harga": 15000,
        "deskripsi": "Layanan cuci kering berkualitas"
    }

    try:
        response = requests.post(url, json=payload, headers=headers, timeout=TIMEOUT)
        assert response.status_code == 200, f"Expected status code 200 but got {response.status_code}"
        content_type = response.headers.get('Content-Type', '')
        assert 'application/json' in content_type.lower(), f"Expected JSON response but got Content-Type: {content_type}"
        data = response.json()
        assert isinstance(data, dict), f"Expected response body to be dict but got {type(data)}"
        assert data.get("nama_layanan") == payload["nama_layanan"], "nama_layanan in response does not match request"
        assert data.get("satuan") == payload["satuan"], "satuan in response does not match request"
        assert data.get("harga") == payload["harga"], "harga in response does not match request"
        assert data.get("deskripsi") == payload["deskripsi"], "deskripsi in response does not match request"
        assert "id" in data, "Response does not contain layanan id"
    finally:
        # Cleanup: delete the created layanan resource if it was created
        try:
            if 'data' in locals() and "id" in data:
                layanan_id = data["id"]
                del_response = requests.delete(f"{url}/{layanan_id}", timeout=TIMEOUT)
                # It's OK if delete fails, just attempt best effort cleanup
        except Exception:
            pass


test_post_layanan_with_valid_data_should_create_service()
