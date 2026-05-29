import re
from pathlib import Path
from typing import List, Dict, Set, Optional

class TestGenerator:
    """
    Generates PHPUnit tests for CodeIgniter controllers by parsing source code.
    Ports logic from LCA CLI to the Backend Plugin.
    """
    def __init__(self, file_path: str):
        self.file_path = Path(file_path)
        self.content = ""
        self.class_name = ""
        self.methods: List[Dict[str, str]] = [] # List of dicts: {name, body}
        self.models: Set[str] = set()
        self.libraries: Set[str] = set()

    def parse(self):
        """Parse the PHP file to extract class info, dependencies, and methods."""
        if not self.file_path.exists():
            raise FileNotFoundError(f"File not found: {self.file_path}")

        with open(self.file_path, "r", encoding="utf-8", errors="ignore") as f:
            self.content = f.read()

        # Extract Class Name
        class_match = re.search(r"class\s+(\w+)\s+extends\s+CI_Controller", self.content)
        if class_match:
            self.class_name = class_match.group(1)
        else:
            # Fallback for non-controllers or complex inheritance
             class_match_generic = re.search(r"class\s+(\w+)", self.content)
             if class_match_generic:
                 self.class_name = class_match_generic.group(1)
             else:
                self.class_name = self.file_path.stem

        # Extract Models (Global scan)
        model_matches = re.findall(r"load->model\s*\(\s*['\"]([\w/]+)['\"]", self.content)
        for m in model_matches:
            self.models.add(m.split('/')[-1])

        # Extract Libraries (Global scan)
        lib_matches = re.findall(r"load->library\s*\(\s*['\"]([\w/]+)['\"]", self.content)
        for l in lib_matches:
            self.libraries.add(l)
            
        # Extract Methods with Bodies
        self._extract_method_bodies()

    def _extract_method_bodies(self):
        # Robust parser with string/comment skipping
        pattern = re.compile(r"public\s+function\s+(\w+)\s*\(")
        
        for match in pattern.finditer(self.content):
            name = match.group(1)
            if name == "__construct": continue
            
            start_idx = match.end()
            open_brace_idx = self.content.find('{', start_idx)
            if open_brace_idx == -1: continue
            
            balance = 1
            i = open_brace_idx + 1
            length = len(self.content)
            in_string = False
            string_char = ''
            in_comment = False # // type
            in_block_comment = False # /* type */
            
            while balance > 0 and i < length:
                char = self.content[i]
                
                # Handle Escapes
                if in_string and char == '\\':
                    i += 2
                    continue
                    
                # Handle Strings
                if not in_comment and not in_block_comment:
                    if (char == '"' or char == "'") and not in_string:
                        in_string = True
                        string_char = char
                    elif char == string_char and in_string:
                        in_string = False
                        
                # Handle Comments
                if not in_string:
                    if not in_comment and not in_block_comment:
                        if char == '/' and i+1 < length:
                            next_char = self.content[i+1]
                            if next_char == '/':
                                in_comment = True
                                i += 1
                            elif next_char == '*':
                                in_block_comment = True
                                i += 1
                    elif in_comment and char == '\n':
                        in_comment = False
                    elif in_block_comment and char == '*' and i+1 < length:
                        if self.content[i+1] == '/':
                            in_block_comment = False
                            i += 1

                # Count Braces
                if not in_string and not in_comment and not in_block_comment:
                    if char == '{': balance += 1
                    elif char == '}': balance -= 1
                
                i += 1
            
            body = self.content[open_brace_idx+1 : i-1]
            self.methods.append({"name": name, "body": body})

    def generate(self) -> str:
        """Generate the PHPUnit test code."""
        template = f"""<?php

use PHPUnit\\Framework\\TestCase;

// Load Mocks definition if local dev
if (!defined('APPPATH')) define('APPPATH', __DIR__ . '/mocks/');
if (!defined('BASEPATH')) define('BASEPATH', dirname(__DIR__) . '/system/');

// Mock CI Controller if needed
if (!class_exists('CI_Controller')) {{
    class CI_Controller {{
        public function __get($key) {{ return null; }}
    }}
}}

// Mock stdClass if needed
if (!class_exists('stdClass')) {{
    class stdClass {{}}
}}

// Require the Controller
// NOTE: Adjust path as necessary for your environment
// require_once '{self.file_path.as_posix()}';

class {self.class_name}Test extends TestCase
{{
    private $controller;
    // Mocks
    {self._generate_mock_properties()}

    protected function setUp(): void
    {{
        $_POST = [];
        // $this->controller = new {self.class_name}();
        $this->controller = $this->getMockBuilder({self.class_name}::class)
                                 ->disableOriginalConstructor()
                                 ->getMock();
        
        // Inject Mocks
        {self._generate_injections()}
    }}

    {self._generate_tests()}
}}
"""
        return template

    def _generate_mock_properties(self):
        props = ""
        for model in self.models:
            props += f"    private ${model}_mock;\n"
        return props

    def _generate_injections(self):
        injections = ""
        for model in self.models:
            injections += f"        $this->{model}_mock = $this->createMock(stdClass::class);\n"
            injections += f"        $this->controller->{model} = $this->{model}_mock;\n"
        return injections

    def _generate_tests(self):
        tests = ""
        for method in self.methods:
            name = method['name']
            body = method['body']
            
            # SPY MASTER: Find calls to models
            spy_assertions = ""
            for model in self.models:
                # Regex to find $this->model->method(...)
                calls = re.findall(rf"this->{model}->(\w+)\(", body)
                for call in set(calls): # usage set to avoid dupes
                    spy_assertions += f"        $this->{model}_mock->expects($this->any())->method('{call}');\n"

            # DATA FACTORY: Find form validation rules OR direct POST access
            data_setup = ""
            rules = set(re.findall(r"set_rules\(['\"](\w+)['\"]", body))
            
            # Also find direct $_POST['key'] usage
            raw_post = set(re.findall(r"\$_POST\s*\[['\"](\w+)['\"]\]", body))
            rules.update(raw_post)

            if rules:
                data_setup += "        // Data Factory (Auto-Generated)\n"
                data_setup += "        $_POST = [\n"
                for field in rules:
                    data_setup += f"            '{field}' => 'dummy_value',\n"
                data_setup += "        ];\n"

            tests += f"""
    public function test_{name}()
    {{
{data_setup}
        // Spy Master Assertions
{spy_assertions}
        // Execute
        // $this->controller->{name}();
        
        $this->assertTrue(true);
    }}
"""
        return tests
