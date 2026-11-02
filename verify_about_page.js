import puppeteer from 'puppeteer-core';
import fs from 'fs';
import path from 'path';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const screenshotDir = 'C:\\Users\\Dilkhush Labana\\.gemini\\antigravity-ide\\brain\\ef2358fc-6697-464f-85d9-384de7b36b28\\screenshots';

async function run() {
    console.log('Launching Chrome to visually verify redesigned About Us page...');
    const browser = await puppeteer.launch({
        executablePath: chromePath,
        headless: 'new',
        args: ['--no-sandbox', '--disable-gpu']
    });

    // 1. Desktop 1440px
    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 900 });
    await page.goto('http://127.0.0.1:8000/about', { waitUntil: 'networkidle2' });

    // Desktop Hero & Intro
    await page.screenshot({ 
        path: path.join(screenshotDir, 'about_page_desktop_hero.png'),
        clip: { x: 0, y: 0, width: 1440, height: 950 }
    });
    console.log('Captured about_page_desktop_hero.png');

    // Desktop Full Page
    await page.screenshot({ 
        path: path.join(screenshotDir, 'about_page_desktop_full.png'),
        fullPage: true
    });
    console.log('Captured about_page_desktop_full.png');

    // 2. Mobile 375px
    const mobilePage = await browser.newPage();
    await mobilePage.setViewport({ width: 375, height: 812, isMobile: true });
    await mobilePage.goto('http://127.0.0.1:8000/about', { waitUntil: 'networkidle2' });

    // Mobile Top View
    await mobilePage.screenshot({ 
        path: path.join(screenshotDir, 'about_page_mobile_top.png'),
        clip: { x: 0, y: 0, width: 375, height: 850 }
    });
    console.log('Captured about_page_mobile_top.png');

    // Mobile Full Page
    await mobilePage.screenshot({ 
        path: path.join(screenshotDir, 'about_page_mobile_full.png'),
        fullPage: true
    });
    console.log('Captured about_page_mobile_full.png');

    await browser.close();
    console.log('Verification completed successfully.');
}

run().catch(err => {
    console.error('Error during verification:', err);
    process.exit(1);
});
