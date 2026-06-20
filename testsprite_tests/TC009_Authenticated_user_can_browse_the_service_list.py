import asyncio
import re
from playwright import async_api
from playwright.async_api import expect

async def run_test():
    pw = None
    browser = None
    context = None

    try:
        # Start a Playwright session in asynchronous mode
        pw = await async_api.async_playwright().start()

        # Launch a Chromium browser in headless mode with custom arguments
        browser = await pw.chromium.launch(
            headless=True,
            args=[
                "--window-size=1280,720",
                "--disable-dev-shm-usage",
                "--ipc=host",
                "--single-process"
            ],
        )

        # Create a new browser context (like an incognito window)
        context = await browser.new_context()
        # Wider default timeout to match the agent's DOM-stability budget;
        # auto-waiting Playwright APIs (expect, locator.wait_for) inherit this.
        context.set_default_timeout(15000)

        # Open a new page in the browser context
        page = await context.new_page()

        # Interact with the page elements to simulate user flow
        # -> navigate
        await page.goto("http://localhost:8000")
        try:
            await page.wait_for_load_state("domcontentloaded", timeout=5000)
        except Exception:
            pass
        
        # -> Click the 'Login' link in the page header to open the login form.
        # Login link
        elem = page.get_by_role('link', name='Login', exact=True)
        await elem.click(timeout=10000)
        
        # -> Fill the Email field with admin@laundry.com, fill the Password field with admin123, and click the 'Log in' button to authenticate.
        # email email field
        elem = page.locator('[id="email"]')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("admin@laundry.com")
        
        # -> Fill the Email field with admin@laundry.com, fill the Password field with admin123, and click the 'Log in' button to authenticate.
        # password password field
        elem = page.locator('[id="password"]')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("admin123")
        
        # -> Fill the Email field with admin@laundry.com, fill the Password field with admin123, and click the 'Log in' button to authenticate.
        # Log in button
        elem = page.get_by_role('button', name='Log in', exact=True)
        await elem.click(timeout=10000)
        
        # -> Click the 'PENGATURAN' menu in the left sidebar to expand settings and reveal the service-related links (e.g., 'Layanan' or 'Tambah Layanan').
        # PENGATURAN
        elem = page.get_by_text('PENGATURAN', exact=True)
        await elem.click(timeout=10000)
        
        # -> Open the service area by clicking the 'Tambah Layanan' button in the 'Aksi Cepat' panel so the service list or service management page can be displayed and inspected.
        # Tambah Layanan link
        elem = page.get_by_role('link', name='Tambah Layanan', exact=True)
        await elem.click(timeout=10000)
        
        # -> Click the 'Layanan' link in the left sidebar to open the service list page so service names, units, and prices can be inspected.
        # Layanan link
        elem = page.get_by_role('link', name='Layanan', exact=True)
        await elem.click(timeout=10000)
        
        # --> Assertions to verify final state
        
        # --> Verify the service list is displayed
        # Assert: The service table header 'Nama Layanan' is visible.
        await expect(page.locator("xpath=/html/body/div/div[2]/main/div/div/div/div/div[2]/table/thead/tr").nth(0)).to_contain_text("Nama Layanan", timeout=15000), "The service table header 'Nama Layanan' is visible."
        # Assert: A service name 'Spray' is visible in the list.
        await expect(page.locator("xpath=/html/body/div/div[2]/main/div/div/div/div/div[2]/table/tbody/tr[1]/td[1]").nth(0)).to_have_text("Spray", timeout=15000), "A service name 'Spray' is visible in the list."
        # Assert: The unit 'PCS' is visible for the first service.
        await expect(page.locator("xpath=/html/body/div/div[2]/main/div/div/div/div/div[2]/table/tbody/tr[1]/td[2]").nth(0)).to_have_text("PCS", timeout=15000), "The unit 'PCS' is visible for the first service."
        # Assert: The price 'Rp 15.000' is visible for the first service.
        await expect(page.locator("xpath=/html/body/div/div[2]/main/div/div/div/div/div[2]/table/tbody/tr[1]/td[3]").nth(0)).to_have_text("Rp 15.000", timeout=15000), "The price 'Rp 15.000' is visible for the first service."
        
        # --> Verify service names, units, and prices are visible
        await page.locator("xpath=/html/body/div/div[2]/main/div/div/div/div/div[2]/table/tbody/tr[1]/td[1]").nth(0).scroll_into_view_if_needed()
        # Assert: The service name 'Spray' is visible.
        await expect(page.locator("xpath=/html/body/div/div[2]/main/div/div/div/div/div[2]/table/tbody/tr[1]/td[1]").nth(0)).to_be_visible(timeout=15000), "The service name 'Spray' is visible."
        await page.locator("xpath=/html/body/div/div[2]/main/div/div/div/div/div[2]/table/tbody/tr[1]/td[2]").nth(0).scroll_into_view_if_needed()
        # Assert: The unit 'PCS' for the first service is visible.
        await expect(page.locator("xpath=/html/body/div/div[2]/main/div/div/div/div/div[2]/table/tbody/tr[1]/td[2]").nth(0)).to_be_visible(timeout=15000), "The unit 'PCS' for the first service is visible."
        await page.locator("xpath=/html/body/div/div[2]/main/div/div/div/div/div[2]/table/tbody/tr[1]/td[3]").nth(0).scroll_into_view_if_needed()
        # Assert: The price 'Rp 15.000' for the first service is visible.
        await expect(page.locator("xpath=/html/body/div/div[2]/main/div/div/div/div/div[2]/table/tbody/tr[1]/td[3]").nth(0)).to_be_visible(timeout=15000), "The price 'Rp 15.000' for the first service is visible."
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    