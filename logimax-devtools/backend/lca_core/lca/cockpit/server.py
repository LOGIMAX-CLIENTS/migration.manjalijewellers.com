"""
LCA Cockpit Server
A local web UI for Code Intelligence Platform.
"""
from pathlib import Path
from fastapi import FastAPI, Request, HTTPException
from fastapi.responses import HTMLResponse
from fastapi.staticfiles import StaticFiles
from fastapi.templating import Jinja2Templates
from fastapi.middleware.cors import CORSMiddleware
import uvicorn
import json

from lca_core.lca.tools.reaper import Reaper
from lca_core.lca.tools.module_mapper import ModuleMapper

app = FastAPI(title="LCA Cockpit", description="Code Intelligence Platform")

# Enable CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# Setup directories
COCKPIT_DIR = Path(__file__).parent
app.mount("/static", StaticFiles(directory=COCKPIT_DIR / "static"), name="static")
templates = Jinja2Templates(directory=COCKPIT_DIR / "templates")

# Global state
_current_index_path: str | None = None
_index_data: dict | None = None
_reaper_instance: Reaper | None = None
_mapper_instance: ModuleMapper | None = None

def load_index_data():
    """Load index and initialize tools."""
    global _index_data, _reaper_instance, _mapper_instance
    if not _current_index_path:
        return
    
    print(f"[+] Loading Index: {_current_index_path}")
    
    # 1. Load Raw JSON
    try:
        with open(_current_index_path, 'r', encoding='utf-8') as f:
            _index_data = json.load(f)
    except Exception as e:
        print(f"[!] Error loading index JSON: {e}")
        _index_data = {}

    # 2. Initialize Reaper
    try:
        _reaper_instance = Reaper(_current_index_path)
    except Exception as e:
        print(f"[!] Reaper init failed: {e}")

    # 3. Initialize ModuleMapper
    # Infer root from index location (assuming index is in lca root, project is parent)
    try:
        project_root = str(Path(_current_index_path).resolve().parent.parent)
        _mapper_instance = ModuleMapper(_current_index_path, project_root)
    except Exception as e:
        print(f"[!] Mapper init failed: {e}")


@app.on_event("startup")
async def startup_event():
    load_index_data()


@app.get("/", response_class=HTMLResponse)
async def dashboard(request: Request):
    """Render the main SPA."""
    return templates.TemplateResponse("index.html", {"request": request})


# --- API ENDPOINTS ---

@app.get("/api/search")
async def search(q: str):
    """Instant search for functions."""
    if not _index_data:
        return {"results": []}
    
    results = []
    q_lower = q.lower()
    functions = _index_data.get("functions", [])
    
    count = 0
    # Simple linear scan suitable for <100k items.
    # For larger scale, build a suffix tree or trie in load_index_data.
    for fn in functions:
        name = fn.get("name", "")
        if q_lower in name.lower():
            results.append({
                "type": "function",
                "name": name,
                "file": fn.get("file", "unknown"),
                "line": fn.get("line", 0)
            })
            count += 1
            if count >= 50: break
    
    return {"results": results}


@app.get("/api/impact")
async def api_impact(function_name: str, depth: int = 1):
    """Get callers for impact analysis (Reverse Dependency) with graph structure."""
    if not _index_data:
        return {"nodes": [], "edges": []}
    
    # Graph Data Structures
    nodes = {} # id -> node_obj
    edges = [] # list of edge_objs
    visited = set()
    
    # Helper: Add Node
    def add_node(name, group="default"):
        if name not in nodes:
            nodes[name] = {
                "id": name,
                "label": name,
                "group": group,
                "value": 10 if name == function_name else 5 # Bigger size for center
            }

    # Helper: Find Function File (for grouping)
    def get_file_type(name):
        # Check explicit functions list
        for f in _index_data.get("functions", []):
            if f["name"] == name:
                if ".js" in f["file"]: return "javascript"
                if ".php" in f["file"]: return "php"
        # Guess from name
        if "::" in name: return "php" 
        return "javascript" 

    # 1. Central Node
    add_node(function_name, get_file_type(function_name))
    
    calls = _index_data.get("calls", [])
    
    # 2. Upstream (Callers) - Who calls THIS?
    # Simple BFS for Upstream
    current_layer = {function_name}
    for _ in range(depth):
        next_layer = set()
        for c in calls:
            target = c.get("target")
            source = c.get("source")
            
            # Exact match or Class::Method match
            if target in current_layer:
                if source not in nodes:
                    add_node(source, get_file_type(source))
                    nodes[source]["level"] = -1 # Upstream
                    next_layer.add(source)
                
                # Prevent duplicate edges
                edge_id = f"{source}->{target}"
                if edge_id not in visited:
                    edges.append({
                        "from": source, 
                        "to": target, 
                        "arrows": "to",
                        "color": {"color": "#6366f1"} # Indigo for Calls
                    })
                    visited.add(edge_id)
        current_layer = next_layer

    # 3. Downstream (Dependencies) - Who does THIS call?
    # Limits: Only 1 level deep for downstream to avoid explosion
    for c in calls:
        if c.get("source") == function_name:
            target = c.get("target")
            add_node(target, get_file_type(target))
            nodes[target]["level"] = 1 # Downstream
            
            edge_id = f"{function_name}->{target}"
            if edge_id not in visited:
                edges.append({
                    "from": function_name, 
                    "to": target, 
                    "arrows": "to",
                    "color": {"color": "#10b981"} # Emerald for Verified/Called
                })
                visited.add(edge_id)

    # Count upstream and downstream
    upstream_count = sum(1 for n in nodes.values() if n.get("level") == -1)
    downstream_count = sum(1 for n in nodes.values() if n.get("level") == 1)
    total_impact = upstream_count + downstream_count
    
    # Build lists for UI panels
    upstream_list = [{"name": n["id"], "group": n.get("group", "default")} 
                     for n in nodes.values() if n.get("level") == -1]
    downstream_list = [{"name": n["id"], "group": n.get("group", "default")} 
                       for n in nodes.values() if n.get("level") == 1]
    
    # Risk Assessment
    if total_impact > 10:
        risk_level = "HIGH"
    elif total_impact > 5:
        risk_level = "MEDIUM"
    else:
        risk_level = "LOW"
    
    # Find Definition Location
    definition = None
    for f in _index_data.get("functions", []):
        if f["name"] == function_name:
            definition = {"file": f["file"], "line": f["line"]}
            break

    return {
        "nodes": list(nodes.values()),
        "edges": edges,
        "stats": {
            "upstream": upstream_count,
            "downstream": downstream_count,
            "total": total_impact
        },
        "riskLevel": risk_level,
        "definition": definition,
        "upstreamList": upstream_list,
        "downstreamList": downstream_list
    }





@app.get("/api/modules")
async def modules():
    """Get module dependency graph."""
    if not _mapper_instance:
        return {"nodes": [], "edges": []}
        
    deps = _mapper_instance.analyze()
    
    nodes = []
    edges = []
    
    # 'deps' is { "ModuleA": { "ModuleB": count } }
    
    for mod_name, connections in deps.items():
        # Scale node size by complexity (outgoing connections)
        size = 10 + len(connections) * 2
        nodes.append({"id": mod_name, "label": mod_name, "value": size, "group": "module"})
        
        for target, count in connections.items():
            edges.append({
                "from": mod_name, 
                "to": target, 
                "value": count, 
                "title": f"{count} calls",
                "arrows": "to"
            })
            
    return {"nodes": nodes, "edges": edges}


@app.get("/api/suggestions")
async def get_suggestions(page: int = 1, limit: int = 50, q: str = ""):
    """Get dead code suggestions."""
    if not _reaper_instance:
         return {"total": 0, "suggestions": []}
         
    # Need to handle scan caching
    # For now, simplistic scan call
    try:
        zombies = _reaper_instance.scan() # logic inside reaper caches? no, we need to fix reaper.
        # But let's assume it works for POC.
        
        # Filter
        filtered = []
        for z in zombies:
             if q and q.lower() not in z['name'].lower():
                 continue
             filtered.append(z)
             
        # Pagination
        start = (page - 1) * limit
        res = filtered[start:start+limit]
        
        return {
            "total": len(filtered),
            "page": page,
            "suggestions": res
        }
    except Exception as e:
        print(f"Reaper Error: {e}")
        return {"total": 0, "suggestions": []}


def run_server(index_path: str, port: int = 8000):
    """Launch the Cockpit server."""
    global _current_index_path
    _current_index_path = index_path
    
    print(f"[+] LCA Cockpit starting at http://127.0.0.1:{port}")
    uvicorn.run(app, host="127.0.0.1", port=port, log_level="warning")
