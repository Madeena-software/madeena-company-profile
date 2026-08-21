const { chromium } = require('playwright');
const fs = require('fs');

const artifactsDir = '/home/faliq/.gemini/antigravity-ide/brain/43df04b9-afd7-48a0-8275-833d39099c2f/artifacts';
if (!fs.existsSync(artifactsDir)) {
  fs.mkdirSync(artifactsDir, { recursive: true });
}

(async () => {
  const browser = await chromium.launch();
  // Force LIGHT color scheme
  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 },
    colorScheme: 'light'
  });
  const page = await context.newPage();
  
  // Login to get session cookies
  await page.goto('http://localhost:8011/test-support/login', { waitUntil: 'networkidle' });
  
  console.log(`Testing Admin Light Mode`);
  await page.goto('http://localhost:8011/admin', { waitUntil: 'networkidle' });
  
  // Forcing Filament light mode button click if it's there, but setting colorScheme: 'light' should be enough
  // Let's just evaluate localStorage to be absolutely sure Filament is in light mode
  await page.evaluate(() => {
      localStorage.setItem('theme', 'light');
  });
  await page.reload({ waitUntil: 'networkidle' });
  
  await page.waitForTimeout(1000);
  await page.screenshot({ path: `${artifactsDir}/admin_light_mode_test.png`, fullPage: true });

  await context.close();
  await browser.close();
})();
