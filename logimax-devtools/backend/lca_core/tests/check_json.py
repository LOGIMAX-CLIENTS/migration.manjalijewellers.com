import json
from pathlib import Path

def check_index(name, path):
    print(f"--- Checking {name} ---")
    if not Path(path).exists():
        print(f"❌ File not found: {path}")
        return

    with open(path, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    functions = data.get("functions", [])
    
    api_calls = 0
    db_calls = 0
    
    for func in functions:
        if "calls" in func:
            for call in func["calls"]:
                ctype = call.get("call_type")
                target = call.get("target")
                if ctype == "api_call":
                    api_calls += 1
                    print(f"  Found API Call: {target} (from {func['name']})")
                if ctype == "db_query":
                    db_calls += 1
                    print(f"  Found DB Call: {target} (from {func['name']})")
                if "TABLE:" in target:
                     # Fallback check if call_type wasn't set but target was
                     print(f"  Found TABLE Ref: {target}")

    print(f"Results: {api_calls} API calls, {db_calls} DB calls.\n")

check_index("JS Index", "estimation_real_index.json")
check_index("PHP Index", "estimation_php_index.json")
