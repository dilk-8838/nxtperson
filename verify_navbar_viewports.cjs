const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

(async () => {
    const screenshotDir = path.join(__dirname, 'docs', 'screenshots', 'navbar');
    if (!fs.existsSync(screenshotDir)) {
        fs.mkdirSync(screenshotDir, { recursive: true });
    }

    const browser = await puppeteer.launch({
        headless: 'new',
        executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const viewports = [
        { width: 320, height: 568, name: '320px_mobile_small' },
        { width: 375, height: 667, name: '375px_mobile_standard' },
        { width: 768, height: 1024, name: '768px_tablet' },
        { width: 1024, height: 768, name: '1024px_laptop_small' },
        { width: 1280, height: 800, name: '1280px_desktop_standard' },
        { width: 1366, height: 768, name: '1366px_desktop_common' },
        { width: 1440, height: 900, name: '1440px_desktop_large' },
        { width: 1920, height: 1080, name: '1920px_desktop_fullhd' },
    ];

    console.log('--- STARTING NAVBAR RESPONSIVE VIEWPORT TESTING ---');

    for (const vp of viewports) {
        const page = await browser.newPage();
        await page.setViewport({ width: vp.width, height: vp.height, deviceScaleFactor: 1 });

        await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
        await new Promise(r => setTimeout(r, 400));

        // Evaluate overflow and CTA visibility
        const layoutMetrics = await page.evaluate(() => {
            const docWidth = document.documentElement.scrollWidth;
            const winWidth = window.innerWidth;
            const cta = document.getElementById('header-primary-cta');
            const ctaRect = cta ? cta.getBoundingClientRect() : null;
            const logo = document.querySelector('a[aria-label="NxtPerson Home"]');
            const logoRect = logo ? logo.getBoundingClientRect() : null;

            return {
                windowWidth: winWidth,
                docWidth: docWidth,
                hasHorizontalOverflow: docWidth > winWidth,
                ctaVisible: !!ctaRect && ctaRect.width > 0 && ctaRect.height > 0,
                ctaFullyInViewport: !!ctaRect && ctaRect.left >= 0 && ctaRect.right <= winWidth,
                ctaRight: ctaRect ? ctaRect.right : null,
                ctaLeft: ctaRect ? ctaRect.left : null,
                logoFullyInViewport: !!logoRect && logoRect.left >= 0 && logoRect.right <= winWidth,
            };
        });

        console.log(`[Viewport ${vp.width}x${vp.height} (${vp.name})]:`, {
            overflow: layoutMetrics.hasHorizontalOverflow ? 'FAIL: OVERFLOW' : 'PASS: NO OVERFLOW',
            docWidth: layoutMetrics.docWidth,
            winWidth: layoutMetrics.windowWidth,
            ctaVisible: layoutMetrics.ctaVisible ? 'PASS' : 'FAIL',
            ctaInViewport: layoutMetrics.ctaFullyInViewport ? 'PASS' : 'FAIL: CLIPPED',
            ctaRight: layoutMetrics.ctaRight,
        });

        // Screenshot
        const filePath = path.join(screenshotDir, `navbar_${vp.name}.png`);
        await page.screenshot({ path: filePath, fullPage: false });

        // If tablet or mobile, test opening mobile drawer
        if (vp.width < 1024) {
            const drawerBtn = await page.$('#mobile-drawer-btn');
            if (drawerBtn) {
                await drawerBtn.click();
                await new Promise(r => setTimeout(r, 400));
                await page.screenshot({ path: path.join(screenshotDir, `navbar_${vp.name}_drawer_open.png`), fullPage: false });
            }
        }

        await page.close();
    }

    // Test Mega Menu Dropdown in Viewport at 1440px
    {
        const page = await browser.newPage();
        await page.setViewport({ width: 1440, height: 900 });
        await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
        await page.hover('#menu-btn-marketplace');
        await new Promise(r => setTimeout(r, 500));
        
        const marketplacePanel = await page.evaluate(() => {
            const panel = document.getElementById('mega-panel-marketplace');
            const rect = panel ? panel.getBoundingClientRect() : null;
            return {
                exists: !!panel,
                withinViewport: rect && rect.left >= 0 && rect.right <= window.innerWidth,
                rect: rect
            };
        });

        console.log('[Marketplace Mega Menu Dropdown]:', marketplacePanel.withinViewport ? 'PASS: IN VIEWPORT' : 'FAIL: CLIPPED');
        await page.screenshot({ path: path.join(screenshotDir, 'navbar_1440px_marketplace_megamenu.png') });
        await page.close();
    }

    // Test Hindi & Gujarati Localizations
    const localizations = [
        { locale: 'hi', name: 'hindi' },
        { locale: 'gu', name: 'gujarati' },
    ];

    for (const loc of localizations) {
        const page = await browser.newPage();
        await page.setViewport({ width: 1280, height: 800 });
        await page.goto(`http://127.0.0.1:8000/locale/${loc.locale}`, { waitUntil: 'networkidle2' });
        await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
        await new Promise(r => setTimeout(r, 400));

        const locMetrics = await page.evaluate(() => {
            const docWidth = document.documentElement.scrollWidth;
            const winWidth = window.innerWidth;
            const cta = document.getElementById('header-primary-cta');
            const ctaRect = cta ? cta.getBoundingClientRect() : null;
            return {
                hasHorizontalOverflow: docWidth > winWidth,
                ctaFullyInViewport: !!ctaRect && ctaRect.left >= 0 && ctaRect.right <= winWidth,
                ctaText: cta ? cta.innerText.trim() : null,
            };
        });

        console.log(`[Locale ${loc.name.toUpperCase()} at 1280px]:`, {
            overflow: locMetrics.hasHorizontalOverflow ? 'FAIL: OVERFLOW' : 'PASS: NO OVERFLOW',
            ctaInViewport: locMetrics.ctaFullyInViewport ? 'PASS' : 'FAIL',
            ctaText: locMetrics.ctaText
        });

        await page.screenshot({ path: path.join(screenshotDir, `navbar_1280px_${loc.name}.png`) });
        await page.close();
    }

    // Test Role-Aware CTA for Buyer & Admin
    {
        // Buyer persona
        const page = await browser.newPage();
        await page.setViewport({ width: 1440, height: 900 });
        await page.goto('http://127.0.0.1:8000/locale/en', { waitUntil: 'networkidle2' });
        // Switch to buyer role
        await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle2' }),
            page.select('select[name="role"]', 'buyer')
        ]);
        await new Promise(r => setTimeout(r, 400));

        const buyerCta = await page.evaluate(() => {
            const cta = document.getElementById('header-primary-cta');
            return {
                text: cta ? cta.innerText.trim() : null,
                href: cta ? cta.getAttribute('href') : null,
            };
        });
        console.log('[Role: Buyer CTA Check]:', buyerCta);
        await page.screenshot({ path: path.join(screenshotDir, 'navbar_1440px_role_buyer.png') });

        // Switch to admin role
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle2' }),
            page.select('select[name="role"]', 'admin')
        ]);
        await new Promise(r => setTimeout(r, 400));
        const adminCta = await page.evaluate(() => {
            const cta = document.getElementById('header-primary-cta');
            return {
                text: cta ? cta.innerText.trim() : null,
                href: cta ? cta.getAttribute('href') : null,
            };
        });
        console.log('[Role: Admin CTA Check]:', adminCta);
        await page.screenshot({ path: path.join(screenshotDir, 'navbar_1440px_role_admin.png') });

        await page.close();
    }

    await browser.close();
    console.log('--- ALL VIEWPORT AND ROLE CHECKS COMPLETED SUCCESSFULLY ---');
})();
