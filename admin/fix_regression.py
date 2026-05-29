import json
import re

# Load regression data
with open('e:/xampp/htdocs/etail_development_src/admin/template_45_regression_gjs.json', 'r') as f:
    gjs_data = json.load(f)

html_content = gjs_data.get('gjs-html', '')

# 1. Identify and extract the footer block #ifky4b
# It looks like: <div ... id="ifky4b" ...>...</div>
footer_pattern = r'(<div data-display-rules="all" id="ifky4b" class="logimax-footer-block">.*?</div></div>)'
footer_match = re.search(footer_pattern, html_content, re.DOTALL)

if footer_match:
    footer_html = footer_match.group(1)
    # Remove it from the current position
    html_content = html_content.replace(footer_html, '')
    # Append it before </body>
    html_content = html_content.replace('</body>', footer_html + '</body>')

# 2. Update GJS Components (Page Data)
# We need to move the component in the JSON as well so the designer stays in sync
components = gjs_data['pages'][0]['frames'][0]['component']['components']
footer_comp = None
footer_idx = -1

for i, comp in enumerate(components):
    if comp.get('attributes', {}).get('id') == 'ifky4b':
        footer_comp = comp
        footer_idx = i
        break

if footer_comp:
    components.pop(footer_idx)
    components.append(footer_comp)

# 3. Adjust CSS for Flow
# Table (#i3oy6) needs margin-top to clear absolute header
# Header elements go up to approx 240px
for style_obj in gjs_data.get('styles', []):
    selectors = style_obj.get('selectors', [])
    style = style_obj.get('style', {})
    
    if '#i3oy6' in selectors or 'dynamic-sales-table-v2' in selectors:
        style['margin-top'] = '250px' # Added clearance for header
        style['position'] = 'relative'
        style['top'] = 'auto'
    
    if '#i1m5m7' in selectors or 'bill-footer-v2-block' in selectors:
        style['margin-top'] = '20px'
        style['position'] = 'relative'
        style['top'] = 'auto'
        style['float'] = 'right' # Totals should be on the right
        style['width'] = 'auto' # Don't force 100% if we want it on the right
        style['min-width'] = '300px'

    if '#ifky4b' in selectors or 'logimax-footer-block' in selectors:
        style['margin-top'] = '40px'
        style['position'] = 'relative'
        style['top'] = 'auto'
        style['clear'] = 'both' # Ensure it starts after floated totals
        style['width'] = '100%'

# 4. Regenerate CSS string
css_lines = ["* { box-sizing: border-box; }", "body { margin: 0; }"]
for style_obj in gjs_data.get('styles', []):
    sel = ",".join(style_obj.get('selectors', []))
    rules = ";".join([f"{k}:{v}" for k,v in style_obj.get('style', {}).items()])
    css_lines.append(f"{sel} {{ {rules} }}")

updated_css = " ".join(css_lines)
gjs_data['gjs-css'] = updated_css
gjs_data['gjs-html'] = html_content

# 5. Save fixed data
with open('e:/xampp/htdocs/etail_development_src/admin/template_45_final_gjs.json', 'w') as f:
    json.dump(gjs_data, f)
    
with open('e:/xampp/htdocs/etail_development_src/admin/template_45_final_html.html', 'w') as f:
    f.write(html_content)

with open('e:/xampp/htdocs/etail_development_src/admin/template_45_final_css.css', 'w') as f:
    f.write(updated_css)

print("Success: Final fixed files generated.")
