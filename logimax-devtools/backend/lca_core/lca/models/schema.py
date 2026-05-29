"""Pydantic models for index data structures."""

from datetime import datetime
from pydantic import BaseModel, Field


class FunctionDef(BaseModel):
    """A function definition in the codebase."""
    name: str
    file: str
    line: int
    end_line: int | None = None
    params: list[str] = Field(default_factory=list)
    language: str = "javascript"  # or "php"


class FunctionCall(BaseModel):
    """A function call relationship."""
    source: str  # Caller function name
    target: str  # Called function name
    line: int
    file: str
    call_type: str = "calls"  # "calls" or "called_by"
    target_file: str | None = None  # File where target function is defined



class UIMatch(BaseModel):
    """A string reference/UI match in code."""
    func: str = "GLOBAL"
    line: int
    context: str
    file: str


class Level2Deps(BaseModel):
    """Level 2 dependencies for a function."""
    calls: list[str] = Field(default_factory=list)
    called_by: list[str] = Field(default_factory=list)


class ModuleIndex(BaseModel):
    """Complete index for a module."""
    module: str
    version: str = "2.0.0"
    indexed_at: str = Field(default_factory=lambda: datetime.now().isoformat())
    functions: list[FunctionDef] = Field(default_factory=list)
    calls: list[FunctionCall] = Field(default_factory=list)
    files: list[dict] = Field(default_factory=list)  # List of {path: str}
    ui_matches: list[UIMatch] = Field(default_factory=list)
    level2: dict[str, Level2Deps] = Field(default_factory=dict)
    
    def to_kb_format(self) -> dict:
        """Convert to Knowledge Base compatible JSON format."""
        return {
            "module": self.module,
            "version": self.version,
            "indexed_at": self.indexed_at,
            "functions": [f.model_dump() for f in self.functions],
            "calls": [
                {
                    "source": c.source,
                    "target": c.target,
                    "line": c.line,
                    "type": c.call_type
                }
                for c in self.calls
            ],
            "files": self.files,
            "ui_matches": [m.model_dump() for m in self.ui_matches],
            "level2": {
                name: deps.model_dump() 
                for name, deps in self.level2.items()
            }
        }


class ImpactResult(BaseModel):
    """Result of an impact analysis query."""
    term: str
    functions: list[FunctionDef] = Field(default_factory=list)
    calls: list[dict] = Field(default_factory=list)  # With type: calls/called_by
    ui_matches: list[UIMatch] = Field(default_factory=list)
    level2: dict[str, Level2Deps] = Field(default_factory=dict)
    risk_level: str = "Low"  # Low, Medium, High
