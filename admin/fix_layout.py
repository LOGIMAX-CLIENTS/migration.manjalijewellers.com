import json

# Define the IDs of elements that should be relative
bottom_element_ids = ['i3oy6', 'i1m5m7', 'ifky4b', 'ibjcbr']

# Load original data from debug files
with open('e:/xampp/htdocs/etail_development_src/admin/template_45_debug_gjs.json', 'r') as f:
    gjs_data = json.load(f)

# 1. Update Styles in GJS JSON
for style_obj in gjs_data.get('styles', []):
    selectors = style_obj.get('selectors', [])
    for selector in selectors:
        # Check if selector is #id or .class that matches our bottom elements
        clean_id = selector.replace('#', '')
        if clean_id in bottom_element_ids or clean_id == 'dynamic-sales-table-v2' or clean_id == 'bill-footer-v2-block' or clean_id == 'logimax-footer-block':
            style = style_obj.get('style', {})
            style['position'] = 'relative'
            style['top'] = 'auto'
            style['left'] = '0'
            style['margin-top'] = '20px'
            style['width'] = '100%'
            style['clear'] = 'both'
            
            if clean_id == 'bill-footer-v2-block' or clean_id == 'i1m5m7':
                style['width'] = '100%'
                style['max-width'] = '100%'
            
            if clean_id == 'ibjcbr':
                style['margin-top'] = '40px'

# 2. Add Amount in Words styling fix
# Amount in words was appended to i1m5m7 in the previous step.
# We ensure the container is full width.
    
# 3. Regenerate HTML from Updated components (or just simple string replace for now)
# The safest way in GJS is usually to let GJS handle it, but since we are modifying DB:
html_content = gjs_data.get('gjs-html', '')

# We will perform string replacements to clean up the position:absolute from inline styles in HTML if any
# (Template 45 seems to use classes + IDs, and the GJS renderer puts styles in <style> block but also can have inlines)

# 4. Update CSS String
# We'll regenerate a clean CSS string from the styles array
css_lines = ["* { box-sizing: border-box; }", "body { margin: 0; }"]
for style_obj in gjs_data.get('styles', []):
    sel = ",".join(style_obj.get('selectors', []))
    rules = ";".join([f"{k}:{v}" for k,v in style_obj.get('style', {}).items()])
    css_lines.append(f"{sel} {{ {rules} }}")

updated_css = " ".join(css_lines)
gjs_data['gjs-css'] = updated_css

# 5. Save updated data
with open('e:/xampp/htdocs/etail_development_src/admin/template_45_fixed_gjs.json', 'w') as f:
    json.dump(gjs_data, f)
    
# We'll use the same gjs-html but the CSS will fix the layout. 
# However, let's ensure the Amount in Words is not squashed.
if '<div style="margin-top:10px; font-weight:bold; font-size:12px; text-align:right;">Amount in Words:' in html_content:
   # Already there from previous step
   pass

with open('e:/xampp/htdocs/etail_development_src/admin/template_45_fixed_html.html', 'w') as f:
    f.write(html_content)

with open('e:/xampp/htdocs/etail_development_src/admin/template_45_fixed_css.css', 'w') as f:
    f.write(updated_css)

print("Success: Fixed files generated.")
