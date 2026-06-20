import requests

BASE_URL = "http://localhost:8000"
HEADERS = {"Content-Type": "application/json"}

def test_post_pelanggan_with_valid_data_should_create_customer():
    url = f"{BASE_URL}/api/v1/pelanggan"
    payload = {
        "nama": "Budi Santoso",
        "telepon": "081234567890",
        "alamat": "Jalan Merdeka No. 123, Jakarta",
        "email": "budi.santoso@example.com"
    }

    response = None
    try:
        response = requests.post(url, json=payload, headers=HEADERS, timeout=30)
        assert response.status_code == 200, f"Expected status code 200 but got {response.status_code}"
        content_type = response.headers.get('Content-Type', '')
        assert 'application/json' in content_type.lower(), f"Expected JSON response but got '{content_type}'"
        json_resp = response.json()
        assert isinstance(json_resp, dict), "Response should be an object/dict"
        # Check if the returned object contains the sent data
        for key in payload:
            assert key in json_resp, f"Response JSON missing key '{key}'"
            assert json_resp[key] == payload[key], f"Expected {key} to be '{payload[key]}', got '{json_resp[key]}'"
    finally:
        if response is not None and response.status_code == 200:
            try:
                pelanggan_id = response.json().get("id") or response.json().get("pelanggan_id")
                if pelanggan_id:
                    del_url = f"{BASE_URL}/api/v1/pelanggan/{pelanggan_id}"
                    requests.delete(del_url, timeout=30)
            except Exception:
                pass

test_post_pelanggan_with_valid_data_should_create_customer()
