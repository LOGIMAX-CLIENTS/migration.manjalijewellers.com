import json

def find_dependencies(target_class):
    with open('admin_app_index.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    print(f"Analyzing dependencies for {target_class}...")
    
    dependencies = set()
    
    # 1. Find functions belonging to this class
    class_functions = [f['name'] for f in data['functions'] if f['name'].startswith(target_class + '::')]
    
    # 2. Find calls made BY these functions
    for call in data['calls']:
        if call['source'] in class_functions:
            # Look for $this->load->model('x') patterns
            # The indexer might capture this as 'Loader::model' or similar depending on the parser
            # Or it might just be a generic call. Let's see what we have.
            if 'model' in call['target'].lower() or 'load' in call['target'].lower():
                 print(f"  Found call: {call['source']} -> {call['target']}")

find_dependencies("Admin_ret_estimation")
