import os

file_path = r'd:\xampp\htdocs\etail_development_src2\admin\application\models\ret_reports_model.php'

with open(file_path, 'r', encoding='latin-1') as f:
    lines = f.readlines()

# Target lines for the esp subquery in get_product_stock_details
# Line 37799 is index 37798
lines[37798] = lines[37798].replace('esp.pcs as product_pieces', 'SUM(esp.pcs) as product_pieces')

# Also adding the product_pcs to the SELECT of get_product_nontag_stock_details
# Line 38077 is index 38076
if "IFNULL(sub.sub_design_name,'') as sub_design_name" in lines[38076]:
    lines[38076] = lines[38076].replace("IFNULL(sub.sub_design_name,'') as sub_design_name", "IFNULL(sub.sub_design_name,'') as sub_design_name, IFNULL(esp.product_pieces, 0) as product_pcs")

with open(file_path, 'w', encoding='latin-1') as f:
    f.writelines(lines)

print("Patch applied successfully.")
