import re

text = "index.php/admin_ret_estimation/getOrderBySearch/?nocache="
regex = r"index\.php\/([a-zA-Z0-9_]+)\/([a-zA-Z0-9_]+)(?:\/([a-zA-Z0-9_]+))?"

match = re.search(regex, text)
print(f"Testing text: '{text}'")
if match:
    print("✅ Match found!")
    print(f"Group 1 (Controller): {match.group(1)}")
    print(f"Group 2 (Method/Folder): {match.group(2)}")
    print(f"Group 3 (Method): {match.group(3)}")
else:
    print("❌ No match.")
