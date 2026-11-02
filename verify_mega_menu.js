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

    // 1. Desktop 1440px - Hover on Farmer Services
    console.log('Testing 1440px desktop...');
    await page.setViewport({ width: 1440, height: 900 });
    await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });

    // Hover on Farmer Services
    console.log('Hovering on Farmer Services mega menu...');
    await page.hover('#menu-btn-farmer-services');
    await new Promise(r => setTimeout(r, 400));
    await page.screenshot({ path: path.join(screenshotDir, 'mega_menu_farmer_services.png') });

    // Hover on Crops
    console.log('Hovering on Crops mega menu...');
    await page.hover('#menu-btn-crops');
    await new Promise(r => setTimeout(r, 400));
    await page.screenshot({ path: path.join(screenshotDir, 'mega_menu_crops.png') });

    // Hover on Agri Products
    console.log('Hovering on Agri Products mega menu...');
    await page.hover('#menu-btn-agri-products');
    await new Promise(r => setTimeout(r, 400));
    await page.screenshot({ path: path.join(screenshotDir, 'mega_menu_agri_products.png') });

    // Hover on Manufacturers
    console.log('Hovering on Manufacturers mega menu...');
    await page.hover('#menu-btn-manufacturers');
    await new Promise(r => setTimeout(r, 400));
    await page.screenshot({ path: path.join(screenshotDir, 'mega_menu_manufacturers.png') });

    // Test Escape key behavior
    console.log('Testing Escape key closing...');
    await page.keyboard.press('Escape');
    await new Promise(r => setTimeout(r, 300));
    const isAnyOpenAfterEscape = await page.evaluate(() => {
        return document.querySelectorAll('.mega-menu-item.is-open').length > 0;
    });
    console.log('Any mega menu open after Escape?', isAnyOpenAfterEscape);

    // Test Tablet 768px
    console.log('Testing 768px tablet...');
    await page.setViewport({ width: 768, height: 1024 });
    await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
    // Click mobile drawer
    await page.click('#mobile-drawer-btn');
    await new Promise(r => setTimeout(r, 300));
    await page.screenshot({ path: path.join(screenshotDir, 'mobile_drawer_768.png') });

    // Test Mobile 375px with accordion expanded
    console.log('Testing 375px mobile with accordion...');
    await page.setViewport({ width: 375, height: 812 });
    await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
    await page.click('#mobile-drawer-btn');
    await new Promise(r => setTimeout(r, 300));
    
    // Expand Farmer Services accordion in mobile
    const accordionBtn = await page.$('.mobile-accordion-trigger');
    if (accordionBtn) {
        await accordionBtn.click();
        await new Promise(r => setTimeout(r, 300));
    }
    await page.screenshot({ path: path.join(screenshotDir, 'mobile_drawer_375_accordion.png') });

    // Test Mobile 320px
    console.log('Testing 320px mobile...');
    await page.setViewport({ width: 320, height: 640 });
    await page.screenshot({ path: path.join(screenshotDir, 'mobile_drawer_320.png') });

    // Test Ultra-wide 4320px
    console.log('Testing 4320px ultra-wide...');
    await page.setViewport({ width: 4320, height: 1800 });
    await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
    await page.hover('#menu-btn-farmer-services');
    await new Promise(r => setTimeout(r, 400));
    await page.screenshot({ path: path.join(screenshotDir, 'ultra_wide_4320.png') });

    // Viewport overflow check across all sizes
    console.log('Checking horizontal overflow across viewports...');
    const viewports = [320, 375, 768, 1440, 1920, 4320];
    const overflowResults = {};
    for (const width of viewports) {
        await page.setViewport({ width, height: 900 });
        await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
        const hasOverflow = await page.evaluate(() => {
            return document.documentElement.scrollWidth > window.innerWidth;
        });
        overflowResults[width] = hasOverflow;
    }
    console.log('Overflow results (should all be false):', overflowResults);

    await browser.close();
    console.log('Verification completed successfully!');
}

run().catch(err => {
    console.error('Error during verification:', err);
    process.exit(1);
});
