# Data Flow traces: Customer Order 

## 1. Flow: Create Customer Order
1. **Trigger**: JS function attached to form submission on `/admin_ret_order/index` or from Cart checkout.
2. **Validation**: Primarily client-side validation logic inside `admin/assets/js/ret_reports.js` and custom inline scripts. 
3. **Endpoint**: `admin_ret_order::order('save')`
4. **Processing (`save`)**: 
   - Retrieves `ret_order_model::get_FinancialYear()`
   - Retrieves `ret_order_model::generateOrderNo()`
   - Prepares transaction via `$this->db->trans_begin()`.
   - Inserts header into `customerorder`
   - Loops through items array:
      - Inserts details into `customerorderdetails`
      - Inserts stones into `ret_order_item_stones`
      - Inserts tag logs into `ret_taging_status_log` if type is stock conversion
      - Evaluates and uploads images into file system, inserting mapping into `customer_order_image`
   - Commits transaction (`$this->db->trans_commit()`)
5. **Output**: Returns JSON `{status: TRUE, message: 'Order Created Successfully', id_customerorder: N}`.

## 2. Flow: Update Customer Order (EDIT)
1. **Trigger**: JS form submitted after pulling data into form.
2. **Endpoint**: `admin_ret_order::order('update')`
3. **Processing (`update`)**:
   - Updates `customerorder` via `$this->ret_order_model->updateData()`.
   - Loops through items:
      - Updates or Inserts into `customerorderdetails` based on sequence.
      - **Deletes and re-inserts** stones inside `ret_order_item_stones` (`deleteData("order_item_id", ...)`)
      - **Deletes and re-inserts** images in `customer_order_image` (`deleteData("id_orderdetails", ...)`)
   - Commits transaction.
4. **Risk Profile**: High risk of data loss due to soft mapping overwrites and `deleteData` on children. 

## 3. Flow: Shopping Cart Creation
1. **Trigger**: Adding items from stock/catalog.
2. **Endpoint**: `admin_ret_order::add_to_cart()`
3. **Processing**:
   - Maps inputs to `order_cart` schema.
   - Saves to `order_cart` tracking branch and product.
   - Status defaults to `0` (In Cart).
4. **Checkout Endpoint**: `admin_ret_order::cart('order_place')`
   - Migrates `order_cart` data to `customerorder` and `customerorderdetails`.
   - Updates status of `order_cart` to `1` (Placed).
   - Generates and sends vendor email with tokenized link (`ret_order_email_logs`).

## 4. Flow: Vendor Acknowledgement
1. **Trigger**: Vendor clicks email link.
2. **Endpoint**: External order acceptance controller `OrderAccept::index`
3. **Processing**: 
   - Token validated against `ret_order_email_logs`.
   - `orderstatus` updated to `3` in `customerorderdetails`.
   - Confirmation registered.

---

## 5. JS → Controller AJAX Map (`ret_reports.js`)

| JS Line | AJAX URL | HTTP | Controller Method | JS Context |
|---|---|---|---|---|
| L8581 | `/admin_ret_order/cart/list/` | GET | `cart('list')` | Cart page redirect |
| L8929 | `/admin_ret_order/add_to_cart` | POST | `add_to_cart()` | Add item to cart |
| L8954 | `/admin_ret_order/cart/save` | POST | `cart('save')` | Save cart items |
| L16220 | `/admin_ret_order/get_img_by_order_id` | POST | `get_img_by_order_id()` | New order list image preview |
| L40569 | `/admin_ret_order/customer_order_acknowladgement/{id}` | GET | `customer_order_acknowladgement()` | Link in estimation billing |

### Cross-Module AJAX (from JS to this controller)
| Source Context | URL | Purpose |
|---|---|---|
| `ret_reports.js` estimation billing block | `customer_order_acknowladgement` | Link to print customer order from estimation |

### Inline URLs (from view files)
| View File | URL | Purpose |
|---|---|---|
| `order/list.php` L26 | `/admin_ret_order/order/add` | "Add" button href |
| `repair_order/list.php` L25 | `/admin_ret_order/repair_order/add` | "Add Repair" button href |

---

## 6. View File Catalog (18 files)

### Root: `admin/application/views/order/`
| File | Lines | Purpose | Key Elements |
|---|---|---|---|
| `form.php` | 1722 | Main order create/edit form | 43 hidden fields, item table, 4 modals (Image, Description, Stone, Charges), webcam capture |
| `list.php` | 218 | Order list with DataTables | Branch/date filters, cancel OTP modal (3-step: confirm→send→verify), bulk image preview modal |
| `cart_list.php` | 298 | Cart items list + batch checkout | Product/Design/Weight/Karigar filters, Select-All checkbox, Order Place / Empty Cart buttons |
| `cart_status.php` | — | Cart status tracking | Status display for placed orders |
| `cus_order_print.php` | — | Customer order PDF print | DOMPDF rendered, customer details + items |
| `order_accept_public.php` | — | Public vendor acceptance page | Token-based access, accept/reject UI |
| `order_message.php` | — | Order status message display | Flash messages for order operations |

### Subdirectory: `neworder/`
| File | Purpose |
|---|---|
| `list.php` | New customer orders list (pending assignment) |

### Subdirectory: `repair_order/`
| File | Purpose |
|---|---|
| `form.php` | Repair order create/edit form (tag scan, damage types) |
| `item_details.php` | Repair order sub-item details (metals, stones, other materials) |
| `list.php` | Repair order list with DataTables |
| `neworders.php` | Pending repair orders for assignment |
| `order_status.php` | Repair order status tracking / bulk completion |
| `repair_print.php` | Current repair order PDF print template |
| `repair_print_old.php` | Legacy repair print template (deprecated) |

### Subdirectory: `stock_order/print/`
| File | Purpose |
|---|---|
| `vendor_ack.php` | PDF: Vendor/karigar acknowledgement print |

### Subdirectory: `supplier_catalog/`
| File | Purpose |
|---|---|
| `form.php` | Supplier catalog order form |
| `list.php` | Supplier catalog orders list |

### Form Modals in `form.php`
| Modal ID | Purpose | Key Inputs |
|---|---|---|
| `#imageModal` | Image upload (file + webcam) | `#order_images` (file), `#my_camera` (webcam), `#active_row` |
| `#order_des` | Add item description | `#description` textarea |
| `#cus_stoneModal` | Add/edit stones per item | `estimation_stone_cus_item_details` table (LWT, Type, Name, Pcs, Wt, Rate, Amount) |
| `#cus_other_charges_modal` | Add/edit other charges | `estimation_other_charge_cus_item_details` table (Charge Name, Value) |
