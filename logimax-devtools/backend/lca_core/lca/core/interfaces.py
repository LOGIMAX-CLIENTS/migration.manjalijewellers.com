"""
LCA Core - Abstract Interfaces
Defines contracts for parsers and storage (Dependency Inversion)
"""
from abc import ABC, abstractmethod
from dataclasses import dataclass, field
from pathlib import Path
from typing import AsyncIterator


@dataclass
class FunctionDef:
    """Represents a function/method definition"""
    name: str
    file: str
    line: int
    end_line: int | None = None
    class_name: str | None = None
    calls: list[tuple[str, int]] = field(default_factory=list)  # [(callee_name, line)]
    
    @property
    def qualified_name(self) -> str:
        """Full name including class if applicable"""
        if self.class_name:
            return f"{self.class_name}.{self.name}"
        return self.name


@dataclass
class CallInfo:
    """Represents a call relationship with location"""
    target: str
    line: int
    call_type: str = "calls"  # "calls" or "called_by"


@dataclass
class CodeIndex:
    """Complete index of a codebase"""
    name: str
    path: str
    functions: dict[str, FunctionDef] = field(default_factory=dict)
    calls: dict[str, list[tuple[str, int]]] = field(default_factory=dict)  # caller -> [(callee, line)]
    
    @property
    def function_count(self) -> int:
        return len(self.functions)
    
    @property
    def call_count(self) -> int:
        return sum(len(callees) for callees in self.calls.values())


@dataclass
class Level2Deps:
    """Second-level dependencies for a function"""
    calls: list[str] = field(default_factory=list)
    called_by: list[str] = field(default_factory=list)


@dataclass 
class ImpactResult:
    """Result of impact analysis - aligned with LCA"""
    function: str
    function_info: dict = field(default_factory=dict)  # {file, line, end_line, class_name}
    calls: list[CallInfo] = field(default_factory=list)  # All call relationships
    level2: dict[str, Level2Deps] = field(default_factory=dict)  # Second-level deps
    risk_level: str = "low"  # low, medium, high, critical
    
    @property
    def direct_callers(self) -> list[str]:
        return [c.target for c in self.calls if c.call_type == "called_by"]
    
    @property
    def direct_callees(self) -> list[str]:
        return [c.target for c in self.calls if c.call_type == "calls"]
    
    @property
    def total_impact(self) -> int:
        return len(self.calls)


class Parser(ABC):
    """Abstract parser interface - Strategy Pattern"""
    
    @property
    @abstractmethod
    def language(self) -> str:
        """Language name (e.g., 'PHP', 'JavaScript')"""
        pass
    
    @property
    @abstractmethod
    def extensions(self) -> list[str]:
        """File extensions this parser handles (e.g., ['.php'])"""
        pass
    
    @abstractmethod
    def parse_file(self, path: Path) -> list[FunctionDef]:
        """Parse a file and extract function definitions with calls"""
        pass
    
    def can_parse(self, path: Path) -> bool:
        """Check if this parser can handle the given file"""
        return path.suffix.lower() in self.extensions


class Storage(ABC):
    """Abstract storage interface - Repository Pattern"""
    
    @abstractmethod
    async def save_index(self, index: CodeIndex) -> None:
        """Save an index to storage"""
        pass
    
    @abstractmethod
    async def load_index(self, name: str) -> CodeIndex | None:
        """Load an index by name"""
        pass
    
    @abstractmethod
    async def list_indexes(self) -> list[str]:
        """List all available indexes"""
        pass
    
    @abstractmethod
    async def delete_index(self, name: str) -> bool:
        """Delete an index"""
        pass
    
    @abstractmethod
    async def get_callers(self, index_name: str, function: str) -> list[str]:
        """Get all functions that call the given function"""
        pass
    
    @abstractmethod
    async def get_callees(self, index_name: str, function: str) -> list[str]:
        """Get all functions called by the given function"""
        pass
    
    @abstractmethod
    async def search_functions(
        self, index_name: str, query: str, limit: int = 20
    ) -> list[FunctionDef]:
        """Search functions by name pattern"""
        pass


class IndexProgress:
    """Progress tracking for indexing operations"""
    
    def __init__(self, total_files: int = 0):
        self.total_files = total_files
        self.processed_files = 0
        self.current_file = ""
        self.errors: list[str] = []
    
    @property
    def progress(self) -> float:
        if self.total_files == 0:
            return 0.0
        return self.processed_files / self.total_files
    
    @property
    def is_complete(self) -> bool:
        return self.processed_files >= self.total_files
