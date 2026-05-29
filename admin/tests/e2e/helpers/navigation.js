/**
 * Navigation Helper — Common page navigation for ETail v6
 * 
 * IMPORTANT: CI3 uses index.php routing. All URLs must be absolute:
 *   http://localhost/etail_v6/admin/index.php/{controller}/{method}
 */

const ROUTES = {
  dashboard:      '/admin/dashboard',
  billingList:    '/admin_ret_billing/billing/list',
  billingAdd:     '/admin_ret_billing/issue/add',
  paymentEdit:    '/admin_ret_billing/paymentmode_edit/list',
  estimationList: '/admin_ret_estimation/estimation/list',
  estimationAdd:  '/admin_ret_estimation/estimation/add',
  reportsSales:   '/admin_ret_reports/sales_report/list',
};

/**
 * Get the base URL from env
 * @returns {string}
 */
function getBaseUrl() {
  return process.env.BASE_URL || 'http://localhost/etail_v6/admin/index.php';
}

/**
 * Navigate to a named route
 * @param {import('@playwright/test').Page} page
 * @param {keyof typeof ROUTES} routeName
 */
async function goTo(page, routeName) {
  const path = ROUTES[routeName];
  if (!path) throw new Error(`Unknown route: ${routeName}`);
  await page.goto(`${getBaseUrl()}${path}`, { waitUntil: 'domcontentloaded', timeout: 30000 });
  // Wait for content wrapper to appear (CI3 template loaded)
  await page.waitForSelector('.content-wrapper', { timeout: 15000 });
}

/**
 * Navigate to payment edit page
 * @param {import('@playwright/test').Page} page
 */
async function goToPaymentEdit(page) {
  await goTo(page, 'paymentEdit');
}

/**
 * Navigate to billing list
 * @param {import('@playwright/test').Page} page
 */
async function goToBillingList(page) {
  await goTo(page, 'billingList');
}

/**
 * Navigate to dashboard
 * @param {import('@playwright/test').Page} page
 */
async function goToDashboard(page) {
  await goTo(page, 'dashboard');
}

module.exports = { ROUTES, getBaseUrl, goTo, goToPaymentEdit, goToBillingList, goToDashboard };
