const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs');

(async () => {
    const screenshotDir = path.join(__dirname, 'docs', 'screenshots');
    if (!fs.existsSync(screenshotDir)) {
        fs.mkdirSync(screenshotDir, { recursive: true });
    }

    const browser = await puppeteer.launch({
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const pagesToCapture = [
        { url: 'http://127.0.0.1:8000', name: '01_homepage_desktop_1440.png', width: 1440, height: 900 },
        { url: 'http://127.0.0.1:8000/solutions', name: '02_solutions_overview_desktop_1440.png', width: 1440, height: 900 },
        { url: 'http://127.0.0.1:8000/solutions/quality-grading', name: '03_quality_grading_pillar1_1440.png', width: 1440, height: 900 },
        { url: 'http://127.0.0.1:8000/marketplace', name: '04_marketplace_overview_1440.png', width: 1440, height: 900 },
        { url: 'http://127.0.0.1:8000/ecosystem', name: '05_ecosystem_network_1440.png', width: 1440, height: 900 },
        { url: 'http://127.0.0.1:8000/roadmap', name: '06_roadmap_timeline_1440.png', width: 1440, height: 900 },
        { url: 'http://127.0.0.1:8000/about', name: '07_about_us_desktop_1440.png', width: 1440, height: 900 },
        { url: 'http://127.0.0.1:8000', name: '08_homepage_mobile_375.png', width: 375, height: 812 },
        { url: 'http://127.0.0.1:8000/about', name: '09_about_us_mobile_375.png', width: 375, height: 812 },
        { url: 'http://127.0.0.1:8000/locale/gu', name: '10_homepage_gujarati_1440.png', width: 1440, height: 900, followTo: 'http://127.0.0.1:8000' },
    ];

    for (const item of pagesToCapture) {
        const page = await browser.newPage();
        await page.setViewport({ width: item.width, height: item.height, deviceScaleFactor: 1 });
        
        try {
            await page.goto(item.url, { waitUntil: 'networkidle2', timeout: 15000 });
            if (item.followTo) {
                await page.goto(item.followTo, { waitUntil: 'networkidle2', timeout: 15000 });
            }
            // Small pause for rendering
            await new Promise(r => setTimeout(r, 800));

            const filePath = path.join(screenshotDir, item.name);
            await page.screenshot({ path: filePath, fullPage: false });
            console.log(`Saved screenshot: ${item.name}`);
        } catch (err) {
            console.error(`Error capturing ${item.name}:`, err.message);
        } finally {
            await page.close();
        }
    }

    // Capture Mega Menu Hover state on Desktop
    {
        const page = await browser.newPage();
        await page.setViewport({ width: 1440, height: 900 });
        await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
        await page.hover('#menu-btn-solutions');
        await new Promise(r => setTimeout(r, 600));
        await page.screenshot({ path: path.join(screenshotDir, '11_solutions_megamenu_hover_1440.png') });
        console.log('Saved screenshot: 11_solutions_megamenu_hover_1440.png');
        await page.close();
    }

    await browser.close();
    console.log('All verification screenshots captured successfully!');
})();
