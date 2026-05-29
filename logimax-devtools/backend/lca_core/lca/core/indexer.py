"""Core indexing orchestrator."""

from pathlib import Path
from datetime import datetime

from lca_core.lca.models import ModuleIndex, FunctionDef, FunctionCall, Level2Deps
from lca_core.lca.parsers import JavaScriptParser, PHPParser, BaseParser

# Try to import PythonParser if available
try:
    from lca_core.lca.parsers.python import PythonParser
    PYTHON_PARSER_AVAILABLE = True
except ImportError:
    PYTHON_PARSER_AVAILABLE = False


class Indexer:
    """Main indexing orchestrator that combines parsers and builds indexes."""
    
    def __init__(self):
        self.parsers: list[BaseParser] = [
            JavaScriptParser(),
            PHPParser(),
        ]
        # Add Python parser if tree-sitter-python is installed
        if PYTHON_PARSER_AVAILABLE:
            try:
                self.parsers.append(PythonParser())
            except RuntimeError:
                pass  # tree-sitter-python not installed

    
    def index_directory(self, path: Path, module_name: str, recursive: bool = True) -> ModuleIndex:
        """
        Index all supported files in a directory.
        
        Args:
            path: Directory path to index
            module_name: Name for this module index
            recursive: Whether to search subdirectories
            
        Returns:
            ModuleIndex containing all discovered functions and calls
        """
        all_functions: list[FunctionDef] = []
        all_calls: list[FunctionCall] = []
        
        # Collect all files
        if recursive:
            # Manual traversal to filter dirs
            files = []
            for root, dirs, filenames in path.walk(): # Using walk for better control
                # Filter directories
                dirs[:] = [d for d in dirs if d not in {'.git', 'node_modules', 'vendor', '__pycache__', 'logs', 'cache', 'libraries', 'third_party'}]
                
                for name in filenames:
                    files.append(root / name)
        else:
            files = list(path.glob("*"))
        
        # Parse each file with appropriate parser
        for file_path in files:
            if not file_path.is_file():
                continue
            
            for parser in self.parsers:
                if parser.can_parse(file_path):
                    try:
                        functions, calls = parser.parse_file(file_path)
                        all_functions.extend(functions)
                        all_calls.extend(calls)
                    except Exception as e:
                        print(f"Error parsing {file_path}: {e}")
                    break
        
        # Resolve cross-file calls (Linker Pass)
        self._resolve_cross_file_calls(all_functions, all_calls)

        # Build the index
        index = ModuleIndex(
            module=module_name,
            version="2.1.0",
            indexed_at=datetime.now().isoformat(),
            functions=all_functions,
            calls=all_calls,
            files=[{"path": str(p)} for p in files],
        )
        
        # Compute level2 dependencies
        index.level2 = self._compute_level2(all_calls)
        
        return index

    def _resolve_cross_file_calls(self, functions: list[FunctionDef], calls: list[FunctionCall]):
        """
        Link Phase: Resolve target_file for each call by looking up the global symbol table.
        """
        # 1. Build Global Symbol Table (Last definition wins for now)
        symbol_table: dict[str, str] = {}
        for func in functions:
            symbol_table[func.name] = func.file
            
        # 2. Link Calls
        for call in calls:
            if call.target in symbol_table:
                call.target_file = symbol_table[call.target]

    
    def index_file(self, path: Path, module_name: str) -> ModuleIndex:
        """Index a single file."""
        functions: list[FunctionDef] = []
        calls: list[FunctionCall] = []
        
        for parser in self.parsers:
            if parser.can_parse(path):
                functions, calls = parser.parse_file(path)
                break
        
        index = ModuleIndex(
            module=module_name,
            version="2.0.0",
            indexed_at=datetime.now().isoformat(),
            functions=functions,
            calls=calls,
        )
        
        index.level2 = self._compute_level2(calls)
        
        return index
    
    def _compute_level2(self, calls: list[FunctionCall]) -> dict[str, Level2Deps]:
        """Compute level 2 dependencies for all functions."""
        # Build adjacency lists
        calls_map: dict[str, set[str]] = {}  # func -> set of functions it calls
        called_by_map: dict[str, set[str]] = {}  # func -> set of functions that call it
        
        for call in calls:
            if call.source not in calls_map:
                calls_map[call.source] = set()
            calls_map[call.source].add(call.target)
            
            if call.target not in called_by_map:
                called_by_map[call.target] = set()
            called_by_map[call.target].add(call.source)
        
        # Build level2 for each function that appears in calls
        level2: dict[str, Level2Deps] = {}
        all_funcs = set(calls_map.keys()) | set(called_by_map.keys())
        
        for func in all_funcs:
            l2_calls = list(calls_map.get(func, set()))
            l2_called_by = list(called_by_map.get(func, set()))
            
            if l2_calls or l2_called_by:
                level2[func] = Level2Deps(
                    calls=l2_calls,
                    called_by=l2_called_by
                )
        
        return level2
                