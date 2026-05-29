<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cashfree API Configuration
 * 
 * Environment: 'test' for sandbox, 'live' for production
 */

// Switch between 'test' and 'live'
$config['cashfree_env'] = 'live';

// Sandbox / Test Environment
$config['cashfree_test_base_url']       = 'https://sandbox.cashfree.com/verification';
$config['cashfree_test_client_id']      = 'CF10699313D6JUDPTP6BAC73D3CK1G';
$config['cashfree_test_client_secret']  = 'cfsk_ma_test_996600a44374cf883fa0e5d99ec846c0_b9a11715';

// Live / Production Environment
$config['cashfree_live_base_url']       = 'https://api.cashfree.com/verification';
$config['cashfree_live_client_id']      = 'CF1010145D6JUDD04C2EC739GC82G';
$config['cashfree_live_client_secret']  = 'cfsk_ma_prod_f4a006d75c6d1b0c40c571bda9d98f65_63b216a5';