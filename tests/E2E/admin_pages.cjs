const { chromium } = require('playwright');
const fs = require('fs');

const artifactsDir = '/home/faliq/.gemini/antigravity-ide/brain/43df04b9-afd7-48a0-8275-833d39099c2f/artifacts';
if (!fs.existsSync(artifactsDir)) {
  fs.mkdirSync(artifactsDir, { recursive: true });
}

const viewports = [
  { name: 'Desktop', width: 1920, height: 1080 },
  { name: 'Tablet', width: 768, height: 1024 },
  { name: 'Mobile', width: 375, height: 812 }
];

const routes = [
  { name: 'admin_dashboard', path: '/admin' },
  { name: 'admin_homepage_editor', path: '/admin/homepage-editor' },
  { name: 'admin_posts', path: '/admin/posts' },
  { name: 'admin_posts_create', path: '/admin/posts/create' },
  { name: 'admin_categories', path: '/admin/categories' },
  { name: 'admin_products', path: '/admin/products' },
  { name: 'admin_pages', path: '/admin/pages' },
  { name: 'admin_settings', path: '/admin/site-settings' },
  { name: 'admin_users', path: '/admin/users' },
  { name: 'admin_inabuyer', path: '/admin/inabuyer-messages' }
];

const baseUrl = 'http://localhost:8011';

(async () => {
  const browser = await chromium.launch();
  
  for (const vp of viewports) {
    const context = await browser.newContext({
      viewport: { width: vp.width, height: vp.height },
      colorScheme: 'dark'
    });
    const page = await context.newPage();
    
    // Login to get session cookies
    await page.goto(`${baseUrl}/login-test-user`, { waitUntil: 'networkidle' });
    
    for (const route of routes) {
      console.log(`Testing Admin ${vp.name} - ${route.name}`);
      await page.goto(`${baseUrl}${route.path}`, { waitUntil: 'networkidle' });
      await page.waitForTimeout(1000);
      await page.screenshot({ path: `${artifactsDir}/admin_${route.name}_${vp.name.toLowerCase()}.png`, fullPage: true });
    }
    await context.close();
  }

  await browser.close();
})();
