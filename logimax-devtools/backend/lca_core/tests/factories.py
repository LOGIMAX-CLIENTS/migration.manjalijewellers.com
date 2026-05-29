"""
Test Data Factories for Logimax Code Analyzer

Usage:
    from tests.factories import FunctionFactory, IndexFactory

    def test_example(function_factory):
        func = function_factory.create(name="saveEstimation")
"""

import pytest
from dataclasses import dataclass, field
from typing import List, Optional, Dict, Any
from pathlib import Path


@dataclass
class FunctionFactory:
    """Factory for creating test function definitions."""
    
    _counter: int = field(default=0, repr=False)
    
    def create(
        self,
        name: str = None,
        file: str = "test.php",
        line: int = 42,
        language: str = "php",
        calls: Optional[List[str]] = None,
    ) -> Dict[str, Any]:
        """Create a function definition."""
        self._counter += 1
        return {
            "name": name or f"testFunction{self._counter}",
            "file": file,
            "line": line,
            "language": language,
            "calls": calls or [],
        }
    
    def create_batch(self, count: int = 5, **kwargs) -> List[Dict[str, Any]]:
        """Create multiple function definitions."""
        return [self.create(**kwargs) for _ in range(count)]


@dataclass
class CallFactory:
    """Factory for creating call relationship data."""
    
    _counter: int = field(default=0, repr=False)
    
    def create(
        self,
        source: str = None,
        target: str = None,
        line: int = 10,
        file: str = "test.php",
    ) -> Dict[str, Any]:
        """Create a call relationship."""
        self._counter += 1
        return {
            "source": source or f"caller{self._counter}",
            "target": target or f"callee{self._counter}",
            "line": line,
            "file": file,
        }
    
    def create_chain(self, functions: List[str]) -> List[Dict[str, Any]]:
        """Create a chain of calls: A -> B -> C -> ..."""
        calls = []
        for i in range(len(functions) - 1):
            calls.append(self.create(
                source=functions[i],
                target=functions[i + 1],
                line=10 * (i + 1),
            ))
        return calls


@dataclass
class IndexFactory:
    """Factory for creating complete module index data."""
    
    def create(
        self,
        module: str = "test_module",
        functions: Optional[List[Dict]] = None,
        calls: Optional[List[Dict]] = None,
    ) -> Dict[str, Any]:
        """Create a complete index structure."""
        func_factory = FunctionFactory()
        call_factory = CallFactory()
        
        return {
            "module": module,
            "functions": functions or func_factory.create_batch(5),
            "calls": calls or [
                call_factory.create(source="main", target="helper"),
                call_factory.create(source="helper", target="util"),
            ],
            "indexed_at": "2026-01-31T12:00:00Z",
            "version": "1.0",
        }
    
    def create_with_hierarchy(self, depth: int = 3) -> Dict[str, Any]:
        """Create an index with a hierarchical call structure."""
        functions = []
        calls = []
        
        for level in range(depth):
            func_name = f"level{level}_func"
            functions.append({
                "name": func_name,
                "file": f"level{level}.php",
                "line": 10 * (level + 1),
                "language": "php",
            })
            
            if level > 0:
                calls.append({
                    "source": f"level{level - 1}_func",
                    "target": func_name,
                    "line": 15 * level,
                    "file": f"level{level - 1}.php",
                })
        
        return self.create(functions=functions, calls=calls)


@dataclass  
class ImpactResultFactory:
    """Factory for impact analysis results."""
    
    def create(
        self,
        term: str = "testFunction",
        risk_level: str = "Medium",
        callers: Optional[List[Dict]] = None,
        callees: Optional[List[Dict]] = None,
    ) -> Dict[str, Any]:
        """Create an impact analysis result."""
        return {
            "term": term,
            "risk_level": risk_level,
            "calls": [
                *[{"type": "called_by", **c} for c in (callers or [])],
                *[{"type": "calls", **c} for c in (callees or [])],
            ],
            "functions": [{
                "name": term,
                "file": "test.php",
                "line": 42,
            }],
        }
    
    def create_high_risk(self, term: str = "criticalFunction") -> Dict[str, Any]:
        """Create a high-risk impact result (many callers)."""
        return self.create(
            term=term,
            risk_level="High",
            callers=[{"target": f"caller{i}", "line": 10 * i} for i in range(15)],
            callees=[{"target": f"callee{i}", "line": 100 + 10 * i} for i in range(5)],
        )


# ============================================
# PYTEST FIXTURES
# ============================================

@pytest.fixture
def function_factory():
    """Provide a FunctionFactory instance."""
    return FunctionFactory()


@pytest.fixture
def call_factory():
    """Provide a CallFactory instance."""
    return CallFactory()


@pytest.fixture
def index_factory():
    """Provide an IndexFactory instance."""
    return IndexFactory()


@pytest.fixture
def impact_factory():
    """Provide an ImpactResultFactory instance."""
    return ImpactResultFactory()


@pytest.fixture
def sample_index(index_factory):
    """Provide a pre-built sample index."""
    return index_factory.create()


@pytest.fixture
def sample_functions(function_factory):
    """Provide a list of sample functions."""
    return [
        function_factory.create("main", "index.php", 1),
        function_factory.create("saveData", "controller.php", 50),
        function_factory.create("validate", "utils.php", 10),
    ]


@pytest.fixture
def sample_call_chain(call_factory):
    """Provide a sample call chain."""
    return call_factory.create_chain(["main", "controller", "model", "database"])


@pytest.fixture
def temp_index_file(tmp_path, sample_index):
    """Create a temporary index file for testing."""
    import json
    
    index_path = tmp_path / "test_index.json"
    with open(index_path, "w") as f:
        json.dump(sample_index, f)
    
    return index_path
