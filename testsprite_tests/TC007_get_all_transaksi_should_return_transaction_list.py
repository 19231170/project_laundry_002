import requests

def test_get_all_transaksi_should_return_transaction_list():
    base_url = "http://localhost:8000"
    endpoint = "/api/v1/transaksi"
    url = f"{base_url}{endpoint}"
    auth = ("admin@laundry.com", "admin123")
    headers = {
        "Accept": "application/json"
    }
    try:
        response = requests.get(url, headers=headers, auth=auth, timeout=30)
    except requests.RequestException as e:
        assert False, f"Request to {url} failed: {str(e)}"
    assert response.status_code == 200, f"Expected status code 200 but got {response.status_code}"
    try:
        data = response.json()
    except ValueError:
        assert False, "Response is not valid JSON"
    assert isinstance(data, list), f"Expected response to be a list but got {type(data)}"
    # Optionally, verify that each item in the list (if any) is a dictionary with expected keys
    for item in data:
        assert isinstance(item, dict), "Each transaksi item should be a dictionary"
        # keys to expect, based on transaction history and statuses idea (example)
        expected_keys = {"pelanggan_id", "tanggal_masuk", "tanggal_selesai", "status", "catatan", "layanan"}
        assert expected_keys.intersection(item.keys()), "Transaksi item should contain relevant keys"
        
test_get_all_transaksi_should_return_transaction_list()