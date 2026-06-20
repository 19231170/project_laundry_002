import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
ENDPOINT = "/api/v1/pelanggan"
TIMEOUT = 30
AUTH = HTTPBasicAuth("admin@laundry.com", "admin123")
HEADERS = {"Content-Type": "application/json"}


def post_pelanggan_with_invalid_data_should_return_validation_error():
    url = BASE_URL + ENDPOINT

    # Test case with missing or invalid required fields
    # Include all keys but with empty or invalid values to trigger validation errors
    invalid_payloads = [
        {"nama": "", "telepon": "", "alamat": "", "email": ""},  # all empty
        {"nama": "", "telepon": "0812345678", "alamat": "Jl Test", "email": "test@example.com"},  # nama empty
        {"nama": "Test User", "telepon": "", "alamat": "Jl Test", "email": "test@example.com"},  # telepon empty
        {"nama": "Test User", "telepon": "notaphone", "alamat": "Jl Test", "email": "test@example.com"},  # invalid telepon format
        {"nama": "Test User", "telepon": "0812345678", "alamat": "", "email": "test@example.com"},  # alamat empty
        {"nama": "Test User", "telepon": "0812345678", "alamat": "Jl Test", "email": "invalid-email"}  # invalid email
    ]

    for payload in invalid_payloads:
        try:
            response = requests.post(
                url,
                json=payload,
                headers=HEADERS,
                auth=AUTH,
                timeout=TIMEOUT
            )
        except requests.RequestException as e:
            assert False, f"Request failed: {e}"

        # Assert that the response status code is 400 for validation error
        assert response.status_code == 400, \
            f"Expected status code 400 for payload {payload}, got {response.status_code}"

        # Assert response contains validation error messages (usually in JSON)
        try:
            resp_json = response.json()
        except ValueError:
            assert False, "Response is not valid JSON"

        # Basic validation error structure check
        assert isinstance(resp_json, dict), f"Expected response JSON object for payload {payload}"

        # Expect some indication of validation errors in the response keys or messages
        validation_keys_found = any(
            key in resp_json for key in ["errors", "message", "validation", "error"]
        )
        assert validation_keys_found, f"Validation error keys not found in response for payload {payload}"


post_pelanggan_with_invalid_data_should_return_validation_error()
