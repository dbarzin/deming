// Takes the documentation screenshots (docs/images) in English and French.
//
// Usage: npm run docs:screenshots
//
// Environment:
//   DEMING_URL       application URL (default: http://localhost:8000)
//   DEMING_LOGIN     administrator login (default: admin@admin.localhost)
//   DEMING_PASSWORD  administrator password (default: admin)
//   CHROME_PATH      browser executable (default: Playwright's bundled Chromium)
//   LANGS            languages to capture (default: en,fr)
//   ONLY             comma-separated screenshot names to capture (default: all)
//   OUT_DIR          output directory (default: docs/images)
//   ID_<lookup>      fixed value for a lookup of screens.json (e.g. ID_control=18)

const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = (process.env.DEMING_URL || 'http://localhost:8000').replace(/\/$/, '');
const LOGIN = process.env.DEMING_LOGIN || 'admin@admin.localhost';
const PASSWORD = process.env.DEMING_PASSWORD || 'admin';
const LANGS = (process.env.LANGS || 'en,fr').split(',');
const ONLY = process.env.ONLY ? process.env.ONLY.split(',') : null;
const OUT_DIR = process.env.OUT_DIR || path.join(__dirname, '..', '..', 'docs', 'images');
const spec = JSON.parse(fs.readFileSync(path.join(__dirname, 'screens.json')));

// Image name: "{fr}" becomes "fr" in French and is dropped in English, "{lang}" is always replaced.
function fileName(name, lang) {
    return name
        .replace('.{fr}', lang === 'en' ? '' : '.fr')
        .replace('{lang}', lang);
}

async function login(page) {
    await page.goto(BASE + '/login');
    await page.fill('#login', LOGIN);
    await page.fill('input[name=password]', PASSWORD);
    await Promise.all([page.waitForNavigation(), page.click('form button.btn-primary')]);
    if (page.url().includes('/login')) throw new Error('Login failed for ' + LOGIN);
}

// Resolves the {placeholders} used in screen URLs from the first matching element of a list page.
async function resolveLookups(page) {
    const values = {};
    for (const [key, l] of Object.entries(spec.lookups)) {
        if (process.env['ID_' + key]) {
            values[key] = process.env['ID_' + key];
            continue;
        }
        await page.goto(BASE + l.page, { waitUntil: 'networkidle' });
        const raw = await page.locator(l.selector).first().getAttribute(l.attr || 'href');
        const match = l.regex ? new RegExp(l.regex).exec(raw || '') : [raw, raw];
        if (!match) throw new Error(`Lookup "${key}" found nothing on ${l.page}`);
        values[key] = match[1].trim();
    }
    return values;
}

function fillUrl(url, values) {
    return url.replace(/\{(\w+)\}/g, (_, key) => encodeURIComponent(values[key]));
}

// Height of the element's content, ignoring the empty space of a full-height container.
async function contentHeight(el) {
    return el.evaluate(e => {
        const top = e.getBoundingClientRect().top;
        let bottom = 0;
        for (const c of e.children) {
            const r = c.getBoundingClientRect();
            if (r.height) bottom = Math.max(bottom, r.bottom - top);
        }
        return bottom ? bottom + 14 : e.getBoundingClientRect().height;
    });
}

async function capture(page, screen, lang, values) {
    const url = fillUrl(screen.url, values);
    const sep = url.includes('?') ? '&' : '?';
    await page.goto(BASE + url + sep + 'change_language=' + lang, { waitUntil: 'networkidle' });
    await page.addStyleTag({ content: '*{animation:none!important;transition:none!important}' });
    // Keep DataTables lists short
    await page.evaluate(() => {
        try {
            if (window.jQuery && jQuery.fn.dataTable) jQuery.fn.dataTable.tables({ api: true }).page.len(10).draw();
        } catch (e) {}
    });
    await page.waitForTimeout(screen.wait || 1200);

    const el = page.locator(screen.selector || 'main#content-home').first();
    const box = await el.boundingBox();
    let height = Math.min(await contentHeight(el), box.height);
    if (screen.until) {
        const until = await page.locator(screen.until).first().boundingBox();
        height = until.y - box.y;
    }
    if (screen.maxHeight) height = Math.min(height, screen.maxHeight);

    const file = path.join(OUT_DIR, fileName(screen.name, lang) + '.png');
    await page.screenshot({ path: file, fullPage: true, clip: { x: box.x, y: box.y, width: box.width, height } });
    console.log(path.relative(process.cwd(), file));
}

(async () => {
    const browser = await chromium.launch(process.env.CHROME_PATH ? { executablePath: process.env.CHROME_PATH } : {});
    try {
        let values = null;
        for (const lang of LANGS) {
            // The browser locale drives the format of date inputs
            const context = await browser.newContext({
                viewport: { width: 1680, height: 1000 },
                locale: lang === 'en' ? 'en-GB' : 'fr-FR',
            });
            const page = await context.newPage();
            await login(page);
            if (!values) values = await resolveLookups(page);
            for (const screen of spec.screens) {
                if (ONLY && !ONLY.includes(screen.name.split('.{')[0])) continue;
                await capture(page, screen, lang, values);
            }
            await context.close();
        }
    } finally {
        await browser.close();
    }
})().catch(e => {
    console.error(e.message);
    process.exit(1);
});
