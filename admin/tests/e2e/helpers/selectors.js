/**
 * Selectors Registry — Central CSS/ID selectors for ETail v6
 * 
 * Update this file when element IDs change in views.
 * Tests import from here instead of hardcoding selectors.
 */

const SELECTORS = {

  // ======================
  // Payment Edit Page
  // ======================
  paymentEdit: {
    // Search
    billSearchInput:    '#paymentmode_ed',
    billSearchBtn:      '#billed_search',
    branchSelect:       '#branch_select',

    // Hidden fields
    hiddenBillId:       '#hidden_bill_id',
    billCusId:          '#bill_cus_id',

    // Customer details section
    customerName:       '#c_name',
    billCusName:        '#bill_cus_name',
    panNo:              '#pan_no',
    gstNum:             '#gst_num',
    aadhaarNo:          '#aadhaar_no',
    employeeSelect:     '#emp_select',
    employeeHidden:     '#id_employee',

    // Billing classification (B2C/B2B)
    billingForB2C:      "input[name='billing_for'][value='1']",  // Individual
    billingForB2B:      "input[name='billing_for'][value='2']",  // Company

    // Payment fields
    billedCash:         '#billed_cash',
    makeCashPay:        '#make_pay_cash',

    // Modals
    cardDetailModal:    '#card_detail_modal',
    chequeModal:        '#cheque_modal',
    netBankModal:       '#net_bank_modal',

    // Customer details section
    cusDetSection:      '#cusDet',

    // Buttons — these are dynamically created, use data attributes or text
    updateButtons:      '#cusDet button',
  },

  // ======================
  // Common / Toaster
  // ======================
  common: {
    toasterSuccess:     '.toast-success',
    toasterDanger:      '.toast-danger',
    toasterWarning:     '.toast-warning',
    // Generic toaster container
    toasterContainer:   '.toaster-container',
  },

  // ======================
  // Login Page
  // ======================
  login: {
    usernameInput:      'textbox >> "User Name"',
    passwordInput:      'textbox >> "Password"',
    branchSelect:       'combobox',
    signInBtn:          'button >> "Sign In"',
  },
};

module.exports = SELECTORS;
