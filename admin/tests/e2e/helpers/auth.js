/**
 * Auth Helper — Login flow for ETail v6
 * 
 * Strategy: Call genOTP() directly via page.evaluate after filling the form.
 * This ensures jQuery + all page context is properly available.
 * 
 * Fallback: If AJAX login fails, try direct API call and session cookie approach.
 */

/**
 * Login to the application
 * @param {import('@playwright/test').Page} page
 * @param {string} username
 * @param {string} password
 * @param {string} [branch] - Branch name to select (optional)
 */
async function login(page, username, password, branch) {
  const baseUrl = process.env.BASE_URL || 'http://localhost/etail_v6/admin/index.php';

  // Navigate to login page
  await page.goto(`${baseUrl}/admin/login`);
  await page.waitForSelector('#username', { state: 'visible', timeout: 10000 });

  // Wait for page JS to fully load (Select2 init, branch AJAX, DeviceId generation)
  await page.waitForTimeout(3000);

  // Fill credentials
  await page.fill('#username', username);
  await page.fill('#password', password);

  // Call genOTP() directly — this is the function the form submit handler calls
  // We set up DeviceId first if it's not already set
  const loginResult = await page.evaluate(async () => {
    // Ensure DeviceId is available (generated in inline script at page bottom)
    if (typeof DeviceId === 'undefined' || !DeviceId) {
      window.DeviceId = localStorage.getItem('deviceID') || 'playwright-test-device';
    }

    return new Promise((resolve) => {
      const usernameVal = $('#username').val();
      const passwordVal = $('#password').val();
      const idBranch = $('#id_branch').val() || '0';
      const idCompany = $('#company_select').val() || '';

      $.ajax({
        url: base_url + 'index.php/chit_admin/authenticate',
        data: {
          password: passwordVal,
          username: usernameVal,
          id_branch: idBranch,
          token_id: DeviceId,
          id_company: idCompany
        },
        type: 'POST',
        dataType: 'json',
        success: function(data) {
          resolve({ success: true, result: data.result, msg: data.msg || '' });
        },
        error: function(xhr, status, error) {
          resolve({ success: false, error: error, status: status });
        }
      });
    });
  });

  if (loginResult.success && loginResult.result == 1) {
    // Login succeeded — navigate to dashboard
    await page.goto(`${baseUrl}/admin/dashboard`);
    await page.waitForLoadState('networkidle');
  } else if (loginResult.success && loginResult.result == 3) {
    throw new Error('OTP required — cannot automate OTP login. Disable OTP for test user.');
  } else {
    throw new Error(`Login failed: ${JSON.stringify(loginResult)}`);
  }
}

/**
 * Logout from the application
 * @param {import('@playwright/test').Page} page
 */
async function logout(page) {
  const baseUrl = process.env.BASE_URL || 'http://localhost/etail_v6/admin/index.php';
  await page.goto(`${baseUrl}/admin/logout`);
  await page.waitForURL('**/admin/login');
}

/**
 * Check if currently logged in
 * @param {import('@playwright/test').Page} page
 * @returns {Promise<boolean>}
 */
async function isLoggedIn(page) {
  const baseUrl = process.env.BASE_URL || 'http://localhost/etail_v6/admin/index.php';
  await page.goto(`${baseUrl}/admin/dashboard`);
  const url = page.url();
  return !url.includes('/login');
}

module.exports = { login, logout, isLoggedIn };
