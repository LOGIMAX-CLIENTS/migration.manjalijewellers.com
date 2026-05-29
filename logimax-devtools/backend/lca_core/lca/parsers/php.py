"""PHP parser using tree-sitter."""

from pathlib import Path
import re
import tree_sitter_php as ts_php
from tree_sitter import Language, Parser, Node

from lca_core.lca.models import FunctionDef, FunctionCall
from .base import BaseParser


class PHPParser(BaseParser):
    """Parser for PHP files."""
    
    def __init__(self):
        # tree-sitter-php uses language_php function
        self._parser = Parser(Language(ts_php.language_php()))

    
    @property
    def language(self) -> str:
        return "php"
    
    @property
    def extensions(self) -> list[str]:
        return [".php", ".phtml", ".inc"]
    
    def parse_file(self, file_path: Path) -> tuple[list[FunctionDef], list[FunctionCall]]:
        """Parse a PHP file and extract functions and calls."""
        content = file_path.read_bytes()
        tree = self._parser.parse(content)
        
        functions: list[FunctionDef] = []
        calls: list[FunctionCall] = []
        
        self._current_function = "GLOBAL"
        self._current_class = None
        self._file_path = str(file_path)
        
        self._traverse(tree.root_node, functions, calls)
        
        return functions, calls
    
    def _traverse(self, node: Node, functions: list[FunctionDef], calls: list[FunctionCall]):
        """Recursively traverse the AST."""
        
        # Class declarations
        if node.type == "class_declaration":
            name_node = node.child_by_field_name("name")
            if name_node:
                self._current_class = name_node.text.decode("utf-8")
            for child in node.children:
                self._traverse(child, functions, calls)
            self._current_class = None
            return
        
        # Function declarations
        elif node.type == "function_definition":
            name_node = node.child_by_field_name("name")
            if name_node:
                func_name = name_node.text.decode("utf-8")
                if self._current_class:
                    func_name = f"{self._current_class}::{func_name}"
                
                func = FunctionDef(
                    name=func_name,
                    file=self._file_path,
                    line=node.start_point[0] + 1,
                    end_line=node.end_point[0] + 1,
                    params=self._extract_params(node),
                    language="php"
                )
                functions.append(func)
                
                old_context = self._current_function
                self._current_function = func_name
                for child in node.children:
                    self._traverse(child, functions, calls)
                self._current_function = old_context
                return
        
        # Method declarations (in classes)
        elif node.type == "method_declaration":
            name_node = node.child_by_field_name("name")
            if name_node:
                func_name = name_node.text.decode("utf-8")
                if self._current_class:
                    func_name = f"{self._current_class}::{func_name}"
                
                func = FunctionDef(
                    name=func_name,
                    file=self._file_path,
                    line=node.start_point[0] + 1,
                    end_line=node.end_point[0] + 1,
                    params=self._extract_params(node),
                    language="php"
                )
                functions.append(func)
                
                old_context = self._current_function
                self._current_function = func_name
                for child in node.children:
                    self._traverse(child, functions, calls)
                self._current_function = old_context
                return
        
        # Function calls: foo() or $obj->foo() or Class::method()
        elif node.type == "function_call_expression":
            call = self._extract_call(node)
            if call:
                calls.append(call)
        
        elif node.type == "member_call_expression":
            call = self._extract_member_call(node)
            if call:
                calls.append(call)
        
        elif node.type == "scoped_call_expression":
            call = self._extract_static_call(node)
            if call:
                calls.append(call)
        
        # Recurse into children
        for child in node.children:
            self._traverse(child, functions, calls)
    
    def _extract_params(self, node: Node) -> list[str]:
        """Extract parameter names from a function node."""
        params = []
        params_node = node.child_by_field_name("parameters")
        if params_node:
            for child in params_node.named_children:
                if child.type == "simple_parameter":
                    name = child.child_by_field_name("name")
                    if name:
                        params.append(name.text.decode("utf-8"))
        return params
    
    def _extract_call(self, node: Node) -> FunctionCall | None:
        """Extract simple function call."""
        func_node = node.child_by_field_name("function")
        if not func_node:
            return None
        
        if func_node.type == "name":
            target = func_node.text.decode("utf-8")
        else:
            return None
        
        # Skip built-ins
        skip_targets = {
            "echo", "print", "print_r", "var_dump", "die", "exit",
            "isset", "empty", "unset", "array", "list",
            "count", "strlen", "substr", "str_replace",
            "json_encode", "json_decode", "implode", "explode",
            "include", "include_once", "require", "require_once"
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
    
        return FunctionCall(
            source=self._current_function,
            target=target,
            line=node.start_point[0] + 1,
            file=self._file_path,
            call_type="calls"
        )
    
    def _extract_member_call(self, node: Node) -> FunctionCall | None:
        """Extract method call: $obj->method() or $this->db->get('table')"""
        name_node = node.child_by_field_name("name")  # method name (e.g. 'get')
        object_node = node.child_by_field_name("object") # e.g. '$this->db'

        if not name_node or not object_node:
            return None
        
        target_method = name_node.text.decode("utf-8")
        object_text = object_node.text.decode("utf-8")

        # 1. Detect DB Interactions (CodeIgniter Pattern)
        # $this->db->get('table'), $this->db->insert('table'), etc.
        if "->db" in object_text and target_method in ("get", "insert", "update", "delete", "replace"):
            # Extract table name from first argument
            args_node = node.child_by_field_name("arguments")
            if args_node and args_node.child_count > 0:
                # Find first valid string argument
                for arg in args_node.children:
                    if arg.type == "argument": # Wrapper in some tree-sitter versions
                        arg = arg.children[0]
                        
                    if arg.type == "string":
                        # Extract string content (remove quotes)
                        table_name = arg.text.decode("utf-8").strip("'\"")
                        return FunctionCall(
                            source=self._current_function,
                            target=f"TABLE:{table_name}",
                            line=node.start_point[0] + 1,
                            file=self._file_path,
                            call_type="db_query",
                            target_file=None # Virtual file
                        )

        # 1b. Handle db->query("SELECT ... FROM table")
        if "->db" in object_text and target_method == "query":
             args_node = node.child_by_field_name("arguments")
             if args_node and args_node.child_count > 0:
                for arg in args_node.children:
                    if arg.type == "argument": arg = arg.children[0]
                    if arg.type == "string" or arg.type == "encapsed_string": # Tree-sitter PHP string types
                        sql = arg.text.decode("utf-8").strip("'\"")
                        # Simple regex to find table refs
                        # Matches: FROM table, JOIN table, UPDATE table, INTO table
                        table_match = re.search(r"(?i)(?:FROM|JOIN|UPDATE|INTO)\s+[`]?([a-zA-Z0-9_]+)[`]?", sql)
                        if table_match:
                            table_name = table_match.group(1)
                            return FunctionCall(
                                source=self._current_function,
                                target=f"TABLE:{table_name}",
                                line=node.start_point[0] + 1,
                                file=self._file_path,
                                call_type="db_query",
                                target_file=None
                            )

        # 2. Standard method call
        # If object is $this, it's a local method call
        target = target_method
        if object_text == "$this":
             # We assume local call, maybe resolve it later
             pass
        else:
            # External object call?
            pass
        
        return FunctionCall(
            source=self._current_function,
            target=target,
            line=node.start_point[0] + 1,
            file=self._file_path,
            call_type="calls"
        )

    
    def _extract_static_call(self, node: Node) -> FunctionCall | None:
        """Extract static call: Class::method()"""
        scope_node = node.child_by_field_name("scope")
        name_node = node.child_by_field_name("name")
        
        if not name_node:
            return None
        
        if scope_node and scope_node.type == "name":
            class_name = scope_node.text.decode("utf-8")
            method_name = name_node.text.decode("utf-8")
            target = f"{class_name}::{method_name}"
        else:
            target = name_node.text.decode("utf-8")
        
        return FunctionCall(
            source=self._current_function,
            target=target,
            line=node.start_point[0] + 1,
            file=self._file_path,
            call_type="calls"
        )
