"""Python parser using tree-sitter.

Parses Python files to extract function definitions, class methods,
and function calls for dependency analysis.
"""

from __future__ import annotations

from pathlib import Path
from typing import TYPE_CHECKING, Any

try:
    import tree_sitter_python as ts_python
    from tree_sitter import Language, Parser, Node
    PYTHON_AVAILABLE = True
except ImportError:
    PYTHON_AVAILABLE = False
    Node = Any  # type: ignore

from lca_core.lca.models import FunctionDef, FunctionCall
from .base import BaseParser


class PythonParser(BaseParser):
    """Parser for Python files."""
    
    def __init__(self):
        if not PYTHON_AVAILABLE:
            raise RuntimeError("tree-sitter-python not installed. Run: uv add tree-sitter-python")
        # tree-sitter-python uses language function
        self._parser = Parser(Language(ts_python.language()))
    
    @property
    def language(self) -> str:
        return "python"
    
    @property
    def extensions(self) -> list[str]:
        return [".py", ".pyw"]
    
    def parse_file(self, file_path: Path) -> tuple[list[FunctionDef], list[FunctionCall]]:
        """Parse a Python file for functions and calls."""
        self._file_path = str(file_path)
        self._current_function = "GLOBAL"
        self._current_class = None
        
        try:
            content = file_path.read_bytes()
        except Exception:
            return [], []
        
        tree = self._parser.parse(content)
        
        functions: list[FunctionDef] = []
        calls: list[FunctionCall] = []
        
        self._walk_tree(tree.root_node, functions, calls)
        
        return functions, calls
    
    def _walk_tree(
        self, 
        node: Node, 
        functions: list[FunctionDef], 
        calls: list[FunctionCall]
    ) -> None:
        """Recursively walk the AST."""
        # Track class context
        if node.type == "class_definition":
            name_node = node.child_by_field_name("name")
            if name_node:
                old_class = self._current_class
                self._current_class = name_node.text.decode("utf-8")
                
                # Extract class as a definition
                functions.append(FunctionDef(
                    name=self._current_class,
                    file=self._file_path,
                    line=node.start_point[0] + 1,
                    end_line=node.end_point[0] + 1,
                    params=[],
                    language=self.language
                ))
                
                # Process children
                for child in node.children:
                    self._walk_tree(child, functions, calls)
                
                self._current_class = old_class
                return
        
        # Handle function definitions
        if node.type == "function_definition":
            func_def = self._extract_function_def(node)
            if func_def:
                functions.append(func_def)
                
                # Save context and process body
                old_func = self._current_function
                self._current_function = func_def.name
                
                body = node.child_by_field_name("body")
                if body:
                    for child in body.children:
                        self._walk_tree(child, functions, calls)
                
                self._current_function = old_func
                return
        
        # Handle function calls
        if node.type == "call":
            call = self._extract_call(node)
            if call:
                calls.append(call)
        
        # Continue walking
        for child in node.children:
            self._walk_tree(child, functions, calls)
    
    def _extract_function_def(self, node: Node) -> FunctionDef | None:
        """Extract function definition from a function_definition node."""
        name_node = node.child_by_field_name("name")
        if not name_node:
            return None
        
        name = name_node.text.decode("utf-8")
        
        # Include class prefix for methods
        if self._current_class and name != "__init__":
            full_name = f"{self._current_class}.{name}"
        else:
            full_name = name
        
        # Extract parameters
        params = self._extract_params(node)
        
        return FunctionDef(
            name=full_name,
            file=self._file_path,
            line=node.start_point[0] + 1,
            end_line=node.end_point[0] + 1,
            params=params,
            language=self.language
        )
    
    def _extract_params(self, node: Node) -> list[str]:
        """Extract parameter names from function definition."""
        params = []
        params_node = node.child_by_field_name("parameters")
        if params_node:
            for child in params_node.children:
                if child.type == "identifier":
                    param_name = child.text.decode("utf-8")
                    if param_name != "self" and param_name != "cls":
                        params.append(param_name)
                elif child.type in ("default_parameter", "typed_parameter", "typed_default_parameter"):
                    name_child = child.child_by_field_name("name")
                    if name_child:
                        param_name = name_child.text.decode("utf-8")
                        if param_name != "self" and param_name != "cls":
                            params.append(param_name)
        return params
    
    def _extract_call(self, node: Node) -> FunctionCall | None:
        """Extract function call from a call node."""
        func_node = node.child_by_field_name("function")
        if not func_node:
            return None
        
        # Simple call: foo()
        if func_node.type == "identifier":
            target = func_node.text.decode("utf-8")
        # Attribute call: obj.foo()
        elif func_node.type == "attribute":
            attr = func_node.child_by_field_name("attribute")
            if attr:
                target = attr.text.decode("utf-8")
            else:
                return None
        else:
            return None
        
        # Skip common built-ins and utilities
        skip_targets = {
            # Built-in functions
            "print", "len", "range", "str", "int", "float", "bool", "list", "dict",
            "tuple", "set", "type", "isinstance", "issubclass", "hasattr", "getattr",
            "setattr", "delattr", "callable", "iter", "next", "open", "input", "abs",
            "min", "max", "sum", "round", "sorted", "reversed", "enumerate", "zip",
            "map", "filter", "any", "all", "super", "classmethod", "staticmethod",
            "property", "repr", "hash", "id", "dir", "vars", "format", "chr", "ord",
            
            # Common decorators
            "lru_cache", "cached_property", "dataclass", "field",
            
            # Typing
            "Optional", "List", "Dict", "Set", "Tuple", "Union", "Any", "Callable",
            "TypeVar", "Generic", "cast", "overload",
            
            # Logging
            "debug", "info", "warning", "error", "critical", "exception",
            
            # Testing
            "pytest", "fixture", "parametrize", "mark", "skip", "skipif",
            
            # Common methods (high noise)
            "append", "extend", "insert", "remove", "pop", "clear", "copy",
            "keys", "values", "items", "get", "update", "setdefault",
            "join", "split", "strip", "replace", "format", "encode", "decode",
            "startswith", "endswith", "lower", "upper", "title", "capitalize",
            "find", "index", "count", "sort", "reverse",
            
            # Async
            "await", "async", "asyncio",
        }
        
        if target in skip_targets:
            return None
        
        return FunctionCall(
            source=self._current_function,
            target=target,
            line=node.start_point[0] + 1,
            file=self._file_path,
            call_type="calls"
        )
