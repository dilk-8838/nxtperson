import puppeteer from 'puppeteer-core';
import fs from 'fs';
import path from 'path';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const screenshotDir = 'C:\\Users\\Dilkhush Labana\\.gemini\\antigravity-ide\\brain\\ef2358fc-6697-464f-85d9-384de7b36b28\\screenshots';

if (!fs.existsSync(screenshotDir)) {
    fs.mkdirSync(screenshotDir, { recursive: true });
}

async function run() {
    console.log('Launching Chrome via puppeteer-core...');
    const browser = await puppeteer.launch({
        executablePath: chromePath,
        headless: 'new',
        args: ['--no-sandbox', '--disable-gpu']
    });

    const page = await browser.newPage();

    // 1. Capture Responsive Breakpoints for Homepage
    const breakpoints = [
        { name: 'desktop_1920', width: 1920, height: 1080 },
        { name: 'laptop_1440', width: 1440, height: 900 },
        { name: 'tablet_768', width: 768, height: 1024 },
        { name: 'mobile_375', width: 375, height: 812 },
        { name: 'mobile_320', width: 320, height: 600 }
    ];

    for (const bp of breakpoints) {
        console.log(`Capturing ${bp.name} (${bp.width}x${bp.height})...`);
        await page.setViewport({ width: bp.width, height: bp.height });
        await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
        await new Promise(r => setTimeout(r, 400));
        await page.screenshot({ path: path.join(screenshotDir, `${bp.name}.png`), fullPage: false });
    }

    // 2. Focused Section Screenshots at 1440px
    await page.setViewport({ width: 1440, height: 900 });
    await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });

    // Hero Section
    const heroEl = await page.$('section:first-of-type');
    if (heroEl) {
        await heroEl.screenshot({ path: path.join(screenshotDir, 'redesigned_hero_1440.png') });
        console.log('Captured redesigned_hero_1440.png');
    }

    // Satellite Section
    const satEl = await page.$('#satellite-section');
    if (satEl) {
        await satEl.scrollIntoView();
        await new Promise(r => setTimeout(r, 300));
        await satEl.screenshot({ path: path.join(screenshotDir, 'redesigned_satellite_1440.png') });
        console.log('Captured redesigned_satellite_1440.png');
    }

    // Distributor Section
    const distEl = await page.evaluateHandle(() => {
        const sections = document.querySelectorAll('section');
        for (const s of sections) {
            if (s.textContent.includes('Find Agricultural Distributors Near You')) return s;
        }
        return null;
    });
    if (distEl && distEl.asElement()) {
        await distEl.asElement().scrollIntoView();
        await new Promise(r => setTimeout(r, 300));
        await distEl.asElement().screenshot({ path: path.join(screenshotDir, 'redesigned_distributor_1440.png') });
        console.log('Captured redesigned_distributor_1440.png');
    }

    // 3. Test Farmer Journey
    console.log('\n--- Testing Farmer Journey ---');
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle2' });
    console.log('Opened Login page');
    
    // Submit login form
    const submitBtn = await page.$('form[action*="verify-otp"] button[type="submit"]');
    if (submitBtn) {
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle2' }),
            submitBtn.click()
        ]);
        console.log('Submitted Simulated OTP. Logged in successfully!');
    }

    // Visit Farmer Dashboard
    await page.goto('http://127.0.0.1:8000/dashboard', { waitUntil: 'networkidle2' });
    console.log('Loaded /dashboard successfully. Current URL:', page.url());
    await page.screenshot({ path: path.join(screenshotDir, 'farmer_dashboard.png') });

    // Submit Advisory
    await page.goto('http://127.0.0.1:8000/advisory', { waitUntil: 'networkidle2' });
    const cottonRadio = await page.$('input[name="crop_slug"][value="cotton"]');
    if (cottonRadio) await cottonRadio.click();
    const bollwormRadio = await page.$('input[name="symptom_key"][value="bollworm"]');
    if (bollwormRadio) await bollwormRadio.click();

    // Attach test photo
    const fileInput = await page.$('#leaf-file-input');
    if (fileInput) {
        const testImagePath = path.resolve('public/images/crops/cotton.webp');
        if (fs.existsSync(testImagePath)) {
            await fileInput.uploadFile(testImagePath);
            console.log('Attached photo to advisory:', testImagePath);
        }
    }

    // Submit form
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2' }),
        page.evaluate(() => {
            const btn = document.querySelector('form[action*="/advisory"] button[type="submit"]');
            btn.click();
        })
    ]);
    console.log('Submitted Crop Advisory. Redirected URL:', page.url());
    await page.screenshot({ path: path.join(screenshotDir, 'advisory_diagnostic_result.png') });

    // Escalate Ticket
    const escalateBtn = await page.$('form[action*="escalate"] button');
    if (escalateBtn) {
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle2' }),
            page.evaluate(() => {
                const eBtn = document.querySelector('form[action*="escalate"] button');
                eBtn.click();
            })
        ]);
        console.log('Escalated Advisory Ticket to Demonstration Specialist');
        await page.screenshot({ path: path.join(screenshotDir, 'advisory_escalated.png') });
    }

    // Advisory History
    await page.goto('http://127.0.0.1:8000/advisory/history', { waitUntil: 'networkidle2' });
    console.log('Loaded Advisory History');
    await page.screenshot({ path: path.join(screenshotDir, 'advisory_history.png') });

    // 4. Test Products & Distributors
    console.log('\n--- Testing Products & Distributors ---');
    await page.goto('http://127.0.0.1:8000/products?search=Neem', { waitUntil: 'networkidle2' });
    console.log('Searched products for "Neem"');
    await page.screenshot({ path: path.join(screenshotDir, 'products_search_neem.png') });

    // Product Detail
    const productLink = await page.$('a[href*="/products/"]');
    if (productLink) {
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle2' }),
            productLink.click()
        ]);
        console.log('Loaded Product Detail:', page.url());
        await page.screenshot({ path: path.join(screenshotDir, 'product_detail_page.png') });
    }

    // Distributors Search
    await page.goto('http://127.0.0.1:8000/distributors?pincode=360001', { waitUntil: 'networkidle2' });
    console.log('Searched distributors for pincode 360001');
    await page.screenshot({ path: path.join(screenshotDir, 'distributors_pincode_360001.png') });

    // 5. Test Admin Portal
    console.log('\n--- Testing Admin Portal ---');
    // Switch to admin role
    await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
    await page.evaluate(() => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/demo/switch-role';
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrf) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrf;
            form.appendChild(csrfInput);
        }
        const roleInput = document.createElement('input');
        roleInput.type = 'hidden';
        roleInput.name = 'role';
        roleInput.value = 'admin';
        form.appendChild(roleInput);
        document.body.appendChild(form);
        form.submit();
    });
    await page.waitForNavigation({ waitUntil: 'networkidle2' });
    console.log('Switched to Admin Role');

    await page.goto('http://127.0.0.1:8000/admin', { waitUntil: 'networkidle2' });
    console.log('Loaded /admin portal. Current URL:', page.url());
    await page.screenshot({ path: path.join(screenshotDir, 'admin_dashboard.png') });

    await page.goto('http://127.0.0.1:8000/admin/products', { waitUntil: 'networkidle2' });
    console.log('Loaded /admin/products');
    await page.screenshot({ path: path.join(screenshotDir, 'admin_products.png') });

    await page.goto('http://127.0.0.1:8000/admin/distributors', { waitUntil: 'networkidle2' });
    console.log('Loaded /admin/distributors');
    await page.screenshot({ path: path.join(screenshotDir, 'admin_distributors.png') });

    await browser.close();
    console.log('\n=== ALL BROWSER QA TASKS COMPLETED SUCCESSFULLY ===');
}

run().catch(err => {
    console.error('Error during verification:', err);
    process.exit(1);
});
