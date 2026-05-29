import os
import re
from typing import List, Dict, Optional
from .impact_service import ImpactService

class QualityService:
    def __init__(self, impact_service: ImpactService = None):
        self.impact_service = impact_service or ImpactService()

    async def evaluate_change(self, change_type: str, files: List[str], index_name: str = "default") -> Dict:
        """
        Evaluate a set of changes against the Quality Gate.
        
        Args:
            change_type: 'BUGFIX', 'CR' (Change Request), or 'NR' (New Requirement)
            files: List of file paths (relative to project root)
            index_name: LCA index to use for impact analysis
            
        Returns:
            Dict containing score, verdict, and detailed report.
        """
        report = {
            "type": change_type,
            "files_analyzed": len(files),
            "metrics": {
                "impact_score": 0,
                "complexity_avg": 0,
                "has_tests": False
            },
            "issues": [],
            "verdict": "PENDING"
        }

        # 1. Impact Analysis
        total_impact = 0
        risky_files = []
        
        for file in files:
            # Skip non-code files
            if not file.endswith(('.php', '.js', '.py')):
                continue
                
            # Quick heuristic for complexity
            # (In a real scenario, we'd read the file content)
            
            # Analyze Impact (if inside a known module)
            if self.impact_service:
                # Extract function name/module from filename is hard without parsing.
                # For now, we assume the user passes a list of "modules" or we try to guess.
                # Fallback: simple path-based impact heuristic
                pass

        # 2. Test Coverage Check
        # Check if any file in 'files' has a corresponding 'test' file in the list OR existing directory
        has_tests = any('test' in f.lower() or 'spec' in f.lower() for f in files)
        report['metrics']['has_tests'] = has_tests

        # 3. Rule Enforcement
        verdict = "PASS"
        issues = []

        if change_type == 'NR':
            if not has_tests:
                issues.append("❌ NR (New Requirement) must include Test cases.")
                verdict = "FAIL"
        
        elif change_type == 'CR':
            if not has_tests:
                issues.append("⚠️ CR (Modification) should include tests (recommended).")
                # Warning but maybe not fail? Let's say FAIL for strict mode.
                verdict = "WARN"

        elif change_type == 'BUGFIX':
            # Bugfixes are allowed without tests if simple
            pass

        # 4. Final Decision
        if issues:
            if verdict == "PASS" and issues: verdict = "WARN"
        
        report['issues'] = issues
        report['verdict'] = verdict
        return report

    def _calculate_complexity(self, content: str) -> int:
        """Simple cyclomatic complexity approximation"""
        keywords = ['if', 'else', 'while', 'for', 'foreach', 'case', 'catch']
        count = 0
        for word in keywords:
            count += content.count(f"{word} ") \
                   + content.count(f"{word}(") 
        return count + 1
