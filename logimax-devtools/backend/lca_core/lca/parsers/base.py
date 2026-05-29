"""Abstract base parser interface."""

from abc import ABC, abstractmethod
from pathlib import Path

from lca_core.lca.models import FunctionDef, FunctionCall


class BaseParser(ABC):
    """Abstract base class for language parsers."""
    
    @property
    @abstractmethod
    def language(self) -> str:
        """Return the language this parser handles."""
        pass
    
    @property
    @abstractmethod
    def extensions(self) -> list[str]:
        """Return file extensions this parser handles."""
        pass
    
    @abstractmethod
    def parse_file(self, file_path: Path) -> tuple[list[FunctionDef], list[FunctionCall]]:
        """
        Parse a source file and extract functions and calls.
        
        Args:
            file_path: Path to the source file
            
        Returns:
            Tuple of (function definitions, function calls)
        """
        pass
    
    def can_parse(self, file_path: Path) -> bool:
        """Check if this parser can handle the given file."""
        return file_path.suffix.lower() in self.extensions
