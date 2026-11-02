const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

(async () => {
    const screenshotDir = path.join(__dirname, 'docs', 'redesign_screenshots');
    if (!fs.existsSync(screenshotDir)) {
        fs.mkdirSync(screenshotDir, { recursive: true });
    }

    const browser = await puppeteer.launch({
        headless: 'new',
        executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const pagesToCapture = [
        {
            url: 'http://127.0.0.1:8000/',
            name: '01_homepage_desktop_1440',
            fullpageName: '01_homepage_desktop_fullpage',
            width: 1440,
            height: 900
        },
        {
            url: 'http://127.0.0.1:8000/buy-produce',
            name: '02_buyer_marketplace_1440',
            width: 1440,
            height: 900
        },
        {
            url: 'http://127.0.0.1:8000/dashboard',
            name: '03_farmer_dashboard_1440',
            width: 1440,
            height: 900
        },
        {
            url: 'http://127.0.0.1:8000/sell-produce',
            name: '04_farmer_sell_produce_1440',
            width: 1440,
            height: 900
        },
        {
            url: 'http://127.0.0.1:8000/admin/produce',
            name: '05_admin_produce_hub_1440',
            width: 1440,
            height: 900
        },
        {
            url: 'http://127.0.0.1:8000/buyer/dashboard',
            name: '06_buyer_dashboard_1440',
            width: 1440,
            height: 900
        },
        {
            url: 'http://127.0.0.1:8000/solutions',
            name: '07_solutions_overview_1440',
            width: 1440,
            height: 900
        },
        {
            url: 'http://127.0.0.1:8000/ecosystem',
            name: '08_ecosystem_1440',
            width: 1440,
            height: 900
        },
        {
            url: 'http://127.0.0.1:8000/',
            name: '09_homepage_mobile_375',
            width: 375,
            height: 667
        }
    ];

    for (const item of pagesToCapture) {
        console.log(`Navigating to ${item.url} at ${item.width}x${item.height}...`);
        const page = await browser.newPage();
        await page.setViewport({ width: item.width, height: item.height, deviceScaleFactor: 1 });
        await page.goto(item.url, { waitUntil: 'networkidle2', timeout: 30000 });
        await new Promise(r => setTimeout(r, 600));

        const destPath = path.join(screenshotDir, `${item.name}.png`);
        await page.screenshot({ path: destPath, fullPage: false });
        console.log(`Saved viewport screenshot: ${destPath}`);

        if (item.fullpageName) {
            const fpPath = path.join(screenshotDir, `${item.fullpageName}.png`);
            await page.screenshot({ path: fpPath, fullPage: true });
            console.log(`Saved fullpage screenshot: ${fpPath}`);
        }

        await page.close();
    }

    await browser.close();
    console.log('--- ALL SCREENSHOTS CAPTURED SUCCESSFULLY ---');
})();
