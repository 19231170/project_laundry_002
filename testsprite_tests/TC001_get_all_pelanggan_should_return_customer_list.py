import requests

def test_get_all_pelanggan_should_return_customer_list():
    base_url = "http://localhost:8000"
    endpoint = "/api/v1/pelanggan"
    url = base_url + endpoint
    headers = {
        "Accept": "application/json"
    }
    try:
        response = requests.get(url, headers=headers, timeout=30)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        assert False, f"Request failed: {e}"
    assert response.status_code == 200
    try:
        data = response.json()
    except ValueError:
        assert False, "Response is not valid JSON"
    assert isinstance(data, list), f"Expected list but got {type(data)}"

test_get_all_pelanggan_should_return_customer_list()