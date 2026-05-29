"""
LCA Plugin - Pydantic Schemas
Request/Response models for API
"""
from pydantic import BaseModel, Field
from typing import Optional


# Request schemas
class IndexRequest(BaseModel):
    path: str = Field(..., description="Path to directory to index")
    name: str = Field(..., description="Name for the index")
    exclude_patterns: list[str] = Field(default=[], description="Patterns to exclude")


class SearchRequest(BaseModel):
    query: str = Field(..., min_length=1, description="Search query")
    limit: int = Field(default=20, ge=1, le=100, description="Max results")


class ImpactRequest(BaseModel):
    function: str = Field(..., description="Function to analyze")
    depth: int = Field(default=2, ge=1, le=5, description="Analysis depth")


class GraphRequest(BaseModel):
    function: str = Field(..., description="Center function")
    depth: int = Field(default=2, ge=1, le=5, description="Graph depth")
    direction: str = Field(default="both", description="Direction: callers, callees, both")


# Response schemas
class FunctionResponse(BaseModel):
    name: str
    qualified_name: str
    file: str
    line: int
    end_line: Optional[int] = None
    class_name: Optional[str] = None


class FunctionDetailResponse(FunctionResponse):
    callers: list[str] = []
    callees: list[str] = []
    caller_count: int = 0
    callee_count: int = 0
    code: Optional[str] = None


class IndexStatusResponse(BaseModel):
    exists: bool
    name: Optional[str] = None
    path: Optional[str] = None
    functions: int = 0
    calls: int = 0


class SearchResponse(BaseModel):
    results: list[FunctionResponse]
    total: int


class CallInfoResponse(BaseModel):
    target: str
    line: int
    call_type: str


class Level2DepsResponse(BaseModel):
    calls: list[str] = []
    called_by: list[str] = []


class FunctionInfoResponse(BaseModel):
    file: str
    line: int
    end_line: Optional[int] = None
    class_name: Optional[str] = None


class ImpactResponse(BaseModel):
    function: str
    function_info: FunctionInfoResponse = Field(default_factory=dict)
    calls: list[CallInfoResponse]
    level2: dict[str, Level2DepsResponse]
    risk_level: str
    total_impact: int


class GraphNodeResponse(BaseModel):
    id: str
    label: str
    type: str  # center, caller, callee


class GraphEdgeResponse(BaseModel):
    source: str
    target: str


class GraphResponse(BaseModel):
    nodes: list[GraphNodeResponse]
    edges: list[GraphEdgeResponse]


class ExplanationResponse(BaseModel):
    function: str
    explanation: str
    is_mock: bool

class IndexProgressResponse(BaseModel):
    total_files: int
    processed_files: int
    current_file: str
    progress: float
    is_complete: bool
    errors: list[str] = []
