"""
LCA Services - Impact Service
Analyzes the impact of changing a function - aligned with LCA
"""
from lca_core.lca.core import ImpactResult, CallInfo, Level2Deps
from lca_core.lca.storage import SQLiteStorage


class ImpactService:
    """Service for impact analysis"""
    
    def __init__(self, storage: SQLiteStorage | None = None):
        self.storage = storage or SQLiteStorage()
    
    async def analyze_impact(
        self,
        index_name: str,
        function: str,
        depth: int = 2,
    ) -> ImpactResult:
        """Analyze the impact of changing a function - LCA aligned"""
        
        # Get function info
        function_info = {}
        func_defs = await self.storage.search_functions(index_name, function, limit=1)
        if func_defs:
            f = func_defs[0]
            function_info = {
                "file": f.file,
                "line": f.line,
                "end_line": f.end_line,
                "class_name": f.class_name,
            }
        
        # Build calls list with CallInfo objects
        calls: list[CallInfo] = []
        
        # Get direct callers (with line numbers)
        direct_callers = await self.storage.get_callers(index_name, function)
        for caller_name, line in direct_callers:
            calls.append(CallInfo(target=caller_name, line=line, call_type="called_by"))
        
        # Get direct callees (with line numbers)
        direct_callees = await self.storage.get_callees(index_name, function)
        for callee_name, line in direct_callees:
            calls.append(CallInfo(target=callee_name, line=line, call_type="calls"))
        
        # Build level2 dependencies
        level2: dict[str, Level2Deps] = {}
        
        if depth >= 2:
            # Get dependencies of each direct dependency
            all_direct = set(c[0] for c in direct_callers) | set(c[0] for c in direct_callees)
            
            for func in list(all_direct)[:20]:  # Limit to avoid explosion
                l2_calls = []
                l2_called_by = []
                
                # What does this func call?
                for callee, _ in await self.storage.get_callees(index_name, func):
                    if callee != function:
                        l2_calls.append(callee)
                
                # What calls this func?
                for caller, _ in await self.storage.get_callers(index_name, func):
                    if caller != function:
                        l2_called_by.append(caller)
                
                if l2_calls or l2_called_by:
                    level2[func] = Level2Deps(
                        calls=list(set(l2_calls[:10])),
                        called_by=list(set(l2_called_by[:10])),
                    )
        
        # Calculate risk level
        total_impact = len(calls)
        risk_level = self._calculate_risk_level(total_impact)
        
        return ImpactResult(
            function=function,
            function_info=function_info,
            calls=calls,
            level2=level2,
            risk_level=risk_level,
        )
    
    def _calculate_risk_level(self, total_callers: int) -> str:
        """Calculate risk level based on number of callers"""
        if total_callers == 0:
            return "low"
        elif total_callers <= 3:
            return "low"
        elif total_callers <= 10:
            return "medium"
        elif total_callers <= 25:
            return "high"
        else:
            return "critical"
    
    async def get_call_graph(
        self,
        index_name: str,
        function: str,
        depth: int = 2,
        direction: str = "both",  # "callers", "callees", "both"
    ) -> dict:
        """Get subgraph centered on a function for visualization"""
        nodes = [{"id": function, "label": function, "type": "center"}]
        edges = []
        visited = {function}
        
        async def add_callers(fn: str, current_depth: int):
            if current_depth > depth:
                return
            callers = await self.storage.get_callers(index_name, fn)
            for caller, _ in callers:  # Unpack (name, line)
                if caller not in visited:
                    visited.add(caller)
                    nodes.append({"id": caller, "label": caller, "type": "caller"})
                edges.append({"source": caller, "target": fn})
                if current_depth < depth:
                    await add_callers(caller, current_depth + 1)
        
        async def add_callees(fn: str, current_depth: int):
            if current_depth > depth:
                return
            callees = await self.storage.get_callees(index_name, fn)
            for callee, _ in callees:  # Unpack (name, line)
                if callee not in visited:
                    visited.add(callee)
                    nodes.append({"id": callee, "label": callee, "type": "callee"})
                edges.append({"source": fn, "target": callee})
                if current_depth < depth:
                    await add_callees(callee, current_depth + 1)
        
        if direction in ("callers", "both"):
            await add_callers(function, 1)
        if direction in ("callees", "both"):
            await add_callees(function, 1)
        
        return {"nodes": nodes, "edges": edges}
