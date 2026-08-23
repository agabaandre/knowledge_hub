#!/usr/bin/env node
/**
 * Capture live Knowledge Hub screenshots for the in-app user manuals.
 *
 *   KHUB_BASE_URL=http://localhost/knowledge_hub \
 *   KHUB_ADMIN_EMAIL=system@africacdc.org \
 *   KHUB_ADMIN_PASSWORD=password \
 *   node scripts/capture-manual-screenshots.cjs
 *
 * Do not commit credentials. The password is read from the environment only.
 */
const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-core');

const ROOT = path.resolve(__dirname, '..');
const BASE = (process.env.KHUB_BASE_URL || 'http://localhost/knowledge_hub').replace(/\/$/, '');
const EMAIL = process.env.KHUB_ADMIN_EMAIL || 'system@africacdc.org';
const PASSWORD = process.env.KHUB_ADMIN_PASSWORD || '';
const CHROME =
    process.env.CHROME_PATH ||
    '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';

const USER_DIR = path.join(ROOT, 'public/manual/user-guide');
const ADMIN_DIR = path.join(ROOT, 'public/manual/administrator-guide');

function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

async function dismissOverlays(page) {
    await page.evaluate(() => {
        const expire = 'expires=Fri, 31 Dec 2030 23:59:59 GMT';
        document.cookie = 'is_returning=yes; path=/; ' + expire;
        document.cookie = 'CDC_Tour_Finished=yes; path=/; ' + expire;
        document.cookie = 'CDC_Tour_Declined=yes; path=/; ' + expire;
        document.getElementById('tourWelcomeModal')?.remove();
        document.querySelectorAll('.cookie-consent, .wt-overlay, .wt-container').forEach((el) => el.remove());
        document.querySelectorAll('.phpdebugbar, #phpdebugbar, .phpdebugbar-openhandler').forEach((el) => el.remove());
    });
    const skip = await page.$('#skipTourBtn, #skipTourBtn');
    if (skip) {
        try {
            await skip.click();
            await sleep(250);
        } catch (_err) {
            // Overlay may already be gone.
        }
        await page.evaluate(() => document.getElementById('tourWelcomeModal')?.remove());
    }
}

async function firstHref(page, selectors) {
    return page.evaluate((list) => {
        for (const selector of list) {
            const el = document.querySelector(selector);
            if (el && el.href) {
                return el.href;
            }
        }
        return null;
    }, selectors);
}

async function shot(page, dest, targetUrl) {
    const url = /^https?:\/\//.test(targetUrl) ? targetUrl : BASE + targetUrl;
    process.stdout.write(`capture ${path.basename(path.dirname(dest))}/${path.basename(dest)}  ${url}\n`);
    let response = null;
    try {
        response = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 45000 });
    } catch (err) {
        process.stderr.write(`  FAIL navigation: ${err.message}\n`);
        return false;
    }
    await dismissOverlays(page);
    await sleep(1200);
    const status = response ? response.status() : 0;
    if (status >= 400) {
        process.stderr.write(`  SKIP HTTP ${status}\n`);
        return false;
    }
    fs.mkdirSync(path.dirname(dest), { recursive: true });
    await page.screenshot({ path: dest, type: 'png' });
    return true;
}

async function login(page) {
    if (!PASSWORD) {
        throw new Error('KHUB_ADMIN_PASSWORD is required');
    }
    await page.goto(BASE + '/login', { waitUntil: 'domcontentloaded', timeout: 45000 });
    await dismissOverlays(page);
    await page.waitForSelector('#email', { timeout: 15000 });
    await page.click('#email', { clickCount: 3 });
    await page.type('#email', EMAIL, { delay: 15 });
    await page.click('#password', { clickCount: 3 });
    await page.type('#password', PASSWORD, { delay: 15 });
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 30000 }).catch(() => null),
        page.click('form#loginForm .btn-login, form[action*="login"] .btn-login, .btn-login'),
    ]);
    await sleep(1200);
    await page.goto(BASE + '/account', { waitUntil: 'domcontentloaded', timeout: 30000 });
    await dismissOverlays(page);
    const url = page.url();
    if (url.includes('/login')) {
        const body = await page.evaluate(() => (document.body && document.body.innerText) || '');
        throw new Error('Login did not succeed. URL=' + url + ' body=' + body.slice(0, 400));
    }
    process.stdout.write(`logged in as ${EMAIL} at ${url}\n`);
}

(async () => {
    fs.mkdirSync(USER_DIR, { recursive: true });
    fs.mkdirSync(ADMIN_DIR, { recursive: true });

    const browser = await puppeteer.launch({
        executablePath: CHROME,
        headless: true,
        defaultViewport: { width: 1440, height: 1000, deviceScaleFactor: 1 },
        args: ['--hide-scrollbars', '--disable-gpu', '--no-sandbox', '--disable-dev-shm-usage'],
    });
    const page = await browser.newPage();
    await page.setUserAgent(
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    );
    page.setDefaultTimeout(45000);

    const captured = [];
    const failed = [];

    async function take(dest, targetUrl) {
        const ok = await shot(page, dest, targetUrl);
        (ok ? captured : failed).push(path.relative(ROOT, dest));
    }

    try {
        await take(path.join(USER_DIR, '01-home.png'), '/');
        await take(path.join(USER_DIR, '02-login.png'), '/login');
        await take(path.join(USER_DIR, '03-register.png'), '/register');
        await take(path.join(USER_DIR, '04-records.png'), '/records');

        const publicationUrl = await firstHref(page, [
            'a[href*="/records/resource/"]',
            'a[href*="/resource/"]',
        ]);
        if (publicationUrl) {
            await take(path.join(USER_DIR, '05-publication-detail.png'), publicationUrl);
        } else {
            failed.push('public/manual/user-guide/05-publication-detail.png');
            process.stderr.write('  SKIP no publication link on /records\n');
        }

        await take(path.join(USER_DIR, '06-forums.png'), '/forums');
        const forumUrl = await firstHref(page, ['a[href*="/forums/thread"]']);
        if (forumUrl) {
            await take(path.join(USER_DIR, '07-forum-thread.png'), forumUrl);
        } else {
            failed.push('public/manual/user-guide/07-forum-thread.png');
        }

        await take(path.join(USER_DIR, '08-communities.png'), '/communities');
        const communityUrl = await firstHref(page, ['a[href*="/communities/detail/"]']);
        if (communityUrl) {
            await take(path.join(USER_DIR, '09-community-detail.png'), communityUrl);
        } else {
            failed.push('public/manual/user-guide/09-community-detail.png');
        }

        await take(path.join(USER_DIR, '10-countries.png'), '/countries');
        await sleep(1500);
        const countryUrl = await firstHref(page, ['a[href*="/countries/details/"]']);
        if (countryUrl) {
            await take(path.join(USER_DIR, '11-country-detail.png'), countryUrl);
        } else {
            failed.push('public/manual/user-guide/11-country-detail.png');
        }

        await take(path.join(USER_DIR, '12-adminunits.png'), '/adminunits');
        const unitUrl = await firstHref(page, ['a[href*="/adminunits/details"]']);
        if (unitUrl) {
            await take(path.join(USER_DIR, '13-adminunit-detail.png'), unitUrl);
        } else {
            failed.push('public/manual/user-guide/13-adminunit-detail.png');
        }

        await take(path.join(USER_DIR, '14-federated.png'), '/federated');
        await take(path.join(USER_DIR, '15-health-topics.png'), '/health-topics');
        const topicUrl = await firstHref(page, ['a[href*="/health-topics/"]:not([href$="/health-topics"]):not([href$="/health-topics/"])']);
        if (topicUrl && !topicUrl.endsWith('/health-topics') && !topicUrl.endsWith('/health-topics/')) {
            await take(path.join(USER_DIR, '16-health-topic-detail.png'), topicUrl);
        } else {
            failed.push('public/manual/user-guide/16-health-topic-detail.png');
        }

        await take(path.join(USER_DIR, '17-courses.png'), '/courses');
        await take(path.join(USER_DIR, '18-faqs.png'), '/faqs');
        await take(path.join(USER_DIR, '19-content-request.png'), '/publications/request-content');

        await login(page);

        await take(path.join(USER_DIR, '20-publish.png'), '/account/publish');
        await take(path.join(USER_DIR, '21-my-publications.png'), '/account/publications');
        await take(path.join(USER_DIR, '22-account.png'), '/account');
        await take(path.join(USER_DIR, '23-favourites.png'), '/account/favourites');
        await take(path.join(USER_DIR, '24-my-forums.png'), '/account/my-forums');
        await take(path.join(USER_DIR, '25-my-communities.png'), '/account/my-communities');
        await take(path.join(USER_DIR, '26-approvals.png'), '/admin/approvals');

        await take(path.join(ADMIN_DIR, '01-dashboard.png'), '/admin/dashboard');
        await take(path.join(ADMIN_DIR, '02-dashboard-list.png'), '/admin/dashboard/list');
        await take(path.join(ADMIN_DIR, '03-approvals.png'), '/admin/approvals');
        await take(path.join(ADMIN_DIR, '04-publications.png'), '/admin/publications');
        await take(path.join(ADMIN_DIR, '05-publication-create.png'), '/admin/publications/create');
        await take(path.join(ADMIN_DIR, '06-publications-pending.png'), '/admin/publications/pending');
        await take(path.join(ADMIN_DIR, '07-adminunits.png'), '/admin/adminunits');
        await take(path.join(ADMIN_DIR, '08-federated-hubs.png'), '/admin/federated-hubs');
        await take(path.join(ADMIN_DIR, '09-federated-pending.png'), '/admin/federated-content/pending');
        await take(path.join(ADMIN_DIR, '10-configure.png'), '/admin/configure');
        await take(path.join(ADMIN_DIR, '11-storage.png'), '/admin/storage-management');
        await take(path.join(ADMIN_DIR, '12-users.png'), '/permissions/users');
        await take(path.join(ADMIN_DIR, '13-roles.png'), '/permissions/roles');
        await take(path.join(ADMIN_DIR, '14-permissions.png'), '/permissions');
        await take(path.join(ADMIN_DIR, '15-events.png'), '/admin/events');

        if (!fs.existsSync(path.join(USER_DIR, '10-countries.png'))) {
            await take(path.join(USER_DIR, '10-countries.png'), '/admin/areas');
        }
        if (!fs.existsSync(path.join(USER_DIR, '11-country-detail.png'))) {
            const areaUrl = await firstHref(page, ['a[href*="/admin/areas/"]', 'a[href*="/countries/details/"]']);
            if (areaUrl) {
                await take(path.join(USER_DIR, '11-country-detail.png'), areaUrl);
            } else if (fs.existsSync(path.join(USER_DIR, '10-countries.png'))) {
                fs.copyFileSync(path.join(USER_DIR, '10-countries.png'), path.join(USER_DIR, '11-country-detail.png'));
                captured.push('public/manual/user-guide/11-country-detail.png');
            }
        }
        if (!fs.existsSync(path.join(USER_DIR, '13-adminunit-detail.png'))) {
            const adminUnitUrl = await firstHref(page, ['a[href*="/admin/adminunits"]', 'a[href*="/adminunits/details"]']);
            if (adminUnitUrl) {
                await take(path.join(USER_DIR, '13-adminunit-detail.png'), adminUnitUrl);
            } else {
                await take(path.join(USER_DIR, '13-adminunit-detail.png'), '/admin/adminunits');
            }
        }
        if (!fs.existsSync(path.join(USER_DIR, '14-federated.png'))) {
            await take(path.join(USER_DIR, '14-federated.png'), '/admin/federated-hubs');
        }
    } finally {
        await browser.close();
    }

    process.stdout.write(`\ncaptured ${captured.length} screenshots\n`);
    if (failed.length) {
        process.stderr.write(`missing ${failed.length}:\n${failed.map((f) => '  ' + f).join('\n')}\n`);
    }
})().catch((err) => {
    process.stderr.write(err.stack || String(err));
    process.stderr.write('\n');
    process.exit(1);
});
