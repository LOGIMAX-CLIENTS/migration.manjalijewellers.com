"""
Test Runner Interface
Abstract interface for test runners (Strategy Pattern)
"""
from abc import ABC, abstractmethod
from dataclasses import dataclass
from enum import Enum
from typing import AsyncIterator


class TestStatus(str, Enum):
    PENDING = "pending"
    RUNNING = "running"
    PASSED = "passed"
    FAILED = "failed"
    SKIPPED = "skipped"
    ERROR = "error"


@dataclass
class TestResult:
    """Single test result"""
    name: str
    status: TestStatus
    duration_ms: float = 0
    message: str | None = None
    file: str | None = None
    line: int | None = None


@dataclass
class TestSuiteResult:
    """Complete test suite result"""
    runner: str
    total: int
    passed: int
    failed: int
    skipped: int
    duration_ms: float
    tests: list[TestResult]
    coverage: float | None = None


class TestRunner(ABC):
    """Abstract test runner interface"""
    
    @property
    @abstractmethod
    def name(self) -> str:
        """Runner name (e.g., 'PHPUnit')"""
        pass
    
    @property
    @abstractmethod
    def language(self) -> str:
        """Language (e.g., 'PHP')"""
        pass
    
    @abstractmethod
    async def is_available(self) -> bool:
        """Check if runner is available"""
        pass
    
    @abstractmethod
    async def run(
        self,
        path: str | None = None,
        filter_pattern: str | None = None,
    ) -> TestSuiteResult:
        """Run tests and return results"""
        pass
    
    async def run_streaming(
        self,
        path: str | None = None,
        filter_pattern: str | None = None,
    ) -> AsyncIterator[TestResult]:
        """Run tests with streaming results"""
        result = await self.run(path, filter_pattern)
        for test in result.tests:
            yield test
