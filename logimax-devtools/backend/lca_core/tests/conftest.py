"""
Pytest configuration and shared fixtures for LCA tests.

Fixtures defined here are automatically available to all test files.
"""

import pytest
import json
from pathlib import Path

# Import all factories for easy access
from tests.factories import (
    FunctionFactory,
    CallFactory,
    IndexFactory,
    ImpactResultFactory,
    function_factory,
    call_factory,
    index_factory,
    impact_factory,
    sample_index,
    sample_functions,
    sample_call_chain,
    temp_index_file,
)


# ============================================
# PYTEST CONFIGURATION
# ============================================

def pytest_configure(config):
    """Configure custom markers."""
    config.addinivalue_line(
        "markers", "slow: marks tests as slow (deselect with '-m \"not slow\"')"
    )
    config.addinivalue_line(
        "markers", "integration: marks tests as integration tests"
    )


# ============================================
# MODULE FIXTURES
# ============================================

@pytest.fixture
def lca_root():
    """Return the LCA project root directory."""
    return Path(__file__).parent.parent


@pytest.fixture
def sample_php_code():
    """Provide sample PHP code for parsing tests."""
    return '''<?php
class TestController {
    public function index() {
        $this->load->model('test_model');
        $data = $this->test_model->getData();
        return $data;
    }
    
    public function save() {
        $result = $this->test_model->insertData($_POST);
        if ($result) {
            redirect('success');
        }
    }
}
'''


@pytest.fixture
def sample_js_code():
    """Provide sample JavaScript code for parsing tests."""
    return '''
function calculateTotal(items) {
    let total = 0;
    items.forEach(item => {
        total += item.price * item.qty;
    });
    return total;
}

async function saveEstimation(data) {
    const response = await fetch('/api/estimation/save', {
        method: 'POST',
        body: JSON.stringify(data)
    });
    return response.json();
}
'''


@pytest.fixture
def mock_index_data():
    """Provide mock index data for testing."""
    return {
        "module": "test_module",
        "functions": [
            {"name": "main", "file": "index.php", "line": 1, "language": "php"},
            {"name": "helper", "file": "utils.php", "line": 10, "language": "php"},
            {"name": "calculateTotal", "file": "calc.js", "line": 5, "language": "javascript"},
        ],
        "calls": [
            {"source": "main", "target": "helper", "line": 5, "file": "index.php"},
            {"source": "helper", "target": "calculateTotal", "line": 15, "file": "utils.php"},
        ],
        "indexed_at": "2026-01-31T12:00:00Z",
    }


# ============================================
# ASYNC FIXTURES
# ============================================

@pytest.fixture
def event_loop_policy():
    """Use default event loop policy for async tests."""
    import asyncio
    return asyncio.DefaultEventLoopPolicy()
