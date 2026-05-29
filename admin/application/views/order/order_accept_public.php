<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Acknowledgement - <?php echo $order[0]['pur_no']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            padding: 20px;
            color: #333;
            min-height: 100vh;
        }

        .document {
            border-radius: 10px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        /* Header with Company Details */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            border-bottom: 4px solid #5a67d8;
        }

        .company-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .company-left h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .company-left p {
            font-size: 13px;
            line-height: 1.6;
            opacity: 0.95;
            max-width: 400px;
        }

        .company-right {
            text-align: right;
            font-size: 13px;
            line-height: 1.8;
        }

        .doc-title {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            padding: 15px 0 0 0;
            border-top: 1px solid rgba(255,255,255,0.2);
            margin-top: 15px;
        }

        /* Order Info Section */
        .order-info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 5px 20px;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 11px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: #212529;
        }

        /* Table Container - Scrollable if more than 5 records */
        .table-container {
            padding: 0 40px;
            margin: 25px 0;
            max-height: 350px; /* Approximately 5 rows + header */
            overflow-y: auto;
        }

        .table-container::-webkit-scrollbar {
            width: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Compact Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            white-space: nowrap;
        }

        thead {
            position: sticky;
            top: 0;
            background: #495057;
            color: white;
            z-index: 10;
        }

        th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid white;
        }

        tbody tr {
            border-bottom: 1px solid #dee2e6;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        td {
            padding: 10px;
            border: 1px solid #dee2e6;
            color: #495057;
            vertical-align: middle;
        }

        .item-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ddd;
            background: #fdfdfd;
        }

        /* S.No column styling */
        th:first-child, td:first-child {
            width: 60px;
            text-align: center;
        }

        /* Form Section */
        .form-section {
            padding: 30px 40px;
            background: #fff;
            border-radius: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #495057;
            font-size: 14px;
        }

        input[type="date"], textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.3s;
        }

        input[type="date"]:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Flex Buttons */
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Reject Form (Hidden by default) */
        .reject-form {
            display: none;
            padding: 30px 40px;
            background: #fff3cd;
            border-top: 3px solid #ffc107;
        }

        /* Success/Reject Messages */
        .success-msg, .reject-msg {
            display: none;
            text-align: center;
            padding: 60px 40px;
        }

        .success-msg i, .reject-msg i {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .success-msg i {
            color: #28a745;
        }

        .reject-msg i {
            color: #dc3545;
        }

        .success-msg h2, .reject-msg h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .success-msg p, .reject-msg p {
            font-size: 16px;
            color: #6c757d;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .form-section, .reject-form {
                display: none !important;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="document" id="mainContainer">
        <!-- Header with Company Details -->
        <div class="header">
            <div class="company-info">
                <div class="company-left" style="display: flex; align-items: center; gap: 20px;">
                    <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="Logo" style="height: 75px; background: white; padding: 5px; border-radius: 8px;">
                    <div>
                        <h1><?php echo $company['comp_name_in_sms']; ?></h1>
                        <p><?php echo nl2br($company['address']); ?></p>
                    </div>
                </div>
                <div class="company-right">
                    <div><strong>Phone:</strong> <?php echo $company['phone']; ?></div>
                    <div><strong>Email:</strong> <?php echo $company['email']; ?></div>
                </div>
            </div>
            <div class="doc-title">PURCHASE ORDER ACKNOWLEDGEMENT</div>
        </div>

        <!-- Order Information -->
        <div class="order-info">
            <div class="info-item">
                <span class="info-label">PO Number</span>
                <span class="info-value"><?php echo $order[0]['pur_no']; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Order Date</span>
                <span class="info-value"><?php echo $order[0]['order_date']; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Supplier</span>
                <span class="info-value"><?php echo $order[0]['karigar_name']; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Due Date</span>
                <span class="info-value"><?php echo $order[0]['smith_due_date']; ?></span>
            </div>
        </div>

        <!-- Scrollable Order Items Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Product</th>
                        <th style="text-align: center;">Image</th>
                        <th>Design</th>
                        <th>Sub Design</th>
                        <th>Size</th>
                        <th>Approx. Wt</th>
                        <th>Weight Range</th>
                        <th>Pieces</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sno = 1; foreach($order as $item): ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td><?php echo $item['product_name']; ?></td>
                        <td style="text-align: center;">
                            <?php 
                            if(!empty($item['images'])): 
                                $img_name = $item['images'][0]['image'];
                                // Try purchase order path first, then customer order path
                                $po_path = 'assets/img/order/purchase_order/' . $img_name;
                                $cus_path = 'assets/img/customer_order/' . $img_name;
                            ?>
                                <img src="<?php echo base_url($po_path); ?>" class="item-img" alt="Item Image" onerror="this.onerror=null;this.src='<?php echo base_url($cus_path); ?>';this.setAttribute('onerror', 'this.src=\'<?php echo base_url('assets/img/no_image.png'); ?>\'');">
                            <?php else: ?>
                                <img src="<?php echo base_url('assets/img/no_image.png'); ?>" class="item-img" alt="No Image">
                            <?php endif; ?>
                        </td>
                        <td><?php echo $item['design_name']; ?></td>
                        <td><?php echo $item['sub_design_name']; ?></td>
                        <td><?php echo $item['size']; ?></td>
                        <td style="text-align: right;"><b><?php echo $item['weight']; ?></b></td>
                        <td><?php echo $item['weight_range_des']; ?></td>
                        <td style="text-align: right;"><?php echo $item['tot_items']; ?></td>
                        <td><?php echo $item['smith_due_date']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Accept Form -->
        <div class="form-section">
            <form id="acceptForm">
                <input type="hidden" name="token" value="<?php echo $log['token']; ?>">
                <div class="form-group">
                    <label for="due_date"><i class="fas fa-calendar-alt"></i> Confirm Expected Delivery Date</label>
                    <input type="date" id="due_date" name="due_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle"></i> Accept Order
                    </button>
                    <button type="button" class="btn btn-danger" id="showRejectBtn">
                        <i class="fas fa-times-circle"></i> Reject Order
                    </button>
                </div>
            </form>
        </div>

        <!-- Reject Form (Hidden) -->
        <div class="reject-form" id="rejectForm">
            <form id="rejectFormSubmit">
                <input type="hidden" name="token" value="<?php echo $log['token']; ?>">
                <div class="form-group">
                    <label for="reason"><i class="fas fa-comment-alt"></i> Reason for Rejection <span style="color: red;">*</span></label>
                    <textarea id="reason" name="reason" placeholder="Please provide a reason for rejecting this order..." required></textarea>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i> Confirm Rejection
                    </button>
                    <button type="button" class="btn btn-secondary" id="cancelRejectBtn">
                        <i class="fas fa-undo"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Message -->
    <div class="document success-msg" id="successMsg">
        <i class="fas fa-check-circle"></i>
        <h2>Order Accepted Successfully!</h2>
        <p>Thank you for confirming the order. The system has been updated with your delivery date.</p>
    </div>

    <!-- Reject Message -->
    <div class="document reject-msg" id="rejectMsg">
        <i class="fas fa-times-circle"></i>
        <h2>Order Rejected</h2>
        <p>The order has been rejected and the system has been updated accordingly.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Accept Order
        $('#acceptForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: '<?php echo base_url("index.php/OrderAccept/accept"); ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if(res.status) {
                        $('#mainContainer').fadeOut(300, function() {
                            $('#successMsg').fadeIn();
                        });
                    } else {
                        alert(res.msg);
                        btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Accept Order');
                    }
                },
                error: function() {
                    alert('Something went wrong. Please try again.');
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Accept Order');
                }
            });
        });

        // Show Reject Form
        $('#showRejectBtn').on('click', function() {
            $('#rejectForm').slideDown();
            $(this).hide();
        });

        // Cancel Reject
        $('#cancelRejectBtn').on('click', function() {
            $('#rejectForm').slideUp();
            $('#showRejectBtn').show();
        });

        // Reject Order
        $('#rejectFormSubmit').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: '<?php echo base_url("index.php/OrderAccept/reject"); ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if(res.status) {
                        $('#mainContainer').fadeOut(300, function() {
                            $('#rejectMsg').fadeIn();
                        });
                    } else {
                        alert(res.msg);
                        btn.prop('disabled', false).html('<i class="fas fa-ban"></i> Confirm Rejection');
                    }
                },
                error: function() {
                    alert('Something went wrong. Please try again.');
                    btn.prop('disabled', false).html('<i class="fas fa-ban"></i> Confirm Rejection');
                }
            });
        });
    </script>
</body>
</html>
