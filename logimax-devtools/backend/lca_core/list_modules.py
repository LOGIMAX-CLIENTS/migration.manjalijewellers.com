from lca.tools.module_mapper import ModuleMapper
import json
import os

# Initialize mapper just to use its logic (or manual parse)
mapper = ModuleMapper("lca_index.json", "../admin/application")
# We want ALL files, not just connected ones.
# ModuleMapper doesn't expose file_map natively in a public way easily without analyze.
# So let's just parse the index directly for speed and accuracy.

with open("lca_index.json", "r") as f:
    data = json.load(f)

modules = set()
for func in data.get("functions", []):
    fpath = func.get("file")
    if not fpath: continue
    
    # Use mapper logic to name it
    # We need to mimic path splitting
    parts = fpath.replace("\\", "/").split("/")
    # Filter out .. and .
    parts = [p for p in parts if p not in ("..", ".")]
    
    mod_name = mapper.get_module_name(fpath) # logic reuse
    modules.add(mod_name)

print("\n[+] All Indexed Modules:")
print("=======================")
for mod in sorted(modules):
    print(f"- {mod}")
print("=======================")
print(f"Total: {len(modules)} Modules")
