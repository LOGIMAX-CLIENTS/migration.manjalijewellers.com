// @ts-check
const { defineConfig } = require('@playwright/test');
require('dotenv').config();

module.exports = defineConfig({
  testDir: '.',
  testMatch: '**/*.spec.js',

  /* Fail the build on CI if you accidentally left test.only in the source code */
  forbidOnly: !!process.env.CI,

  /* Retry on CI only */
  retries: process.env.CI ? 2 : 0,

  /* Reporter */
  reporter: [
    ['html', { open: 'never' }],
    ['list']
  ],

  /* Shared settings for all projects */
  use: {
    baseURL: process.env.BASE_URL || 'http://localhost/etail_v6/admin/index.php',

    /* Capture screenshot on failure */
    screenshot: 'only-on-failure',

    /* Capture trace on first retry */
    trace: 'on-first-retry',

    /* Default timeout for actions */
    actionTimeout: 10000,

    /* Default viewport */
    viewport: { width: 1920, height: 1080 },
  },

  /* Global timeout per test */
  timeout: 60000,

  /* Projects: setup auth first, then run tests */
  projects: [
    {
      name: 'auth-setup',
      testMatch: /auth\.setup\.js/,
    },
    {
      name: 'chromium',
      use: {
        browserName: 'chromium',
        /* Reuse auth state from setup */
        storageState: '.auth/user.json',
      },
      dependencies: ['auth-setup'],
    },
  ],

  /* Output directory for test artifacts */
  outputDir: 'test-results',
});
