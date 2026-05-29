"""Cross-reference parser for JS-to-PHP API calls.

Extracts AJAX/fetch calls from JavaScript that call PHP controllers,
creating cross-language dependency links.
"""

import re
from pathlib import Path
from dataclasses import dataclass


@dataclass
class APICall:
    """Represents a JS→PHP API call."""
    source_function: str  # JS function making the call
    controller: str       # PHP controller (e.g., admin_ret_estimation)
    method: str           # PHP method (e.g., getOrderBySearch)
    line: int             # Line number in JS file
    file: str             # JS file path
    http_method: str      # GET, POST, etc.
    
    @property
    def target(self) -> str:
        """Full PHP target as controller::method."""
        return f"{self.controller}::{self.method}"


class CrossReferenceParser:
    """Parser for extracting JS→PHP API calls."""
    
    # Pattern to match CodeIgniter URLs: index.php/controller/method
    URL_PATTERN = re.compile(
        r"""
        (?:url\s*:\s*|fetch\s*\(|\.(?:get|post|ajax)\s*\()  # URL context
        .*?                                                    # Any prefix
        ['"](.*?)['"]                                          # URL string
        """,
        re.VERBOSE | re.IGNORECASE
    )
    
    # Pattern to extract controller/method from CI URL
    CI_URL_PATTERN = re.compile(
        r"""
        index\.php/                    # CI routing prefix
        ([a-zA-Z_][a-zA-Z0-9_]*)       # Controller name
        /
        ([a-zA-Z_][a-zA-Z0-9_]*)       # Method name
        """,
        re.VERBOSE
    )
    
    # Pattern to detect HTTP method
    HTTP_METHOD_PATTERN = re.compile(
        r"""
        (?:
            method\s*:\s*['"](GET|POST|PUT|DELETE|PATCH)['"]  |  # $.ajax method
            \.(?:get|post)\s*\(                                    # $.get/$.post
        )
        """,
        re.VERBOSE | re.IGNORECASE
    )
    
    # Pattern to find function context
    FUNCTION_PATTERN = re.compile(
        r"""
        (?:function\s+([a-zA-Z_][a-zA-Z0-9_]*)  |     # Named function
        (?:const|let|var)\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(?:async\s+)?function  |  # Variable function
        ([a-zA-Z_][a-zA-Z0-9_]*)\s*:\s*(?:async\s+)?function  |    # Object method
        (?:const|let|var)\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(?:async\s+)?\([^)]*\)\s*=>)  # Arrow function
        """,
        re.VERBOSE
    )
    
    def __init__(self):
        self._current_function = "GLOBAL"
        self._file_path = ""
        self._function_ranges: list[tuple[str, int, int]] = []
    
    def parse_file(self, file_path: Path) -> list[APICall]:
        """Parse a JavaScript file for API calls."""
        self._file_path = str(file_path)
        
        try:
            content = file_path.read_text(encoding="utf-8", errors="ignore")
        except Exception:
            return []
        
        lines = content.split("\n")
        
        # First pass: build function ranges
        self._build_function_ranges(lines)
        
        # Second pass: find API calls
        api_calls = []
        for line_num, line in enumerate(lines, 1):
            calls = self._extract_api_calls(line, line_num)
            api_calls.extend(calls)
        
        return api_calls
    
    def _build_function_ranges(self, lines: list[str]) -> None:
        """Build a mapping of line numbers to function contexts."""
        self._function_ranges = []
        brace_count = 0
        current_function = "GLOBAL"
        function_start = 1
        
        for line_num, line in enumerate(lines, 1):
            # Check for function definition
            match = self.FUNCTION_PATTERN.search(line)
            if match:
                # Get the first non-None group (function name)
                func_name = next((g for g in match.groups() if g), None)
                if func_name:
                    current_function = func_name
                    function_start = line_num
            
            # Track braces for scope
            brace_count += line.count("{") - line.count("}")
            
            # If we're back to global scope, record the function range
            if brace_count <= 0 and current_function != "GLOBAL":
                self._function_ranges.append((current_function, function_start, line_num))
                current_function = "GLOBAL"
                brace_count = 0
    
    def _get_function_at_line(self, line_num: int) -> str:
        """Get the function name that contains the given line."""
        for func_name, start, end in self._function_ranges:
            if start <= line_num <= end:
                return func_name
        return "GLOBAL"
    
    def _extract_api_calls(self, line: str, line_num: int) -> list[APICall]:
        """Extract API calls from a line."""
        calls = []
        
        # Find CI URLs in the line
        for match in self.CI_URL_PATTERN.finditer(line):
            controller = match.group(1)
            method = match.group(2)
            
            # Determine HTTP method
            http_method = "POST"  # Default for $.ajax
            http_match = self.HTTP_METHOD_PATTERN.search(line)
            if http_match:
                if http_match.group(1):
                    http_method = http_match.group(1).upper()
                elif ".get" in line.lower():
                    http_method = "GET"
            
            # Get function context
            source_function = self._get_function_at_line(line_num)
            
            calls.append(APICall(
                source_function=source_function,
                controller=controller,
                method=method,
                line=line_num,
                file=self._file_path,
                http_method=http_method
            ))
        
        return calls


def extract_api_calls(file_path: Path) -> list[APICall]:
    """Convenience function to extract API calls from a JS file."""
    parser = CrossReferenceParser()
    return parser.parse_file(file_path)


if __name__ == "__main__":
    # Quick test
    import sys
    if len(sys.argv) > 1:
        path = Path(sys.argv[1])
        calls = extract_api_calls(path)
        for call in calls:
            print(f"{call.source_function} -> {call.target} (line {call.line}, {call.http_method})")
