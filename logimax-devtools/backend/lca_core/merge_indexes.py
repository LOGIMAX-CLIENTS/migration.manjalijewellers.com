
import json
import sys
from pathlib import Path

def merge(files: list, output_path: str):
    merged = {"functions": [], "calls": [], "files": []}
    
    for path in files:
        if not path: continue
        print(f"Loading Index: {path}")
        try:
            with open(path, 'r', encoding='utf-8') as f:
                data = json.load(f)
                merged['functions'].extend(data.get('functions', []))
                merged['calls'].extend(data.get('calls', []))
                merged['files'].extend(data.get('files', []))
        except FileNotFoundError:
            print(f"Warning: {path} not found. Skipping.")

    # Update Stats
    print(f"Total Functions: {len(merged['functions'])}")
    print(f"Total Calls: {len(merged['calls'])}")
    
    print(f"Saving Merged Index: {output_path}")
    with open(output_path, 'w', encoding='utf-8') as f:
        json.dump(merged, f, indent=2)

if __name__ == "__main__":
    if len(sys.argv) < 3:
        # Default fallback
        inputs = ["lca_index.json", "js_index.json", "admin_js_index.json"]
        merge(inputs, "master_index.json")
    else:
        # Last arg is output, rest are inputs
        merge(sys.argv[1:-1], sys.argv[-1])
