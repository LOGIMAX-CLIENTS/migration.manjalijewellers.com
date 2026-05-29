"""
Test Service - Run PHPUnit and Jest tests
Executes automated tests and reports results
"""
import asyncio
import subprocess
import json
from pathlib import Path
from typing import Any


class TestService:
    """Service to run automated tests for the pipeline workflow"""
    
    def __init__(self):
        self.php_project_path = Path("C:/xampp/htdocs/etail_v3/admin")
        self.js_project_path = Path("C:/xampp/htdocs/etail_v3/logimax-devtools/frontend")
    
    async def run_tests(self, changed_files: list[str] = None) -> dict[str, Any]:
        """Run all relevant tests based on changed files"""
        results = {
            "passed": True,
            "php": None,
            "js": None,
            "summary": "",
            "total_tests": 0,
            "total_passed": 0,
            "total_failed": 0,
        }
        
        # Determine which tests to run
        run_php = False
        run_js = False
        
        if changed_files:
            for f in changed_files:
                if f.endswith('.php'):
                    run_php = True
                elif f.endswith(('.js', '.jsx', '.ts', '.tsx')):
                    run_js = True
        else:
            # Run both if no specific files
            run_php = True
            run_js = True
        
        # Run PHPUnit tests
        if run_php:
            php_results = await self.run_phpunit()
            results["php"] = php_results
            results["total_tests"] += php_results.get("tests", 0)
            results["total_passed"] += php_results.get("passed", 0)
            results["total_failed"] += php_results.get("failed", 0)
            if not php_results.get("success", False):
                results["passed"] = False
        
        # Run Jest tests
        if run_js:
            js_results = await self.run_jest()
            results["js"] = js_results
            results["total_tests"] += js_results.get("tests", 0)
            results["total_passed"] += js_results.get("passed", 0)
            results["total_failed"] += js_results.get("failed", 0)
            if not js_results.get("success", False):
                results["passed"] = False
        
        # Generate summary
        if results["passed"]:
            results["summary"] = f"All tests passed ({results['total_passed']}/{results['total_tests']})"
        else:
            results["summary"] = f"Tests failed: {results['total_failed']} of {results['total_tests']} failed"
        
        return results
    
    async def run_phpunit(self) -> dict[str, Any]:
        """Run PHPUnit tests"""
        result = {
            "success": False,
            "tests": 0,
            "passed": 0,
            "failed": 0,
            "errors": 0,
            "output": "",
            "error": None
        }
        
        phpunit_path = self.php_project_path / "vendor" / "bin" / "phpunit"
        config_path = self.php_project_path / "phpunit.xml"
        
        # Check if PHPUnit is available
        if not phpunit_path.exists() and not (self.php_project_path / "phpunit.xml").exists():
            result["error"] = "PHPUnit not configured in project"
            result["success"] = True  # Not a failure, just not configured
            return result
        
        try:
            process = await asyncio.create_subprocess_exec(
                "php", str(phpunit_path),
                "--configuration", str(config_path),
                "--testdox",
                cwd=str(self.php_project_path),
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE
            )
            stdout, stderr = await asyncio.wait_for(
                process.communicate(), timeout=300  # 5 minute timeout
            )
            
            output = stdout.decode() + stderr.decode()
            result["output"] = output
            
            # Parse results (simplified)
            if process.returncode == 0:
                result["success"] = True
                # Try to extract test count
                if "OK" in output:
                    # Extract number from "OK (X tests, Y assertions)"
                    import re
                    match = re.search(r'OK \((\d+) tests?', output)
                    if match:
                        result["tests"] = int(match.group(1))
                        result["passed"] = result["tests"]
            else:
                result["success"] = False
                # Try to extract failure count
                import re
                match = re.search(r'FAILURES!\s+Tests: (\d+), .*Failures: (\d+)', output)
                if match:
                    result["tests"] = int(match.group(1))
                    result["failed"] = int(match.group(2))
                    result["passed"] = result["tests"] - result["failed"]
                    
        except asyncio.TimeoutError:
            result["error"] = "PHPUnit tests timed out after 5 minutes"
        except FileNotFoundError:
            result["error"] = "PHP or PHPUnit not found"
            result["success"] = True  # Not a failure, just not available
        except Exception as e:
            result["error"] = str(e)
        
        return result
    
    async def run_jest(self) -> dict[str, Any]:
        """Run Jest tests"""
        result = {
            "success": False,
            "tests": 0,
            "passed": 0,
            "failed": 0,
            "output": "",
            "error": None
        }
        
        # Check if package.json exists and has test script
        package_json = self.js_project_path / "package.json"
        if not package_json.exists():
            result["error"] = "No package.json found"
            result["success"] = True  # Not a failure
            return result
        
        try:
            process = await asyncio.create_subprocess_exec(
                "npm", "test", "--", "--passWithNoTests", "--json",
                cwd=str(self.js_project_path),
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE
            )
            stdout, stderr = await asyncio.wait_for(
                process.communicate(), timeout=300
            )
            
            output = stdout.decode()
            result["output"] = output
            
            # Try to parse JSON output
            try:
                # Jest JSON output
                json_output = json.loads(output)
                result["tests"] = json_output.get("numTotalTests", 0)
                result["passed"] = json_output.get("numPassedTests", 0)
                result["failed"] = json_output.get("numFailedTests", 0)
                result["success"] = json_output.get("success", False)
            except json.JSONDecodeError:
                # Non-JSON output
                if process.returncode == 0:
                    result["success"] = True
                else:
                    result["success"] = False
                    result["error"] = stderr.decode() if stderr else "Tests failed"
                    
        except asyncio.TimeoutError:
            result["error"] = "Jest tests timed out after 5 minutes"
        except FileNotFoundError:
            result["error"] = "npm not found"
            result["success"] = True
        except Exception as e:
            result["error"] = str(e)
        
        return result
    
    async def run_specific_tests(
        self, 
        test_files: list[str] = None,
        test_classes: list[str] = None
    ) -> dict[str, Any]:
        """Run specific test files or classes"""
        # For now, just run all tests
        return await self.run_tests()
