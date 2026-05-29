"""
LCA Services - Index Service
Orchestrates codebase indexing
"""
import asyncio
from pathlib import Path
from typing import AsyncIterator, Callable

from lca_core.lca.core import (
    CodeIndex, FunctionDef, IndexProgress
)
from lca_core.lca.storage import SQLiteStorage
from lca_core.lca.parsers import AVAILABLE_PARSERS, get_parser_for_file


class IndexService:
    """Service for indexing codebases"""
    
    def __init__(self, storage: SQLiteStorage | None = None):
        self.storage = storage or SQLiteStorage()
        self.parsers = AVAILABLE_PARSERS
    
    async def index_directory(
        self,
        path: str,
        name: str,
        exclude_patterns: list[str] | None = None,
        on_progress: Callable[[IndexProgress], None] | None = None,
    ) -> CodeIndex:
        """Index a directory and save to storage"""
        root = Path(path)
        if not root.exists():
            raise ValueError(f"Path does not exist: {path}")
        
        # Default exclusions
        excludes = exclude_patterns or []
        default_excludes = [
            'node_modules', 'vendor', '.git', '__pycache__', 
            'venv', '.venv', 'dist', 'build', '.idea', '.vscode'
        ]
        all_excludes = set(excludes + default_excludes)
        
        # Collect files to parse
        files_to_parse = []
        for parser in self.parsers:
            for ext in parser.extensions:
                for file_path in root.rglob(f'*{ext}'):
                    # Check exclusions
                    if any(ex in str(file_path) for ex in all_excludes):
                        continue
                    files_to_parse.append((file_path, parser))
        
        # Create progress tracker
        progress = IndexProgress(total_files=len(files_to_parse))
        
        # Create index
        index = CodeIndex(name=name, path=str(root))
        
        # Parse files
        for file_path, parser in files_to_parse:
            progress.current_file = str(file_path)
            
            try:
                functions = parser.parse_file(file_path)
                for fn in functions:
                    index.functions[fn.qualified_name] = fn
                    if fn.calls:
                        index.calls[fn.qualified_name] = fn.calls
            except Exception as e:
                progress.errors.append(f"{file_path}: {e}")
            
            progress.processed_files += 1
            
            if on_progress:
                on_progress(progress)
            
            # Yield control to event loop periodically
            if progress.processed_files % 50 == 0:
                await asyncio.sleep(0)
        
        # Save to storage
        await self.storage.save_index(index)
        
        return index
    
    async def get_index_status(self, name: str) -> dict:
        """Get status of an index"""
        index = await self.storage.load_index(name)
        if not index:
            return {"exists": False}
        
        stats = await self.storage.get_stats(name)
        return {
            "exists": True,
            "name": index.name,
            "path": index.path,
            **stats,
        }
    
    async def list_indexes(self) -> list[str]:
        """List all available indexes"""
        return await self.storage.list_indexes()
    
    async def delete_index(self, name: str) -> bool:
        """Delete an index"""
        return await self.storage.delete_index(name)
