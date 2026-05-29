from pathlib import Path
import json
from collections import defaultdict
import glob
import os

from rich.console import Console
from rich.table import Table
from rich.panel import Panel

console = Console()

class ModuleMapper:
    """Analyzes index to show module-level dependencies."""
    
    def __init__(self, index_path: str, root_dir: str):
        self.index_data = self._load_index(index_path)
        self.root_dir = Path(root_dir).resolve()
        
    def _load_index(self, path: str) -> dict:
        with open(path, 'r', encoding='utf-8') as f:
            return json.load(f)
            
    def get_module_name(self, file_path: str) -> str:
        """
        Heuristic to determine module name from file path.
        Customize this for CodeIgniter structure.
        """
        if not file_path or file_path.startswith("API:") or file_path.startswith("TABLE:"):
            return file_path.split(":")[0] + " Services" # Group APIs and Tables
            
        try:
            path = Path(file_path).resolve()
            # Make relative to project root if possible
            if path.is_absolute():
                try:
                    rel = path.relative_to(self.root_dir)
                    parts = rel.parts
                except ValueError:
                    # File is outside root?
                    return "External"
            else:
                parts = Path(file_path).parts

            # Logic for etail_v3/admin
            # application/controllers/folder/File.php -> folder
            # application/models/File.php -> models
            # assets/js/folder/file.js -> js/folder
            
            parts_str = [str(p).lower() for p in parts]
            
            if "controllers" in parts_str:
                idx = parts_str.index("controllers")
                if len(parts) > idx + 1:
                    return f"Controller: {parts[idx+1]}"
                return "Controller: Root"
            
            if "models" in parts_str:
                return "Models"
                
            if "views" in parts_str:
                if len(parts) > idx + 1:
                    return f"View: {parts[idx+1]}"
                return "Views"

            # Fallback: Top level folder
            return str(parts[0])

        except Exception as e:
            # print(f"DEBUG ERROR: {e} for {file_path}")
            return "Unknown"

    def analyze(self):
        """Build the module graph."""
        module_deps = defaultdict(lambda: defaultdict(int)) # Source -> Target -> Count
        file_map = {} # Function Name -> File Path

        # 1. Build Function -> File map
        for func in self.index_data.get("functions", []):
            file_map[func["name"]] = func["file"]

        # 2. Analyze Calls
        calls = self.index_data.get("calls", [])
        
        for call in calls:
            # Resolve source file from function name
            source_func = call.get("source")
            source_file = file_map.get(source_func)
            
            # For target, we need to look it up if it says "calls" (it's a function name)
            # Or use 'target_file' if available (added in my previous edit)
            target_name = call.get("target")
            
            # Resolve target file
            target_file = call.get("target_file")
            
            # Special handling for API/Table nodes which don't have files per se
            if target_name.startswith("API:") or target_name.startswith("TABLE:"):
                target_file = target_name
            elif not target_file:
                 target_file = file_map.get(target_name)

            if not source_file or not target_file:
                continue
                
            source_mod = self.get_module_name(source_file)
            target_mod = self.get_module_name(target_file)
            
            if source_mod != target_mod:
                # print(f"DEBUG: {source_mod} -> {target_mod}")
                module_deps[source_mod][target_mod] += 1
        
        print(f"[DEBUG] Total Functions mapped: {len(file_map)}")
        print(f"[DEBUG] Total Calls processed: {len(calls)}")
        print(f"[DEBUG] Modules found: {len(module_deps)}")

        return module_deps

    def print_report(self, module_deps: dict):
        """Print the analysis report."""
        console.print(Panel.fit("[bold blue][#] Module Coupling Report[/bold blue]", subtitle="Architectural Dependencies"))
        
        if not module_deps:
            console.print("[yellow]No module dependencies found.[/yellow]")
            return
        
        # Sort by most active modules (outgoing calls)
        sorted_modules = sorted(module_deps.items(), key=lambda x: sum(x[1].values()), reverse=True)
        
        for source, targets in sorted_modules:
            total_calls = sum(targets.values())
            if total_calls == 0: continue
            
            table = Table(title=f"From [bold yellow]{source}[/bold yellow] ({total_calls} outbound)", box=None)
            table.add_column("To Module", style="cyan")
            table.add_column("Coupling Strength", justify="right", style="magenta")
            
            # helper to color code strength
            def get_icon(count):
                if count > 20: return "(!)"
                if count > 10: return "(!)"
                return "(*)"

            # Sort targets by count
            for target, count in sorted(targets.items(), key=lambda x: x[1], reverse=True):
                table.add_row(f"{target}", f"{count} {get_icon(count)}")
            
            console.print(table)
            console.print("")

if __name__ == "__main__":
    import sys
    if len(sys.argv) < 3:
        print("Usage: python module_mapper.py <index_json> <project_root>")
        sys.exit(1)
        
    mapper = ModuleMapper(sys.argv[1], sys.argv[2])
    deps = mapper.analyze()
    mapper.print_report(deps)
