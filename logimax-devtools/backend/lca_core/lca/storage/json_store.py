"""JSON file storage for indexes."""

import json
from pathlib import Path

from lca_core.lca.models import ModuleIndex, FunctionDef, FunctionCall, Level2Deps


def save_index(index: ModuleIndex, output_path: Path) -> None:
    """Save a module index to a JSON file."""
    output_path.parent.mkdir(parents=True, exist_ok=True)
    
    data = index.to_kb_format()
    
    with open(output_path, "w", encoding="utf-8") as f:
        json.dump(data, f, indent=2, ensure_ascii=False)


def load_index(path: Path) -> ModuleIndex:
    """Load a module index from a JSON file (KB format)."""
    with open(path, "r", encoding="utf-8") as f:
        data = json.load(f)
    
    # Convert KB format back to ModuleIndex
    functions = [
        FunctionDef(
            name=f["name"],
            file=f["file"],
            line=f["line"],
            end_line=f.get("end_line"),
            params=f.get("params", []),
            language=f.get("language", "javascript")
        )
        for f in data.get("functions", [])
    ]
    
    calls = [
        FunctionCall(
            source=c.get("source", "GLOBAL"),
            target=c["target"],
            line=c["line"],
            file=c.get("file", ""),
            call_type=c.get("type", "calls")
        )
        for c in data.get("calls", [])
    ]
    
    level2 = {}
    for name, deps in data.get("level2", {}).items():
        level2[name] = Level2Deps(
            calls=deps.get("calls", []),
            called_by=deps.get("called_by", [])
        )
    
    return ModuleIndex(
        module=data.get("module", "unknown"),
        version=data.get("version", "2.0.0"),
        indexed_at=data.get("indexed_at", ""),
        functions=functions,
        calls=calls,
        level2=level2
    )
