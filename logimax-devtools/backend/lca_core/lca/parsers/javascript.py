"""JavaScript/TypeScript parser using tree-sitter."""

from pathlib import Path
import re
import tree_sitter_javascript as ts_js
from tree_sitter import Language, Parser, Node

from lca_core.lca.models import FunctionDef, FunctionCall
from .base import BaseParser


class JavaScriptParser(BaseParser):
    """Parser for JavaScript and TypeScript files."""
    
    def __init__(self):
        self._parser = Parser(Language(ts_js.language()))
    
    @property
    def language(self) -> str:
        return "javascript"
    
    @property
    def extensions(self) -> list[str]:
        return [".js", ".jsx", ".ts", ".tsx", ".mjs"]
    
    def parse_file(self, file_path: Path) -> tuple[list[FunctionDef], list[FunctionCall]]:
        """Parse a JavaScript file and extract functions and calls."""
        content = file_path.read_bytes()
        tree = self._parser.parse(content)
        
        functions: list[FunctionDef] = []
        calls: list[FunctionCall] = []
        
        # Track current function context for associating calls
        self._current_function = "GLOBAL"
        self._file_path = str(file_path)
        
        self._traverse(tree.root_node, functions, calls)
        
        return functions, calls
    
    def _traverse(self, node: Node, functions: list[FunctionDef], calls: list[FunctionCall]):
        """Recursively traverse the AST."""
        
        # Function declarations: function foo() {}
        if node.type == "function_declaration":
            func = self._extract_function(node)
            if func:
                functions.append(func)
                old_context = self._current_function
                self._current_function = func.name
                for child in node.children:
                    self._traverse(child, functions, calls)
                self._current_function = old_context
                return
        
        # Function expressions OR Top-level Variables
        elif node.type == "variable_declarator":
            name_node = node.child_by_field_name("name")
            value_node = node.child_by_field_name("value")
            
            if not name_node: return

            # 1. Function Expression (const foo = function() {})
            if value_node and value_node.type in ("function", "arrow_function"):
                func = FunctionDef(
                    name=name_node.text.decode("utf-8"),
                    file=self._file_path,
                    line=node.start_point[0] + 1,
                    end_line=node.end_point[0] + 1,
                    params=self._extract_params(value_node),
                    language="javascript"
                )
                functions.append(func)
                old_context = self._current_function
                self._current_function = func.name
                for child in node.children:
                    self._traverse(child, functions, calls)
                self._current_function = old_context
                return

            # 2. Top-Level Variable (const LOT_DETAILS = [])
            # Only index if we are at GLOBAL scope and it's an Identifier
            if self._current_function == "GLOBAL" and name_node.type == "identifier":
                # Filter out obvious noise (common short vars)
                var_name = name_node.text.decode("utf-8")
                if len(var_name) < 3: return
                
                # Check value type if present (prioritize Arrays/Objects)
                # value_type = value_node.type if value_node else "undefined"
                
                functions.append(FunctionDef(
                    name=f"{var_name} (Var)", # Mark as Variable
                    file=self._file_path,
                    line=node.start_point[0] + 1,
                    end_line=node.end_point[0] + 1,
                    params=[], # Variables have no params
                    language="javascript"
                ))
                # Do NOT recurse into variable value for function definitions 
                # (unless it contains nested functions, which traverse handles via children)
                for child in node.children:
                    self._traverse(child, functions, calls)
                return
        
        # Method definitions in objects/classes
        elif node.type == "method_definition":
            name_node = node.child_by_field_name("name")
            if name_node:
                func = FunctionDef(
                    name=name_node.text.decode("utf-8"),
                    file=self._file_path,
                    line=node.start_point[0] + 1,
                    end_line=node.end_point[0] + 1,
                    params=[],
                    language="javascript"
                )
                functions.append(func)
                old_context = self._current_function
                self._current_function = func.name
                for child in node.children:
                    self._traverse(child, functions, calls)
                self._current_function = old_context
                return
        
        # Function calls: foo() or obj.foo()
        elif node.type == "call_expression":
            call = self._extract_call(node)
            if call:
                calls.append(call)
        
        # API String Detection strings: "index.php/admin/..."
        elif node.type == "string":
            # Extract string content (remove quotes)
            text = node.text.decode("utf-8").strip("'\"`")
            # Match CodeIgniter URLs
            api_match = re.search(r"index\.php\/([a-zA-Z0-9_]+)\/([a-zA-Z0-9_]+)(?:\/([a-zA-Z0-9_]+))?", text)
            if api_match:
                # e.g. admin_ret_estimation/estimation/save
                # We normalize to Controller/Method
                controller = api_match.group(1)
                # If group 3 exists, it's Folder/Controller/Method. If not, it's Controller/Method (Group 2)
                method = api_match.group(3) if api_match.group(3) else api_match.group(2)
                
                # Create a link from current function -> API Endpoint
                calls.append(FunctionCall(
                    source=self._current_function,
                    target=f"API:{controller}/{method}",
                    line=node.start_point[0] + 1,
                    file=self._file_path,
                    call_type="api_call",
                    target_file=None
                ))

        # Recurse into children
        for child in node.children:
            self._traverse(child, functions, calls)
    
    def _extract_function(self, node: Node) -> FunctionDef | None:
        """Extract function definition from a function_declaration node."""
        name_node = node.child_by_field_name("name")
        if not name_node:
            return None
        
        return FunctionDef(
            name=name_node.text.decode("utf-8"),
            file=self._file_path,
            line=node.start_point[0] + 1,
            end_line=node.end_point[0] + 1,
            params=self._extract_params(node),
            language="javascript"
        )
    
    def _extract_params(self, node: Node) -> list[str]:
        """Extract parameter names from a function node."""
        params = []
        params_node = node.child_by_field_name("parameters")
        if params_node:
            for child in params_node.children:
                if child.type == "identifier":
                    params.append(child.text.decode("utf-8"))
                elif child.type == "formal_parameters":
                    for param in child.children:
                        if param.type == "identifier":
                            params.append(param.text.decode("utf-8"))
        return params
    
    def _extract_call(self, node: Node) -> FunctionCall | None:
        """Extract function call from a call_expression node."""
        func_node = node.child_by_field_name("function")
        if not func_node:
            return None
        
        # Simple call: foo()
        if func_node.type == "identifier":
            target = func_node.text.decode("utf-8")
        # Member call: obj.foo() - extract just the method name
        elif func_node.type == "member_expression":
            prop = func_node.child_by_field_name("property")
            if prop:
                target = prop.text.decode("utf-8")
            else:
                return None
        else:
            return None
        
        # Skip common built-ins, jQuery/DOM methods, and utilities
        # These create noise in call graphs without adding semantic value
        skip_targets = {
            # Console/logging
            "console", "log", "error", "warn", "info", "debug", "trace",
            
            # Timers
            "setTimeout", "setInterval", "clearTimeout", "clearInterval",
            "requestAnimationFrame", "cancelAnimationFrame",
            
            # Type conversion
            "parseInt", "parseFloat", "Number", "String", "Boolean",
            "toString", "valueOf", "toFixed", "toPrecision",
            
            # Built-in objects
            "Array", "Object", "JSON", "Math", "Date", "Promise", "Map", "Set",
            "RegExp", "Error", "Symbol", "Proxy", "Reflect",
            
            # Module system
            "require", "import", "export", "define", "module",
            
            # jQuery core
            "$", "jQuery",
            
            # jQuery DOM traversal (high noise)
            "find", "children", "parent", "parents", "closest", "siblings",
            "next", "prev", "first", "last", "eq", "filter", "not", "has",
            "add", "end", "contents", "nextAll", "prevAll", "parentsUntil",
            
            # jQuery DOM manipulation (high noise)
            "append", "prepend", "after", "before", "remove", "empty",
            "clone", "wrap", "unwrap", "replaceWith", "detach", "insertAfter",
            "insertBefore", "appendTo", "prependTo", "html", "text",
            
            # jQuery attributes/properties (high noise)
            "attr", "removeAttr", "prop", "removeProp", "val", "data",
            "addClass", "removeClass", "toggleClass", "hasClass", "css",
            
            # jQuery events (high noise)
            "on", "off", "one", "trigger", "triggerHandler", "bind", "unbind",
            "click", "dblclick", "hover", "focus", "blur", "change", "submit",
            "keydown", "keyup", "keypress", "mouseenter", "mouseleave",
            "scroll", "resize", "load", "ready",
            
            # jQuery effects
            "show", "hide", "toggle", "fadeIn", "fadeOut", "fadeToggle",
            "slideUp", "slideDown", "slideToggle", "animate", "stop", "delay",
            
            # jQuery dimensions
            "width", "height", "innerWidth", "innerHeight", "outerWidth", "outerHeight",
            "offset", "position", "scrollTop", "scrollLeft",
            
            # jQuery AJAX (keep high-level, skip low-level)
            "param", "serialize", "serializeArray",
            
            # jQuery utilities
            "each", "map", "grep", "extend", "merge", "inArray", "isArray",
            "isFunction", "isNumeric", "isPlainObject", "trim", "proxy",
            "makeArray", "type", "now", "parseJSON", "parseHTML",
            
            # Native DOM methods (high noise)
            "getElementById", "getElementsByClassName", "getElementsByTagName",
            "querySelector", "querySelectorAll", "createElement", "createTextNode",
            "appendChild", "removeChild", "insertBefore", "replaceChild",
            "getAttribute", "setAttribute", "removeAttribute", "hasAttribute",
            "addEventListener", "removeEventListener", "dispatchEvent",
            "classList", "style", "innerHTML", "innerText", "textContent",
            
            # Array methods (utility noise)
            "push", "pop", "shift", "unshift", "slice", "splice", "concat",
            "join", "reverse", "sort", "indexOf", "lastIndexOf", "includes",
            "forEach", "some", "every", "reduce", "reduceRight",
            
            # String methods (utility noise)
            "charAt", "charCodeAt", "concat", "indexOf", "lastIndexOf",
            "match", "replace", "search", "split", "substring", "substr",
            "toLowerCase", "toUpperCase", "trim", "startsWith", "endsWith",
            
            # Object methods
            "keys", "values", "entries", "assign", "freeze", "seal",
            "hasOwnProperty", "isPrototypeOf", "propertyIsEnumerable",
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
