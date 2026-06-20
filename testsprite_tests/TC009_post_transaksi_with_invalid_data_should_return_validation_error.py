import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
TIMEOUT = 30
AUTH = HTTPBasicAuth("admin@laundry.com", "admin123")
HEADERS = {
    "Content-Type": "application/json"
}

def test_post_transaksi_with_invalid_data_should_return_validation_error():
    url = f"{BASE_URL}/api/v1/transaksi"
    
    test_payloads = [
        # Invalid pelanggan_id (negative number)
        {
            "pelanggan_id": -1,
            "tanggal_masuk": "2026-06-15",
            "tanggal_selesai": "2026-06-16",
            "status": "pending",
            "catatan": "Invalid pelanggan_id test",
            "layanan": [{"id": 1, "quantity": 2}]
        },
        # Malformed layanan array (not an array)
        {
            "pelanggan_id": 1,
            "tanggal_masuk": "2026-06-15",
            "tanggal_selesai": "2026-06-16",
            "status": "pending",
            "catatan": "Malformed layanan test",
            "layanan": "not_an_array"
        },
        # Malformed layanan array (array with invalid item structure)
        {
            "pelanggan_id": 1,
            "tanggal_masuk": "2026-06-15",
            "tanggal_selesai": "2026-06-16",
            "status": "pending",
            "catatan": "Malformed layanan item test",
            "layanan": [{"invalid_key": "value"}]
        }
    ]
    
    for payload in test_payloads:
        try:
            response = requests.post(url, json=payload, headers=HEADERS, auth=AUTH, timeout=TIMEOUT)
        except requests.RequestException as e:
            assert False, f"Request failed: {e}"
        
        assert response.status_code == 400, f"Expected status code 400 but got {response.status_code} for payload {payload}"
        
        try:
            resp_json = response.json()
        except ValueError:
            assert False, f"Response is not valid JSON for payload {payload}"
        
        # Check for validation error message presence, common keys in such errors might include 'errors' or 'message'
        assert any(key in resp_json for key in ["errors", "message"]), f"Validation error details not found in response for payload {payload}"

test_post_transaksi_with_invalid_data_should_return_validation_error()