"""Impact analysis engine."""

from lca_core.lca.models import ModuleIndex, ImpactResult, FunctionDef, Level2Deps, UIMatch


class ImpactAnalyzer:
    """Analyzes impact of changes to a function."""
    
    def __init__(self, index: ModuleIndex):
        self.index = index
        self._build_lookup_maps()
    
    def _build_lookup_maps(self):
        """Build fast lookup maps from the index."""
        # Function name -> FunctionDef
        self.func_map: dict[str, FunctionDef] = {
            f.name: f for f in self.index.functions
        }
        
        # Function -> list of functions it calls
        self.calls_map: dict[str, list[tuple[str, int]]] = {}
        
        # Function -> list of functions that call it
        self.called_by_map: dict[str, list[tuple[str, int]]] = {}
        
        for call in self.index.calls:
            # Forward: source calls target
            if call.source not in self.calls_map:
                self.calls_map[call.source] = []
            self.calls_map[call.source].append((call.target, call.line))
            
            # Reverse: target is called by source
            if call.target not in self.called_by_map:
                self.called_by_map[call.target] = []
            self.called_by_map[call.target].append((call.source, call.line))
    
    def analyze(self, term: str, depth: int = 2) -> ImpactResult:
        """
        Analyze impact of changes to a function.
        
        Args:
            term: Function name to analyze
            depth: How many levels of dependencies to include
            
        Returns:
            ImpactResult with all affected functions
        """
        # Find function definitions
        functions: list[FunctionDef] = []
        if term in self.func_map:
            functions.append(self.func_map[term])
        
        # Also search for partial matches
        for name, func in self.func_map.items():
            if term.lower() in name.lower() and func not in functions:
                functions.append(func)
        
        # Get direct calls (what this function calls)
        calls: list[dict] = []
        
        for target, line in self.calls_map.get(term, []):
            calls.append({
                "type": "calls",
                "target": target,
                "line": line
            })
        
        # Get called_by (what calls this function)
        for source, line in self.called_by_map.get(term, []):
            calls.append({
                "type": "called_by",
                "target": source,
                "line": line
            })
        
        # Get level2 dependencies
        level2: dict[str, Level2Deps] = {}
        
        # For each direct dependency, get its dependencies
        direct_funcs = set()
        for call in calls:
            direct_funcs.add(call["target"])
        
        for func in direct_funcs:
            l2_calls = []
            l2_called_by = []
            
            # What does this func call (excluding term)?
            for target, _ in self.calls_map.get(func, []):
                if target != term:
                    l2_calls.append(target)
            
            # What calls this func (excluding term)?
            for source, _ in self.called_by_map.get(func, []):
                if source != term:
                    l2_called_by.append(source)
            
            if l2_calls or l2_called_by:
                level2[func] = Level2Deps(
                    calls=list(set(l2_calls)),
                    called_by=list(set(l2_called_by))
                )
        
        # UI matches (string references)
        ui_matches: list[UIMatch] = [
            m for m in self.index.ui_matches
            if term.lower() in m.context.lower()
        ]
        
        # Calculate risk level
        total_affected = len(calls)
        if total_affected > 10:
            risk_level = "High"
        elif total_affected > 5:
            risk_level = "Medium"
        else:
            risk_level = "Low"
        
        return ImpactResult(
            term=term,
            functions=functions,
            calls=calls,
            ui_matches=ui_matches,
            level2=level2,
            risk_level=risk_level
        )
    
    def get_callers(self, func_name: str) -> list[tuple[str, int]]:
        """Get all functions that call the given function."""
        return self.called_by_map.get(func_name, [])
    
    def get_callees(self, func_name: str) -> list[tuple[str, int]]:
        """Get all functions called by the given function."""
        return self.calls_map.get(func_name, [])
