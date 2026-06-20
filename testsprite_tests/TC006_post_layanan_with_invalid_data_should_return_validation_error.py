import requests

BASE_URL = "http://localhost:8000"
ENDPOINT = "/api/v1/layanan"
TIMEOUT = 30
HEADERS = {'Content-Type': 'application/json'}

def post_layanan_with_invalid_data_should_return_validation_error():
    url = BASE_URL + ENDPOINT

    invalid_payloads = [
        # Invalid harga (string instead of number)
        {
            "nama_layanan": "Cuci Kering",
            "satuan": "kg",
            "harga": "not_a_number",
            "deskripsi": "Layanan cuci dengan harga tidak valid"
        },
        # Missing harga field
        {
            "nama_layanan": "Cuci Basah",
            "satuan": "kg",
            "deskripsi": "Layanan cuci tanpa harga"
        },
        # Missing nama_layanan field
        {
            "satuan": "pcs",
            "harga": 5000,
            "deskripsi": "Layanan tanpa nama"
        },
        # Missing satuan field
        {
            "nama_layanan": "Setrika",
            "harga": 7000,
            "deskripsi": "Layanan tanpa satuan"
        },
        # Empty body
        {}
    ]

    for payload in invalid_payloads:
        try:
            response = requests.post(
                url,
                headers=HEADERS,
                json=payload,
                timeout=TIMEOUT
            )
            assert response.status_code == 400, f"Expected status code 400, got {response.status_code} for payload {payload}"
            # Validate response contains validation error indication
            json_resp = response.json()
            assert ("errors" in json_resp) or ("message" in json_resp), "Response should contain validation error details"
        except requests.RequestException as e:
            assert False, f"Request failed: {e}"

post_layanan_with_invalid_data_should_return_validation_error()