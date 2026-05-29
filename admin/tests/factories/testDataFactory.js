/**
 * Test Data Factories for eTail v3 JavaScript Tests
 *
 * Usage:
 *   const { createEstimation, createCustomer } = require('./factories/testDataFactory');
 *
 *   test('example', () => {
 *     const estimation = createEstimation({ discount: 500 });
 *   });
 */

// Counter for unique IDs
let idCounter = 0;
const nextId = () => ++idCounter;

// Reset counter (call in beforeEach if needed)
const resetFactories = () => {
  idCounter = 0;
};

// ============================================
// ESTIMATION FACTORY
// ============================================

const createEstimation = (overrides = {}) => ({
  id: nextId(),
  id_branch: 1,
  esti_for: 1,
  cus_id: 101,
  discount: 0,
  blk_discount: 0,
  gift_voucher_amt: 0,
  total_cost: 50000,
  created_by: 1,
  is_eda: 0,
  goldrate_22ct: 5500,
  silverrate_1gm: 75,
  cgst: 1.5,
  sgst: 1.5,
  igst: 0,
  status: "draft",
  items: [],
  created_at: new Date().toISOString(),
  ...overrides,
});

const createEstimationItem = (overrides = {}) => ({
  id: nextId(),
  tag_no: `TAG${String(nextId()).padStart(6, "0")}`,
  gross_wt: 10.5,
  net_wt: 9.8,
  stone_wt: 0.7,
  id_product: 1,
  id_design: 1,
  making_charge: 500,
  stone_charge: 0,
  wastage: 10,
  rate_per_gram: 5500,
  amount: 53900, // 9.8 * 5500
  ...overrides,
});

const createEstimationWithItems = (itemCount = 2, overrides = {}) => {
  const items = Array.from({ length: itemCount }, () => createEstimationItem());
  const totalCost = items.reduce((sum, item) => sum + item.amount, 0);
  return createEstimation({ items, total_cost: totalCost, ...overrides });
};

// ============================================
// CUSTOMER FACTORY
// ============================================

const createCustomer = (overrides = {}) => {
  const id = nextId();
  return {
    id,
    cusName: `Test Customer ${id}`,
    cusMobile: `98765${String(id).padStart(5, "0")}`,
    mail: `customer${id}@example.com`,
    id_branch: 1,
    id_state: 1,
    id_city: 1,
    address: "123 Test Street",
    pincode: "600001",
    gst_no: "",
    pan_no: "",
    ...overrides,
  };
};

const createCorporateCustomer = (overrides = {}) =>
  createCustomer({
    cusName: "Corporate Client Pvt Ltd",
    gst_no: "33AABCU9603R1ZM",
    pan_no: "AABCU9603R",
    is_corporate: 1,
    ...overrides,
  });

// ============================================
// PRODUCT FACTORY
// ============================================

const createProduct = (overrides = {}) => {
  const id = nextId();
  return {
    id,
    prod_name: `Test Product ${id}`,
    id_category: 1,
    id_subcategory: 1,
    hsn_code: "7113",
    purity: "22K",
    metal_type: "gold",
    making_type: "percentage",
    making_value: 12,
    wastage_type: "percentage",
    wastage_value: 8,
    status: 1,
    ...overrides,
  };
};

const createTaggingItem = (overrides = {}) => ({
  id: nextId(),
  tag_no: `TAG${String(nextId()).padStart(6, "0")}`,
  gross_wt: 10.5,
  net_wt: 9.8,
  stone_wt: 0.7,
  id_product: 1,
  id_design: 1,
  id_branch: 1,
  status: "available",
  location: "showcase",
  ...overrides,
});

// ============================================
// BILLING FACTORY
// ============================================

const createBill = (overrides = {}) => ({
  id: nextId(),
  id_branch: 1,
  cus_id: 101,
  bill_type: "sales",
  payment_mode: "cash",
  subtotal: 50000,
  discount: 0,
  tax_amount: 1500,
  grand_total: 51500,
  paid_amount: 51500,
  balance: 0,
  created_by: 1,
  bill_date: new Date().toISOString().split("T")[0],
  items: [],
  ...overrides,
});

const createBillItem = (overrides = {}) => ({
  id: nextId(),
  tag_no: `TAG${String(nextId()).padStart(6, "0")}`,
  gross_wt: 10.5,
  net_wt: 9.8,
  rate: 5000,
  amount: 49000,
  making_charge: 500,
  tax: 750,
  ...overrides,
});

// ============================================
// USER / AUTH FACTORY
// ============================================

const createUser = (overrides = {}) => {
  const id = nextId();
  return {
    id,
    username: `testuser${id}`,
    email: `user${id}@example.com`,
    name: `Test User ${id}`,
    id_profile: 2, // Staff
    id_branch: 1,
    status: 1,
    mobile: `98765${String(id).padStart(5, "0")}`,
    ...overrides,
  };
};

const createAdminUser = (overrides = {}) =>
  createUser({
    id_profile: 1, // Admin
    name: "Admin User",
    ...overrides,
  });

const createSession = (overrides = {}) => ({
  is_logged: true,
  uid: 1,
  profile: 1,
  branch_id: 1,
  username: "testuser",
  ...overrides,
});

// ============================================
// API RESPONSE MOCKS
// ============================================

const createSuccessResponse = (data, message = "Success") => ({
  status: "success",
  message,
  data,
});

const createErrorResponse = (message = "Error occurred", code = 400) => ({
  status: "error",
  message,
  code,
});

const createPaginatedResponse = (
  items,
  { page = 1, perPage = 10, total = null } = {}
) => ({
  status: "success",
  data: items,
  pagination: {
    page,
    per_page: perPage,
    total: total ?? items.length,
    total_pages: Math.ceil((total ?? items.length) / perPage),
  },
});

// ============================================
// EXPORTS
// ============================================

module.exports = {
  // Utilities
  resetFactories,
  nextId,

  // Estimation
  createEstimation,
  createEstimationItem,
  createEstimationWithItems,

  // Customer
  createCustomer,
  createCorporateCustomer,

  // Product
  createProduct,
  createTaggingItem,

  // Billing
  createBill,
  createBillItem,

  // User/Auth
  createUser,
  createAdminUser,
  createSession,

  // API Responses
  createSuccessResponse,
  createErrorResponse,
  createPaginatedResponse,
};
