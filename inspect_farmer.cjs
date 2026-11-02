const puppeteer = require('puppeteer-core');

(async () => {
    const browser = await puppeteer.launch({
        headless: 'new',
        executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        args: ['--no-sandbox']
    });
    const page = await browser.newPage();
    await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
    const info = await page.evaluate(() => {
        const img = document.querySelector('img[alt*="Indian farmer"]');
        if (!img) return { found: false };
        const parent = img.parentElement;
        img.style.height = '100%';
        img.style.width = '100%';
        img.style.objectFit = 'cover';
        return {
            found: true,
            computedImgHeightAfterStyle: window.getComputedStyle(img).height,
            computedParentHeight: window.getComputedStyle(parent).height
        };
    });
    console.log(JSON.stringify(info, null, 2));
    await browser.close();
})();
