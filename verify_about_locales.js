import puppeteer from 'puppeteer-core';
import fs from 'fs';
import path from 'path';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const screenshotDir = 'C:\\Users\\Dilkhush Labana\\.gemini\\antigravity-ide\\brain\\ef2358fc-6697-464f-85d9-384de7b36b28\\screenshots';

async function run() {
    console.log('Testing About page across EN, HI, GU locales and capturing verification screenshots...');
    const browser = await puppeteer.launch({
        executablePath: chromePath,
        headless: 'new',
        args: ['--no-sandbox', '--disable-gpu']
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 900 });

    // 1. English Desktop
    await page.goto('http://127.0.0.1:8000/locale/en', { waitUntil: 'networkidle2' });
    await page.goto('http://127.0.0.1:8000/about', { waitUntil: 'networkidle2' });
    await page.screenshot({ 
        path: path.join(screenshotDir, 'about_en_desktop_hero.png'),
        clip: { x: 0, y: 0, width: 1440, height: 950 }
    });
    console.log('Captured about_en_desktop_hero.png');

    // 2. Hindi Desktop
    await page.goto('http://127.0.0.1:8000/locale/hi', { waitUntil: 'networkidle2' });
    await page.goto('http://127.0.0.1:8000/about', { waitUntil: 'networkidle2' });
    await page.screenshot({ 
        path: path.join(screenshotDir, 'about_hi_desktop_hero.png'),
        clip: { x: 0, y: 0, width: 1440, height: 950 }
    });
    console.log('Captured about_hi_desktop_hero.png');

    // 3. Gujarati Desktop
    await page.goto('http://127.0.0.1:8000/locale/gu', { waitUntil: 'networkidle2' });
    await page.goto('http://127.0.0.1:8000/about', { waitUntil: 'networkidle2' });
    await page.screenshot({ 
        path: path.join(screenshotDir, 'about_gu_desktop_hero.png'),
        clip: { x: 0, y: 0, width: 1440, height: 950 }
    });
    console.log('Captured about_gu_desktop_hero.png');

    // Reset back to English
    await page.goto('http://127.0.0.1:8000/locale/en', { waitUntil: 'networkidle2' });
    await page.goto('http://127.0.0.1:8000/about', { waitUntil: 'networkidle2' });

    // 4. English Mobile (375x812)
    const mobilePage = await browser.newPage();
    await mobilePage.setViewport({ width: 375, height: 812, isMobile: true });
    await mobilePage.goto('http://127.0.0.1:8000/about', { waitUntil: 'networkidle2' });
    await mobilePage.screenshot({ 
        path: path.join(screenshotDir, 'about_en_mobile_hero.png'),
        clip: { x: 0, y: 0, width: 375, height: 850 }
    });
    console.log('Captured about_en_mobile_hero.png');

    await mobilePage.screenshot({ 
        path: path.join(screenshotDir, 'about_en_mobile_full.png'),
        fullPage: true
    });
    console.log('Captured about_en_mobile_full.png');

    await browser.close();
    console.log('All locale and viewport verifications completed successfully.');
}

run().catch(err => {
    console.error('Error:', err);
    process.exit(1);
});
