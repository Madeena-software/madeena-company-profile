import { chromium } from '@playwright/test';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const BASE_URL = 'http://127.0.0.1:8011';
const SCREENSHOT_DIR = path.join(__dirname, 'screenshots');

const viewports = { width: 1280, height: 800 };

async function ensureDir(dir) {
    if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
    }
}

async function capture(page, dirName, fileName) {
    const fullDir = path.join(SCREENSHOT_DIR, dirName);
    await ensureDir(fullDir);
    await page.waitForTimeout(1000); // Wait for animations
    await page.screenshot({ path: path.join(fullDir, fileName) });
    console.log(`Captured ${dirName}/${fileName}`);
}

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({ viewport: viewports });
    const page = await context.newPage();

    try {
        console.log('Starting screenshot capture...');

        // 01. Login Page
        await page.goto(`${BASE_URL}/admin/login`);
        await capture(page, '01-login', 'login-overview.png');

        // Magic Login
        await page.goto(`${BASE_URL}/login-test-user`);
        await page.waitForURL('**/admin');
        
        // 02. Dashboard
        await capture(page, '02-dashboard', 'dashboard-overview.png');

        // 03. Homepage Editor
        await page.goto(`${BASE_URL}/admin/homepage-editor`);
        await capture(page, '03-homepage', 'homepage-overview.png');
        
        // Try to open first block if possible
        const firstBlockToggle = page.locator('button[aria-label="Collapse"]').first();
        if (await firstBlockToggle.isVisible()) {
            await firstBlockToggle.click({ force: true });
            await page.waitForTimeout(500);
            await capture(page, '03-homepage', 'homepage-edit.png');
        }

        // 04. Produk
        await page.goto(`${BASE_URL}/admin/products`);
        await capture(page, '04-produk', 'produk-overview.png');
        
        // Click first Edit button
        const firstProductEdit = page.locator('a[href*="/edit"]').first();
        if (await firstProductEdit.isVisible()) {
            await firstProductEdit.click();
            await page.waitForLoadState('networkidle');
            await capture(page, '04-produk', 'produk-edit.png');
        }

        // 05. Artikel
        await page.goto(`${BASE_URL}/admin/posts`);
        await capture(page, '05-artikel', 'artikel-overview.png');
        
        const firstPostEdit = page.locator('a[href*="/edit"]').first();
        if (await firstPostEdit.isVisible()) {
            await firstPostEdit.click();
            await page.waitForLoadState('networkidle');
            await capture(page, '05-artikel', 'artikel-edit.png');
            
            // Switch to Konten Artikel tab
            const kontenTab = page.locator('button[role="tab"]:has-text("Konten Artikel")');
            if (await kontenTab.isVisible()) {
                await kontenTab.click();
                await page.waitForTimeout(500);
                await capture(page, '05-artikel', 'artikel-editor.png');
            }
        }

        // 06. Halaman
        await page.goto(`${BASE_URL}/admin/pages`);
        await capture(page, '06-halaman', 'halaman-overview.png');

        // 07. Pengaturan
        await page.goto(`${BASE_URL}/admin/site-settings`);
        await capture(page, '07-pengaturan', 'pengaturan-overview.png');

        // 08. Pengguna
        await page.goto(`${BASE_URL}/admin/users`);
        await capture(page, '08-pengguna', 'pengguna-overview.png');

        // 09. Events
        await page.goto(`${BASE_URL}/admin/events`);
        await capture(page, '09-events', 'events-overview.png');
        
        const firstEventEdit = page.locator('a[href*="/edit"]').first();
        if (await firstEventEdit.isVisible()) {
            await firstEventEdit.click();
            await page.waitForLoadState('networkidle');
            await capture(page, '09-events', 'events-edit.png');
        }

        // 10. Guest Messages
        await page.goto(`${BASE_URL}/admin/guest-messages`);
        await capture(page, '10-guest-messages', 'guest-messages-overview.png');

        const firstMessageEdit = page.locator('a[href*="/edit"]').first();
        if (await firstMessageEdit.isVisible()) {
            await firstMessageEdit.click();
            await page.waitForLoadState('networkidle');
            await capture(page, '10-guest-messages', 'guest-messages-edit.png');
        }

        console.log('Successfully captured all screenshots.');
    } catch (error) {
        console.error('Error capturing screenshots:', error);
    } finally {
        await browser.close();
    }
})();
