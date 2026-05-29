<div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 700px; margin: 0 auto; border: 1px solid #eee; padding: 20px;'>
    <div style='text-align: center; margin-bottom: 25px; border-bottom: 2px solid #6366f1; padding-bottom: 15px;'>
        <img src='<?php echo $logo_src; ?>' alt='Logo' style='max-height: 70px;'>
    </div>
    <h2 style='color: #4f46e5; margin-top: 0;'>New Purchase Order Received</h2>
    <p>Hello <b><?php echo $karigar['karigar_name']; ?></b>,</p>
    <p>A new purchase order has been placed with PO Number: <b>#<?php echo $order['pur_no']; ?></b>.</p>
    
    <table style='width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #fdfdfd;'>
        <thead>
            <tr style='background-color: #6366f1; color: white;'>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: center; font-size: 12px;'>Image</th>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>Product</th>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>Design</th>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>Sub Design</th>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: right; font-size: 12px;'>Approx. Wt</th>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: right; font-size: 12px;'>Pcs</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orderDetails as $itemDet): ?>
                <?php 
                    $img_src = base_url('assets/img/no_image.png');
                    if(!empty($itemDet['images'])) {
                        $img_name = $itemDet['images'][0]['image'];
                        $img_path_po = FCPATH . 'assets/img/order/purchase_order/' . $img_name;
                        $img_path_cus = FCPATH . 'assets/img/customer_order/' . $img_name;
                        
                        if(file_exists($img_path_po) || file_exists($img_path_cus)) {
                            $img_src = "cid:" . $img_name;
                        }
                    }
                ?>
                <tr>
                    <td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>
                        <img src='<?php echo $img_src; ?>' style='width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;'>
                    </td>
                    <td style='border: 1px solid #ddd; padding: 8px; font-size: 13px;'><?php echo $itemDet['product_name']; ?></td>
                    <td style='border: 1px solid #ddd; padding: 8px; font-size: 13px;'><?php echo $itemDet['design_name']; ?></td>
                    <td style='border: 1px solid #ddd; padding: 8px; font-size: 13px;'><?php echo $itemDet['sub_design_name']; ?></td>
                    <td style='border: 1px solid #ddd; padding: 8px; font-size: 13px; text-align: right;'><b><?php echo number_format($itemDet['weight'], 3); ?></b></td>
                    <td style='border: 1px solid #ddd; padding: 8px; font-size: 13px; text-align: right;'><?php echo $itemDet['tot_items']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p>Please click the button below to view the order details (including images) and confirm your delivery date:</p>
    <p style='text-align: center; margin: 30px 0;'>
        <a href='<?php echo $accept_url; ?>' style='background-color: #6366f1; color: white; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;'>View & Confirm Order</a>
    </p>
    <p style='font-size: 13px; color: #666;'>If the button doesn't work, copy and paste this link into your browser:</p>
    <p style='font-size: 13px;'><a href='<?php echo $accept_url; ?>' style='color: #6366f1;'><?php echo $accept_url; ?></a></p>
    <hr style='border: 0; border-top: 1px solid #eee; margin-top: 30px;'>
    <p style='font-size: 11px; color: #999; text-align: center;'>This is an automated message. Please do not reply directly to this email.</p>
</div>
