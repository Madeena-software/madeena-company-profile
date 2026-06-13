const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ colorScheme: 'dark' });
  
  // Navigate to the test login route, which redirects to admin panel
  await page.goto('http://localhost:8011/login-test-user', { waitUntil: 'networkidle' });
  
  // We might be redirected to login. Take a screenshot of whatever page we are on.
  await page.screenshot({ path: '/home/faliq/.gemini/antigravity-ide/brain/0b6b0c65-7a6d-4061-bdd5-f60fa6672a8a/artifacts/admin-screenshot.png', fullPage: true });
  
  console.log("Screenshot saved!");
  await browser.close();
})();
