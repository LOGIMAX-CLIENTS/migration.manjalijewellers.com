-- MD Dashboard Visibility & Job Work Mail Trigger After MD Approval
-- Adds "MD Pending" and "MD Approved" statuses to order_status_message

INSERT INTO order_status_message (id_order_msg, order_status, color, remark)
VALUES 
  (10, 'MD Pending', 'orange', 'Awaiting MD Approval'),
  (11, 'MD Approved', 'green', 'Approved by MD');
