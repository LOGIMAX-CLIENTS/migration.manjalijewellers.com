"""
Test the factory fixtures to verify they work correctly.
"""

import pytest
from tests.factories import (
    FunctionFactory,
    CallFactory,
    IndexFactory,
    ImpactResultFactory,
)


class TestFunctionFactory:
    """Tests for FunctionFactory."""

    def test_create_default(self, function_factory):
        """Test creating a function with defaults."""
        func = function_factory.create()
        
        assert "name" in func
        assert func["file"] == "test.php"
        assert func["line"] == 42
        assert func["language"] == "php"

    def test_create_with_overrides(self, function_factory):
        """Test creating a function with custom values."""
        func = function_factory.create(
            name="saveEstimation",
            file="estimation.php",
            line=100,
        )
        
        assert func["name"] == "saveEstimation"
        assert func["file"] == "estimation.php"
        assert func["line"] == 100

    def test_create_batch(self, function_factory):
        """Test creating multiple functions."""
        funcs = function_factory.create_batch(5)
        
        assert len(funcs) == 5
        assert all("name" in f for f in funcs)


class TestCallFactory:
    """Tests for CallFactory."""

    def test_create_default(self, call_factory):
        """Test creating a call with defaults."""
        call = call_factory.create()
        
        assert "source" in call
        assert "target" in call
        assert "line" in call

    def test_create_chain(self, call_factory):
        """Test creating a call chain."""
        chain = call_factory.create_chain(["main", "helper", "db"])
        
        assert len(chain) == 2
        assert chain[0]["source"] == "main"
        assert chain[0]["target"] == "helper"
        assert chain[1]["source"] == "helper"
        assert chain[1]["target"] == "db"


class TestIndexFactory:
    """Tests for IndexFactory."""

    def test_create_default(self, index_factory):
        """Test creating an index with defaults."""
        index = index_factory.create()
        
        assert "module" in index
        assert "functions" in index
        assert "calls" in index
        assert len(index["functions"]) > 0

    def test_create_with_hierarchy(self, index_factory):
        """Test creating hierarchical index."""
        index = index_factory.create_with_hierarchy(depth=4)
        
        assert len(index["functions"]) == 4
        assert len(index["calls"]) == 3


class TestImpactResultFactory:
    """Tests for ImpactResultFactory."""

    def test_create_default(self, impact_factory):
        """Test creating impact result with defaults."""
        result = impact_factory.create()
        
        assert result["term"] == "testFunction"
        assert result["risk_level"] == "Medium"

    def test_create_high_risk(self, impact_factory):
        """Test creating high risk impact result."""
        result = impact_factory.create_high_risk("criticalFunction")
        
        assert result["risk_level"] == "High"
        callers = [c for c in result["calls"] if c["type"] == "called_by"]
        assert len(callers) == 15


class TestSharedFixtures:
    """Test shared pytest fixtures."""

    def test_sample_index(self, sample_index):
        """Test sample_index fixture."""
        assert "module" in sample_index
        assert "functions" in sample_index

    def test_sample_functions(self, sample_functions):
        """Test sample_functions fixture."""
        assert len(sample_functions) == 3
        names = [f["name"] for f in sample_functions]
        assert "main" in names
        assert "saveData" in names

    def test_sample_call_chain(self, sample_call_chain):
        """Test sample_call_chain fixture."""
        assert len(sample_call_chain) == 3
        assert sample_call_chain[0]["source"] == "main"
        assert sample_call_chain[-1]["target"] == "database"
