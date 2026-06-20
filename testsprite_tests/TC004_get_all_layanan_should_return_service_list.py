import requests

def test_get_all_layanan_should_return_service_list():
    base_url = "http://localhost:8000"
    endpoint = "/api/v1/layanan"
    url = base_url + endpoint
    headers = {
        "Accept": "application/json"
    }
    try:
        response = requests.get(url, headers=headers, timeout=30)
        assert response.status_code == 200, f"Expected status code 200 but got {response.status_code}"
        layanan_list = response.json()
        assert isinstance(layanan_list, list), "Response JSON is not an array"
    except requests.RequestException as e:
        assert False, f"Request failed: {e}"

test_get_all_layanan_should_return_service_list()