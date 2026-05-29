from pathlib import Path
import json
from rich.console import Console
from rich.table import Table
from rich.panel import Panel

console = Console()

class Reaper:
    """The Dead Code Detector backend."""
    
    def __init__(self, index_path: str):
        self.index_path = Path(index_path)
        self.data = self._load_index()
        
    def _load_index(self) -> dict:
        if not self.index_path.exists():
            raise FileNotFoundError(f"Index not found: {self.index_path}")
        with open(self.index_path, "r", encoding="utf-8") as f:
            return json.load(f)

    def scan(self) -> list[dict]:
        """Find functions with 0 incoming calls."""
        
        # 1. Map all CALLS
        # target_name -> count
        incoming_counts = {}
        
        # Initialize all functions with 0
        all_funcs = self.data.get("functions", [])
        for f in all_funcs:
            incoming_counts[f["name"]] = 0
            
        # Count calls
        calls = self.data.get("calls", [])
        for c in calls:
            target = c.get("target")
            if target in incoming_counts:
                incoming_counts[target] += 1
                
        # 2. Filter for "Zombies"
        zombies = []
        for f in all_funcs:
            name = f["name"]
            file_path = f["file"]
            
            # SAFEGUARD: Ignore Controllers (Entry Points)
            # In CodeIgniter, Controllers are called by the Router, not other code.
            if "controllers" in file_path.lower():
                continue
                
            # SAFEGUARD: Ignore standard CI methods (index, __construct)
            if name in ["index", "__construct", "db_connect"]:
                continue

            if incoming_counts[name] == 0:
                zombies.append({
                    "name": name,
                    "file": file_path,
                    "line": f["line"],
                    "confidence": "Medium"  # Logic to be refined
                })
                
        # 3. GREPPER GUARD: Textual Verification
        # Only index files that are in the parsed index (avoiding vendor/node_modules if excluded)
        indexed_files = [f["path"] for f in self.data.get("files", [])]
        
        # Build token frequency map if not already cached (could be optimized)
        # For now, we do a "lazy" scan or a full scan? A full scan is safer.
        # To avoid massive latency, we only scan if we have zombies.
        
        if zombies:
            console.print(f"[yellow]Grepper Guard: Verifying {len(zombies)} candidates against {len(indexed_files)} files...[/yellow]")
            token_counts = self._build_token_counts(indexed_files)
            
            verified_zombies = []
            for z in zombies:
                name = z["name"]
                # Count in text (all files)
                text_hits = token_counts.get(name, 0)
                
                # If hit count > 1, it implies usage (1 definition + at least 1 usage)
                # We update confidence or remove it.
                if text_hits <= 1:
                    # High Confidence: Found in graph 0 times, found in text <= 1 time.
                    z["confidence"] = "High"
                    z["hits"] = text_hits
                    verified_zombies.append(z)
                else:
                    # Low Confidence: Graph says 0, but text says > 1. Likely dynamic.
                    # We can either exclude it or mark it as "Suspicious".
                    # User wants to be careful. Let's mark it "Dynamic?" and keep it but low severity.
                    z["confidence"] = "Low"
                    z["title"] = f"Dynamic?: {name}" 
                    z["hits"] = text_hits
                    verified_zombies.append(z)
            
            zombies = verified_zombies

        return zombies

    def _build_token_counts(self, file_paths: list[str]) -> dict:
        """Scan all files and count token occurrences."""
        import re
        from collections import Counter
        
        counts = Counter()
        # Regex for valid PHP identifiers (simple version)
        token_pattern = re.compile(r'[a-zA-Z_][a-zA-Z0-9_]*')
        
        for path_str in file_paths:
            try:
                path = Path(path_str)
                if not path.exists():
                    continue
                    
                # Skip massive files or binary
                if path.stat().st_size > 500 * 1024:  # 500KB limit
                    continue

                with open(path, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                    # Find all tokens
                    tokens = token_pattern.findall(content)
                    counts.update(tokens)
            except Exception:
                continue
                
        return counts

    def print_report(self, zombies: list[dict]):
        console.print(Panel(f"[bold red]Reaper Found {len(zombies)} Zombie Functions[/bold red]", border_style="red"))
        
        if not zombies:
            console.print("[green]No dead code detected! Clean codebase.[/green]")
            return

        table = Table(show_header=True, header_style="bold red")
        table.add_column("Function", style="white")
        table.add_column("File", style="dim")
        table.add_column("Line")
        
        # Show top 20
        for z in zombies[:20]:
            # shortening file path
            short_path = Path(z["file"]).name
            table.add_row(z["name"], short_path, str(z["line"]))
            
        console.print(table)
        if len(zombies) > 20:
            console.print(f"[dim]...and {len(zombies)-20} more[/dim]")

