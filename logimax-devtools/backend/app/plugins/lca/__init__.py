"""
LCA Plugin - Main Registration
Registers LCA as a backend plugin
"""
from fastapi import APIRouter

from ...core.plugin_system import BackendPlugin
from .router import create_router


class LcaPlugin(BackendPlugin):
    """LCA Cockpit backend plugin - Code analysis and impact visualization"""
    
    @property
    def id(self) -> str:
        return "lca"
    
    @property
    def name(self) -> str:
        return "LCA Cockpit"
    
    @property
    def version(self) -> str:
        return "2.0.0"  # Major version bump for full rebuild
    
    def get_router(self) -> APIRouter:
        return create_router()
    
    def get_mcp_tools(self) -> list[dict]:
        """Define MCP tools for Antigravity integration"""
        return [
            {
                "name": "lca_index",
                "description": "Index a codebase to build call graphs",
                "parameters": {
                    "path": {"type": "string", "description": "Path to directory"},
                    "name": {"type": "string", "description": "Index name"},
                },
            },
            {
                "name": "lca_search",
                "description": "Search for functions by name pattern",
                "parameters": {
                    "index_name": {"type": "string", "description": "Index to search"},
                    "query": {"type": "string", "description": "Search query"},
                    "limit": {"type": "integer", "description": "Max results", "default": 20},
                },
            },
            {
                "name": "lca_callers",
                "description": "Get all functions that call a specific function",
                "parameters": {
                    "index_name": {"type": "string"},
                    "function": {"type": "string", "description": "Function name"},
                },
            },
            {
                "name": "lca_callees",
                "description": "Get all functions called by a specific function",
                "parameters": {
                    "index_name": {"type": "string"},
                    "function": {"type": "string", "description": "Function name"},
                },
            },
            {
                "name": "lca_impact",
                "description": "Analyze impact of changing a function",
                "parameters": {
                    "index_name": {"type": "string"},
                    "function": {"type": "string"},
                    "depth": {"type": "integer", "default": 2},
                },
            },
        ]
    
    async def on_init(self) -> None:
        """Initialize the plugin"""
        # Ensure database is ready
        from lca_core.lca.storage import SQLiteStorage
        storage = SQLiteStorage()
        await storage._init_db()
