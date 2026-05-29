"""Logimax Code Analyzer CLI."""

from pathlib import Path
from typing import Optional

import typer
from rich.console import Console
from rich.table import Table
from rich.panel import Panel

from lca_core.lca.core import Indexer, ImpactAnalyzer
from lca_core.lca.storage import save_index, load_index

app = typer.Typer(
    name="lca",
    help="Logimax Code Analyzer - Code analysis and impact tracking tool",
    add_completion=False,
)
console = Console()


@app.command()
def index(
    path: Path = typer.Argument(..., help="Path to file or directory to index"),
    module: str = typer.Option(..., "--module", "-m", help="Module name for the index"),
    output: Optional[Path] = typer.Option(None, "--output", "-o", help="Output JSON file path"),
    recursive: bool = typer.Option(True, "--recursive/--no-recursive", "-r/-R", help="Recurse into subdirectories"),
):
    """Index a file or directory and generate a dependency map."""
    if not path.exists():
        console.print(f"[red]Error:[/red] Path does not exist: {path}")
        raise typer.Exit(1)
    
    console.print(f"[cyan]Indexing:[/cyan] {path}")
    
    indexer = Indexer()
    
    if path.is_file():
        result = indexer.index_file(path, module)
    else:
        result = indexer.index_directory(path, module, recursive=recursive)
    
    # Determine output path
    if output is None:
        output = Path(f"./{module}_index.json")
    
    save_index(result, output)
    
    # Display summary
    console.print(Panel.fit(
        f"[green][+][/green] Indexed [bold]{len(result.functions)}[/bold] functions\n"
        f"[green][+][/green] Found [bold]{len(result.calls)}[/bold] call relationships\n"
        f"[green][+][/green] Output: [cyan]{output}[/cyan]",
        title=f"Module: {module}",
        border_style="green"
    ))


@app.command()
def impact(
    function: str = typer.Argument(..., help="Function name to analyze"),
    index_file: Path = typer.Option(..., "--index", "-i", help="Path to index JSON file"),
    depth: int = typer.Option(2, "--depth", "-d", help="Depth of dependency analysis"),
):
    """Analyze the impact of changes to a function."""
    if not index_file.exists():
        console.print(f"[red]Error:[/red] Index file does not exist: {index_file}")
        raise typer.Exit(1)
    
    idx = load_index(index_file)
    analyzer = ImpactAnalyzer(idx)
    result = analyzer.analyze(function, depth=depth)
    
    # Risk badge
    risk_colors = {"Low": "green", "Medium": "yellow", "High": "red"}
    risk_color = risk_colors.get(result.risk_level, "white")
    
    console.print(Panel.fit(
        f"[bold]{result.term}[/bold] | Risk: [{risk_color}]{result.risk_level}[/{risk_color}]",
        title="Impact Analysis",
        border_style="cyan"
    ))
    
    # Callers table
    callers = [c for c in result.calls if c["type"] == "called_by"]
    if callers:
        table = Table(title="Called By (Upstream)", show_header=True)
        table.add_column("Function", style="magenta")
        table.add_column("Line", style="dim")
        for c in callers[:10]:
            table.add_row(c["target"], str(c["line"]))
        if len(callers) > 10:
            table.add_row(f"... +{len(callers) - 10} more", "")
        console.print(table)
    
    # Calls table
    calls = [c for c in result.calls if c["type"] == "calls"]
    if calls:
        table = Table(title="Calls (Downstream)", show_header=True)
        table.add_column("Function", style="blue")
        table.add_column("Line", style="dim")
        for c in calls[:10]:
            table.add_row(c["target"], str(c["line"]))
        if len(calls) > 10:
            table.add_row(f"... +{len(calls) - 10} more", "")
        console.print(table)
    
    # Definition
    if result.functions:
        f = result.functions[0]
        console.print(f"\n[dim]Defined at:[/dim] {f.file}:{f.line}")


@app.command()
def search(
    query: str = typer.Argument(..., help="Search query (supports wildcards)"),
    index_file: Path = typer.Option(..., "--index", "-i", help="Path to index JSON file"),
    limit: int = typer.Option(20, "--limit", "-l", help="Maximum results"),
):
    """Search for functions by name."""
    if not index_file.exists():
        console.print(f"[red]Error:[/red] Index file does not exist: {index_file}")
        raise typer.Exit(1)
    
    idx = load_index(index_file)
    
    # Simple search (case-insensitive contains)
    query_lower = query.lower().replace("*", "")
    matches = [f for f in idx.functions if query_lower in f.name.lower()][:limit]
    
    if not matches:
        console.print(f"[yellow]No functions found matching:[/yellow] {query}")
        return
    
    table = Table(title=f"Functions matching '{query}'", show_header=True)
    table.add_column("Function", style="cyan")
    table.add_column("File", style="dim")
    table.add_column("Line", style="dim")
    table.add_column("Language", style="magenta")
    
    for f in matches:
        table.add_row(f.name, Path(f.file).name, str(f.line), f.language)
    
    console.print(table)


@app.command()
def crossref(
    path: Path = typer.Argument(..., help="Path to JS file or directory"),
    output: Optional[Path] = typer.Option(None, "--output", "-o", help="Output JSON file (optional)"),
):
    """Extract JS→PHP API calls (cross-language dependencies)."""
    from lca.parsers.crossref import extract_api_calls
    import json
    
    if not path.exists():
        console.print(f"[red]Error:[/red] Path does not exist: {path}")
        raise typer.Exit(1)
    
    console.print(f"[cyan]Analyzing:[/cyan] {path}")
    
    # Collect all JS files
    if path.is_file():
        files = [path] if path.suffix in ('.js', '.jsx', '.mjs') else []
    else:
        files = list(path.rglob("*.js")) + list(path.rglob("*.jsx"))
    
    all_calls = []
    for f in files:
        calls = extract_api_calls(f)
        all_calls.extend(calls)
    
    # Group by controller
    controllers = {}
    for call in all_calls:
        key = call.controller
        if key not in controllers:
            controllers[key] = []
        controllers[key].append(call)
    
    # Display results
    console.print(Panel.fit(
        f"[green][+][/green] Found [bold]{len(all_calls)}[/bold] API calls\n"
        f"[green][+][/green] To [bold]{len(controllers)}[/bold] PHP controllers",
        title="Cross-Reference Analysis",
        border_style="blue"
    ))
    
    for ctrl, calls in sorted(controllers.items()):
        table = Table(title=f"Controller: {ctrl}", show_header=True)
        table.add_column("JS Function", style="cyan")
        table.add_column("PHP Method", style="magenta")
        table.add_column("Line", style="dim")
        table.add_column("HTTP", style="yellow")
        
        for c in calls[:10]:
            table.add_row(c.source_function, c.method, str(c.line), c.http_method)
        if len(calls) > 10:
            table.add_row(f"... +{len(calls) - 10} more", "", "", "")
        console.print(table)
    
    # Save if output specified
    if output:
        data = {
            "api_calls": [
                {
                    "source": c.source_function,
                    "controller": c.controller,
                    "method": c.method,
                    "line": c.line,
                    "file": c.file,
                    "http_method": c.http_method
                }
                for c in all_calls
            ]
        }
        with open(output, "w", encoding="utf-8") as f:
            json.dump(data, f, indent=2)
        console.print(f"[green]✓[/green] Saved to: {output}")


@app.command()
def serve(
    port: int = typer.Option(3100, "--port", "-p", help="Port for MCP server (not used in stdio mode)"),
):
    """Start the MCP server for AI agent integration (stdio mode)."""
    # NOTE: Do NOT print anything to stdout - MCP uses stdio for protocol communication
    # Any output here will corrupt the MCP handshake
    import sys
    sys.stderr.write("LCA MCP Server starting in stdio mode...\n")
    sys.stderr.flush()
    
    from lca.mcp_server import run_server
    run_server()


@app.command()
def watch(
    path: Path = typer.Argument(..., help="Path to file or directory to watch"),
    module: str = typer.Option(..., "--module", "-m", help="Module name for the index"),
    output: Optional[Path] = typer.Option(None, "--output", "-o", help="Output JSON file path"),
    debounce: float = typer.Option(2.0, "--debounce", "-d", help="Seconds to wait before reindexing"),
):
    """Watch files and reindex on changes (live mode)."""
    if not path.exists():
        console.print(f"[red]Error:[/red] Path does not exist: {path}")
        raise typer.Exit(1)
    
    from lca.watcher import watch_and_index
    
    watch_and_index(
        path=path,
        module_name=module,
        output_path=output,
        debounce_seconds=debounce,
    )



@app.command()
def modules(
    index_file: Path = typer.Option(..., "--index", "-i", help="Path to index JSON file"),
    root: Path = typer.Option(..., "--root", "-r", help="Project root directory (for relative paths)"),
):
    """Analyze high-level architecture: dependencies between modules."""
    if not index_file.exists():
        console.print(f"[red]Error:[/red] Index file does not exist: {index_file}")
        raise typer.Exit(1)
        
    from lca.tools.module_mapper import ModuleMapper
    
    console.print(f"[cyan]Mapping Modules using index:[/cyan] {index_file}")
    
    mapper = ModuleMapper(str(index_file), str(root))
    deps = mapper.analyze()
    mapper.print_report(deps)
    
@app.command()
def reaper(
    index_file: Optional[Path] = typer.Option(None, "--index", "-i", help="Path to index JSON file"),
):
    """Detect dead code (functions with zero usages)."""
    # 1. Default to lca_index.json if not specified
    if not index_file:
        index_file = Path("lca_index.json")

    # 2. Check existence
    if not index_file.exists():
        console.print(f"[red]Error:[/red] Index file not found: {index_file}")
        console.print(f"[yellow]Tip:[/yellow] Run 'lca index ...' first.")
        raise typer.Exit(1)
        
    from lca.tools.reaper import Reaper
    
    console.print(f"[red]Awakening The Reaper...[/red]")
    
    tool = Reaper(str(index_file))
    zombies = tool.scan()
    tool.print_report(zombies)

@app.command()
def ui(
    index_file: Path = typer.Option(..., "--index", "-i", help="Path to index JSON file"),
    port: int = typer.Option(8000, "--port", "-p", help="Server port"),
):
    """Launch the LCA Cockpit web UI."""
    if not index_file.exists():
        console.print(f"[red]Error:[/red] Index file does not exist: {index_file}")
        raise typer.Exit(1)
        
    from lca.cockpit.server import run_server
    
    console.print(f"[cyan]Launching LCA Cockpit...[/cyan]")
    run_server(str(index_file), port=port)

@app.command()
def gen_test(
    file_path: Path = typer.Argument(..., help="Path to PHP Controller file"),
    output: Optional[Path] = typer.Option(None, "--output", "-o", help="Output file path"),
):
    """Generate a PHPUnit test file skeleton with dependencies mocked."""
    if not file_path.exists():
        console.print(f"[red]Error:[/red] File does not exist: {file_path}")
        raise typer.Exit(1)
        
    from lca.tools.testgen import TestGenerator
    
    console.print(f"[cyan]Generating Test for:[/cyan] {file_path}")
    
    try:
        gen = TestGenerator(str(file_path))
        gen.parse()
        code = gen.generate()
        
        if output:
            with open(output, "w", encoding="utf-8") as f:
                f.write(code)
            console.print(f"[green]SUCCESS[/green] Test generated at: {output}")
        else:
            console.print(code)
            
    except Exception as e:
        console.print(f"[red]Failed:[/red] {e}")

@app.command()
def verify():
    """Run the Project Regression Test Suite (PHPUnit + LCA Audits)."""
    import subprocess
    import os
    
    # Locate run_tests.bat
    current_dir = os.path.dirname(os.path.abspath(__file__)) # .../lca/
    project_root = os.path.dirname(os.path.dirname(current_dir)) # .../etail_v3/
    script_path = os.path.join(project_root, "run_tests.bat")
    
    if not os.path.exists(script_path):
        console.print(f"[red]Error:[/red] Could not find '{script_path}'")
        raise typer.Exit(1)

    console.print(f"[bold cyan]Verifying Code Quality...[/bold cyan]")
    try:
        # Run the batch file
        result = subprocess.run([script_path], cwd=project_root, shell=True)
        if result.returncode != 0:
            console.print("[bold red]VERIFICATION FAILED![/bold red]")
            raise typer.Exit(1)
        else:
            console.print("[bold green]VERIFICATION PASSED![/bold green]")
    except Exception as e:
        console.print(f"[red]Execution Error:[/red] {e}")
        raise typer.Exit(1)


def main():
    app()


if __name__ == "__main__":
    main()
