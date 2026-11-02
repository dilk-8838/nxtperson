import puppeteer from 'puppeteer-core';
import fs from 'fs';
import path from 'path';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const aboutDir = 'c:\\Users\\Dilkhush Labana\\Desktop\\NXTperson website\\public\\images\\about';

if (!fs.existsSync(aboutDir)) {
    fs.mkdirSync(aboutDir, { recursive: true });
}

async function convertImage(srcPath, destJpg, destWebp, maxWidth = 1200) {
    // Copy JPG first
    fs.copyFileSync(srcPath, destJpg);
    console.log(`Copied ${srcPath} -> ${destJpg}`);

    // Convert to WebP via headless Chrome canvas
    const browser = await puppeteer.launch({
        executablePath: chromePath,
        headless: 'new',
        args: ['--no-sandbox', '--disable-gpu']
    });

    const page = await browser.newPage();
    const dataUrl = `data:image/jpeg;base64,${fs.readFileSync(srcPath).toString('base64')}`;

    const webpBase64 = await page.evaluate(async (imgSrc, maxW) => {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                let w = img.width;
                let h = img.height;
                if (w > maxW) {
                    h = Math.round((h * maxW) / w);
                    w = maxW;
                }
                canvas.width = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);
                const webp = canvas.toDataURL('image/webp', 0.88);
                resolve(webp.split(',')[1]);
            };
            img.onerror = reject;
            img.src = imgSrc;
        });
    }, dataUrl, maxWidth);

    await browser.close();
    fs.writeFileSync(destWebp, Buffer.from(webpBase64, 'base64'));
    console.log(`Generated WebP: ${destWebp} (${fs.statSync(destWebp).size} bytes)`);
}

async function main() {
    const src1 = 'c:\\Users\\Dilkhush Labana\\Desktop\\NXTperson website\\public\\images\\hero\\farmer_field.jpg';
    const jpg1 = path.join(aboutDir, 'farmer_story.jpg');
    const webp1 = path.join(aboutDir, 'farmer_story.webp');
    await convertImage(src1, jpg1, webp1, 1200);

    const src2 = 'c:\\Users\\Dilkhush Labana\\Desktop\\NXTperson website\\public\\images\\hero\\hero_farm.jpg';
    const jpg2 = path.join(aboutDir, 'farm_vision.jpg');
    const webp2 = path.join(aboutDir, 'farm_vision.webp');
    await convertImage(src2, jpg2, webp2, 1400);

    console.log('About images prepared successfully.');
}

main().catch(console.error);
