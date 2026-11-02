const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const outDir = path.join(__dirname, 'docs', 'baseline_screenshots');

if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, { recursive: true });
}

async function capture() {
    console.log('Launching headless Chrome for baseline screenshot capture...');
    const browser = await puppeteer.launch({
        executablePath: chromePath,
        headless: 'new',
        args: ['--no-sandbox', '--disable-gpu']
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 900 });

    const publicPages = [
        { name: '01_public_homepage_1440.png', url: 'http://127.0.0.1:8000/' },
        { name: '01_public_homepage_mobile_375.png', url: 'http://127.0.0.1:8000/', width: 375, height: 812 },
        { name: '02_public_about_1440.png', url: 'http://127.0.0.1:8000/about' },
        { name: '03_public_solutions_overview_1440.png', url: 'http://127.0.0.1:8000/solutions' },
        { name: '04_public_quality_grading_1440.png', url: 'http://127.0.0.1:8000/solutions/quality-grading' },
        { name: '05_public_marketplace_overview_1440.png', url: 'http://127.0.0.1:8000/marketplace' },
        { name: '06_public_ecosystem_1440.png', url: 'http://127.0.0.1:8000/ecosystem' },
        { name: '07_public_roadmap_1440.png', url: 'http://127.0.0.1:8000/roadmap' },
        { name: '08_public_crops_cotton_1440.png', url: 'http://127.0.0.1:8000/crops/cotton' },
        { name: '09_public_products_catalog_1440.png', url: 'http://127.0.0.1:8000/products' },
        { name: '10_public_distributors_1440.png', url: 'http://127.0.0.1:8000/distributors' },
        { name: '11_public_mandi_1440.png', url: 'http://127.0.0.1:8000/mandi' },
        { name: '12_public_weather_1440.png', url: 'http://127.0.0.1:8000/weather' },
        { name: '13_public_satellite_1440.png', url: 'http://127.0.0.1:8000/satellite' },
        { name: '14_public_login_1440.png', url: 'http://127.0.0.1:8000/login' },
    ];

    for (const item of publicPages) {
        try {
            await page.setViewport({ width: item.width || 1440, height: item.height || 900 });
            await page.goto(item.url, { waitUntil: 'networkidle2', timeout: 15000 });
            await new Promise(r => setTimeout(r, 400));
            await page.screenshot({ path: path.join(outDir, item.name), fullPage: false });
            console.log(`Captured: ${item.name}`);
        } catch (e) {
            console.error(`Failed ${item.name}: ${e.message}`);
        }
    }

    // Role Switch helper
    async function switchRole(roleName) {
        await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle2' });
        await page.evaluate((r) => {
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
            roleInput.value = r;
            form.appendChild(roleInput);
            document.body.appendChild(form);
            form.submit();
        }, roleName);
        await page.waitForNavigation({ waitUntil: 'networkidle2' });
    }

    // 2. Farmer Portal Baseline
    console.log('Switching to Farmer perspective...');
    await switchRole('farmer');
    const farmerPages = [
        { name: '15_farmer_dashboard_1440.png', url: 'http://127.0.0.1:8000/dashboard' },
        { name: '16_farmer_sell_produce_1440.png', url: 'http://127.0.0.1:8000/sell-produce' },
        { name: '17_farmer_lots_index_1440.png', url: 'http://127.0.0.1:8000/farmer/lots' },
        { name: '18_farmer_advisory_hub_1440.png', url: 'http://127.0.0.1:8000/advisory' },
    ];
    for (const item of farmerPages) {
        try {
            await page.setViewport({ width: 1440, height: 900 });
            await page.goto(item.url, { waitUntil: 'networkidle2', timeout: 15000 });
            await new Promise(r => setTimeout(r, 400));
            await page.screenshot({ path: path.join(outDir, item.name), fullPage: false });
            console.log(`Captured: ${item.name}`);
        } catch (e) {
            console.error(`Failed ${item.name}: ${e.message}`);
        }
    }

    // 3. Buyer Portal Baseline
    console.log('Switching to Buyer perspective...');
    await switchRole('buyer');
    const buyerPages = [
        { name: '19_buyer_marketplace_1440.png', url: 'http://127.0.0.1:8000/buy-produce' },
        { name: '20_buyer_dashboard_1440.png', url: 'http://127.0.0.1:8000/buyer/dashboard' },
        { name: '21_buyer_notifications_1440.png', url: 'http://127.0.0.1:8000/buyer/notifications' },
    ];
    for (const item of buyerPages) {
        try {
            await page.setViewport({ width: 1440, height: 900 });
            await page.goto(item.url, { waitUntil: 'networkidle2', timeout: 15000 });
            await new Promise(r => setTimeout(r, 400));
            await page.screenshot({ path: path.join(outDir, item.name), fullPage: false });
            console.log(`Captured: ${item.name}`);
        } catch (e) {
            console.error(`Failed ${item.name}: ${e.message}`);
        }
    }

    // 4. Admin Portal Baseline
    console.log('Switching to Admin perspective...');
    await switchRole('admin');
    const adminPages = [
        { name: '22_admin_dashboard_1440.png', url: 'http://127.0.0.1:8000/admin' },
        { name: '23_admin_produce_hub_1440.png', url: 'http://127.0.0.1:8000/admin/produce' },
        { name: '24_admin_products_1440.png', url: 'http://127.0.0.1:8000/admin/products' },
        { name: '25_admin_distributors_1440.png', url: 'http://127.0.0.1:8000/admin/distributors' },
        { name: '26_admin_reservations_1440.png', url: 'http://127.0.0.1:8000/admin/produce-reservations' },
    ];
    for (const item of adminPages) {
        try {
            await page.setViewport({ width: 1440, height: 900 });
            await page.goto(item.url, { waitUntil: 'networkidle2', timeout: 15000 });
            await new Promise(r => setTimeout(r, 400));
            await page.screenshot({ path: path.join(outDir, item.name), fullPage: false });
            console.log(`Captured: ${item.name}`);
        } catch (e) {
            console.error(`Failed ${item.name}: ${e.message}`);
        }
    }

    await browser.close();
    console.log('All 26 baseline screenshots captured into ' + outDir);
}

capture().catch(err => {
    console.error('Fatal capture error:', err);
    process.exit(1);
});
