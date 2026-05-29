"""Watch mode for live reindexing.

Monitors file changes and automatically updates indexes.
"""

import sys
import time
from pathlib import Path
from datetime import datetime
from typing import Optional, Callable

from rich.console import Console

try:
    from watchdog.observers import Observer
    from watchdog.events import FileSystemEventHandler, FileModifiedEvent, FileCreatedEvent, FileDeletedEvent
    WATCHDOG_AVAILABLE = True
except ImportError:
    WATCHDOG_AVAILABLE = False


console = Console()


class IndexUpdateHandler(FileSystemEventHandler):
    """Handler that triggers reindexing on file changes."""
    
    def __init__(
        self, 
        path: Path, 
        module_name: str, 
        output_path: Path,
        extensions: set[str],
        debounce_seconds: float = 2.0,
        on_index: Optional[Callable] = None
    ):
        self.path = path
        self.module_name = module_name
        self.output_path = output_path
        self.extensions = extensions
        self.debounce_seconds = debounce_seconds
        self.on_index = on_index
        self._last_update = 0.0
        self._pending_files: set[str] = set()
    
    def _should_process(self, path: str) -> bool:
        """Check if file should trigger reindexing."""
        p = Path(path)
        return p.suffix.lower() in self.extensions
    
    def _trigger_reindex(self) -> None:
        """Trigger reindexing with debounce."""
        now = time.time()
        if now - self._last_update < self.debounce_seconds:
            return
        
        self._last_update = now
        
        # Import here to avoid circular import
        from lca_core.lca.core import Indexer
        from lca_core.lca.storage import save_index
        
        console.print(f"[cyan]Reindexing:[/cyan] {len(self._pending_files)} file(s) changed")
        
        try:
            indexer = Indexer()
            if self.path.is_file():
                result = indexer.index_file(self.path, self.module_name)
            else:
                result = indexer.index_directory(self.path, self.module_name)
            
            save_index(result, self.output_path)
            
            console.print(
                f"[green]✓[/green] Updated index: "
                f"{len(result.functions)} functions, {len(result.calls)} calls"
            )
            
            if self.on_index:
                self.on_index(result)
            
            self._pending_files.clear()
            
        except Exception as e:
            console.print(f"[red]Error:[/red] {e}")
    
    def on_modified(self, event: FileModifiedEvent) -> None:
        if not event.is_directory and self._should_process(event.src_path):
            self._pending_files.add(event.src_path)
            self._trigger_reindex()
    
    def on_created(self, event: FileCreatedEvent) -> None:
        if not event.is_directory and self._should_process(event.src_path):
            self._pending_files.add(event.src_path)
            self._trigger_reindex()
    
    def on_deleted(self, event: FileDeletedEvent) -> None:
        if not event.is_directory and self._should_process(event.src_path):
            self._pending_files.add(event.src_path)
            self._trigger_reindex()


def watch_and_index(
    path: Path,
    module_name: str,
    output_path: Optional[Path] = None,
    extensions: Optional[set[str]] = None,
    debounce_seconds: float = 2.0,
) -> None:
    """
    Watch a directory and reindex on changes.
    
    Args:
        path: Path to watch (file or directory)
        module_name: Module name for the index
        output_path: Output JSON file path (default: {module}_index.json)
        extensions: File extensions to watch (default: .js, .jsx, .php, .py)
        debounce_seconds: Seconds to wait before reindexing after changes
    """
    if not WATCHDOG_AVAILABLE:
        console.print("[red]Error:[/red] watchdog not installed. Run: uv add watchdog")
        sys.exit(1)
    
    if output_path is None:
        output_path = Path(f"./{module_name}_index.json")
    
    if extensions is None:
        extensions = {".js", ".jsx", ".php", ".py", ".mjs"}
    
    # Initial index
    from lca_core.lca.core import Indexer
    from lca_core.lca.storage import save_index
    
    console.print(f"[cyan]Initial indexing:[/cyan] {path}")
    indexer = Indexer()
    
    if path.is_file():
        result = indexer.index_file(path, module_name)
        watch_path = path.parent
    else:
        result = indexer.index_directory(path, module_name)
        watch_path = path
    
    save_index(result, output_path)
    console.print(
        f"[green]✓[/green] Initial index: "
        f"{len(result.functions)} functions, {len(result.calls)} calls"
    )
    
    # Start watching
    event_handler = IndexUpdateHandler(
        path=path,
        module_name=module_name,
        output_path=output_path,
        extensions=extensions,
        debounce_seconds=debounce_seconds,
    )
    
    observer = Observer()
    observer.schedule(event_handler, str(watch_path), recursive=True)
    observer.start()
    
    console.print(f"[bold green]Watching:[/bold green] {watch_path}")
    console.print(f"[dim]Extensions: {', '.join(extensions)}[/dim]")
    console.print(f"[dim]Output: {output_path}[/dim]")
    console.print("[dim]Press Ctrl+C to stop[/dim]")
    
    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        console.print("\n[yellow]Stopping watcher...[/yellow]")
        observer.stop()
    
    observer.join()
    console.print("[green]✓[/green] Watch mode stopped")
