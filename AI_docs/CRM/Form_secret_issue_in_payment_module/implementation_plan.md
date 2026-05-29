# Implementation Plan - Fix Payment Submission Bug (Invalid Form Submit)

The "Unable To Proceed Your Request.Invalid Form Submit" error occurs when the `form_secret` sent in a payment submission does not match the `FORM_SECRET` stored in the user's session. Currently, every time the payment form is loaded, it generates a new secret and overwrites the existing one in the session. This makes it impossible to submit the form from multiple tabs or after a page refresh in another tab.

## Proposed Changes

### [Component: Admin Payment]

#### [MODIFY] [admin_payment.php](file:///d:/xampp7.1/htdocs/nskjewels.com/admin/application/controllers/admin_payment.php)
Update the validation logic in `payment()` method for cases `general_advance` and `SaveAll` to check against a list of valid secrets instead of a single value.

#### [MODIFY] [form.php](file:///d:/xampp7.1/htdocs/nskjewels.com/admin/application/views/payment/form.php)
Update the secret generation logic at the top of the file to add the new secret to a list in the session rather than unsetting and overwriting a singular value.

---

### [Component: Helper]

#### [MODIFY] [formsecret_helper.php](file:///d:/xampp7.1/htdocs/nskjewels.com/admin/application/helpers/formsecret_helper.php)
Update the helper to provide multi-token support for future use across the application.

## Verification Plan

### Manual Verification
1.  **Multi-Tab Test**:
    *   Open the Payment page in Tab 1.
    *   Open the Payment page in Tab 2.
    *   Submit a payment from Tab 1.
    *   **Expected Result**: Payment should succeed (currently it fails).
    *   Submit a payment from Tab 2.
    *   **Expected Result**: Payment should succeed.

2.  **Double Submission Test**:
    *   Open the Payment page.
    *   Submit the payment.
    *   Immediately try to re-submit the same form (e.g., by clicking "Back" and Resubmit, or double-clicking if JS doesn't stop it).
    *   **Expected Result**: The second submission should fail with "Invalid Form Submit" (as the token should be consumed).

3.  **Basic Payment Test**:
    *   Ensure normal single-tab payment still works as expected.
