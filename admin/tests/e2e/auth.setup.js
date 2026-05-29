/**
 * Auth Setup — Runs once before all tests to save login session
 * 
 * Saves browser state (cookies + localStorage) to .auth/user.json
 * so all subsequent tests can reuse the session without logging in again.
 */
const { test: setup } = require('@playwright/test');
const { login } = require('./helpers/auth');

setup('authenticate', async ({ page }) => {
  const username = process.env.TEST_USERNAME || 'Developer';
  const password = process.env.TEST_PASSWORD || 'Coswan@dev#etail';
  const branch = process.env.LOGIN_BRANCH || '';

  // Login
  await login(page, username, password, branch || undefined);

  // Save auth state for reuse
  await page.context().storageState({ path: '.auth/user.json' });
});
