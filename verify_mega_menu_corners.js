import puppeteer from 'puppeteer-core';
import fs from 'fs';
import path from 'path';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const screenshotDir = 'C:\\Users\\Dilkhush Labana\\.gemini\\antigravity-ide\\brain\\ef2358fc-6697-464f-85d9-384de7b36b28\\screenshots';

async function run() {
    console.log('Launching Chrome via puppeteer-core to verify mega-menu rounded corners...');
    const browser = await puppeteer.launch({
        executablePath: chromePath,
        headless: 'new',
        args: ['--no-sandbox', '--disable-gpu']
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 900 });
    await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });

    const menus = [
        { id: '#menu-btn-farmer-services', name: 'mega_menu_rounded_farmer_services.png' },
        { id: '#menu-btn-crops', name: 'mega_menu_rounded_crops.png' },
        { id: '#menu-btn-agri-products', name: 'mega_menu_rounded_agri_products.png' },
        { id: '#menu-btn-manufacturers', name: 'mega_menu_rounded_manufacturers.png' }
    ];

    for (const m of menus) {
        console.log(`Hovering over ${m.id}...`);
        await page.hover(m.id);
        await new Promise(r => setTimeout(r, 400));
        await page.screenshot({ path: path.join(screenshotDir, m.name) });
        console.log(`Captured ${m.name}`);
    }

    await browser.close();
    console.log('Completed mega menu rounded corner verification.');
}

run().catch(err => {
    console.error('Error:', err);
    process.exit(1);
});
