"""
LCA Services - Search Service
Function search functionality
"""
from lca_core.lca.storage import SQLiteStorage
from lca_core.lca.core import FunctionDef


class SearchService:
    """Service for searching functions"""
    
    def __init__(self, storage: SQLiteStorage | None = None):
        self.storage = storage or SQLiteStorage()
    
    async def search(
        self,
        index_name: str,
        query: str,
        limit: int = 20,
    ) -> list[FunctionDef]:
        """Search for functions matching a query"""
        return await self.storage.search_functions(index_name, query, limit)
    
    async def get_callers(self, index_name: str, function: str) -> list[str]:
        """Get all functions that call the given function"""
        results = await self.storage.get_callers(index_name, function)
        return [r[0] for r in results]
    
    async def get_callees(self, index_name: str, function: str) -> list[str]:
        """Get all functions called by the given function"""
        results = await self.storage.get_callees(index_name, function)
        return [r[0] for r in results]
    
    async def get_function_details(
        self, index_name: str, function_name: str
    ) -> dict | None:
        """Get detailed information about a function"""
        results = await self.storage.search_functions(index_name, function_name, 1)
        if not results:
            return None
        
        fn = results[0]
        callers = await self.get_callers(index_name, fn.qualified_name)
        callees = await self.get_callees(index_name, fn.qualified_name)
        
        # Read source code
        code_content = ""
        try:
            file_path = fn.file
            # Safe read: verify file exists and is a file
            import os
            if os.path.isfile(file_path):
                with open(file_path, 'r', encoding='utf-8', errors='replace') as f:
                    lines = f.readlines()
                    # line is 1-indexed
                    start = max(0, fn.line - 1)
                    end = fn.end_line if fn.end_line else start + 50
                    
                    if end > start:
                        code_content = "".join(lines[start:end])
        except Exception as e:
            code_content = f"# Error reading file source: {str(e)}"
        
        return {
            "name": fn.name,
            "qualified_name": fn.qualified_name,
            "file": fn.file,
            "line": fn.line,
            "end_line": fn.end_line,
            "class_name": fn.class_name,
            "callers": callers,
            "callees": callees,
            "caller_count": len(callers),
            "callee_count": len(callees),
            "code": code_content,
        }
