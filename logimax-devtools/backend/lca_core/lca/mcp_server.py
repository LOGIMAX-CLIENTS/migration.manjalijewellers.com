"""MCP Server for Logimax Code Analyzer.

Exposes code analysis tools to AI agents via Model Context Protocol.
"""

import asyncio
from pathlib import Path
from typing import Any

from mcp.server import Server
from mcp.server.stdio import stdio_server
from mcp.types import Tool, TextContent

from lca_core.lca.core import Indexer, ImpactAnalyzer
from lca_core.lca.storage import save_index, load_index
from lca_core.lca.models import ModuleIndex

# Global state
_indexes: dict[str, ModuleIndex] = {}
_default_index_path: Path | None = None

app = Server("logimax-code-analyzer")


def get_index(index_path: str | None = None) -> ModuleIndex | None:
    """Get an index from cache or load from file."""
    global _indexes, _default_index_path
    
    if index_path:
        path = Path(index_path)
    elif _default_index_path:
        path = _default_index_path
    else:
        return None
    
    path_str = str(path.absolute())
    
    if path_str not in _indexes:
        if path.exists():
            _indexes[path_str] = load_index(path)
        else:
            return None
    
    return _indexes[path_str]


@app.list_tools()
async def list_tools() -> list[Tool]:
    """List available tools."""
    return [
        Tool(
            name="index_module",
            description="Index a file or directory to build a dependency map. Returns function and call counts.",
            inputSchema={
                "type": "object",
                "properties": {
                    "path": {
                        "type": "string",
                        "description": "Path to file or directory to index"
                    },
                    "module_name": {
                        "type": "string",
                        "description": "Name for this module index"
                    },
                    "output_path": {
                        "type": "string",
                        "description": "Output JSON file path (optional)"
                    },
                    "recursive": {
                        "type": "boolean",
                        "description": "Recurse into subdirectories (default: true)"
                    }
                },
                "required": ["path", "module_name"]
            }
        ),
        Tool(
            name="get_impact",
            description="Analyze the impact of changes to a function. Returns callers, callees, and risk level.",
            inputSchema={
                "type": "object",
                "properties": {
                    "function_name": {
                        "type": "string",
                        "description": "Function name to analyze"
                    },
                    "index_path": {
                        "type": "string",
                        "description": "Path to index JSON file (optional if already loaded)"
                    },
                    "depth": {
                        "type": "integer",
                        "description": "Depth of dependency analysis (default: 2)"
                    }
                },
                "required": ["function_name"]
            }
        ),
        Tool(
            name="search_functions",
            description="Search for functions by name pattern.",
            inputSchema={
                "type": "object",
                "properties": {
                    "query": {
                        "type": "string",
                        "description": "Search query (case-insensitive contains)"
                    },
                    "index_path": {
                        "type": "string",
                        "description": "Path to index JSON file"
                    },
                    "limit": {
                        "type": "integer",
                        "description": "Maximum results (default: 20)"
                    }
                },
                "required": ["query"]
            }
        ),
        Tool(
            name="get_callers",
            description="Get all functions that call the specified function.",
            inputSchema={
                "type": "object",
                "properties": {
                    "function_name": {
                        "type": "string",
                        "description": "Function name to find callers of"
                    },
                    "index_path": {
                        "type": "string",
                        "description": "Path to index JSON file"
                    }
                },
                "required": ["function_name"]
            }
        ),
        Tool(
            name="get_callees",
            description="Get all functions called by the specified function.",
            inputSchema={
                "type": "object",
                "properties": {
                    "function_name": {
                        "type": "string",
                        "description": "Function name to find callees of"
                    },
                    "index_path": {
                        "type": "string",
                        "description": "Path to index JSON file"
                    }
                },
                "required": ["function_name"]
            }
        ),
        Tool(
            name="load_index",
            description="Load an index file into memory for faster subsequent queries.",
            inputSchema={
                "type": "object",
                "properties": {
                    "index_path": {
                        "type": "string",
                        "description": "Path to index JSON file"
                    },
                    "set_default": {
                        "type": "boolean",
                        "description": "Set this as the default index (default: true)"
                    }
                },
                "required": ["index_path"]
            }
        ),
    ]


@app.call_tool()
async def call_tool(name: str, arguments: dict[str, Any]) -> list[TextContent]:
    """Handle tool calls."""
    global _indexes, _default_index_path
    
    try:
        if name == "index_module":
            path = Path(arguments["path"])
            module_name = arguments["module_name"]
            output_path = arguments.get("output_path")
            recursive = arguments.get("recursive", True)
            
            if not path.exists():
                return [TextContent(type="text", text=f"Error: Path does not exist: {path}")]
            
            indexer = Indexer()
            
            if path.is_file():
                result = indexer.index_file(path, module_name)
            else:
                result = indexer.index_directory(path, module_name, recursive=recursive)
            
            # Determine output path
            if output_path:
                out_path = Path(output_path)
            else:
                out_path = Path(f"./{module_name}_index.json")
            
            save_index(result, out_path)
            
            # Cache and set as default
            _indexes[str(out_path.absolute())] = result
            _default_index_path = out_path.absolute()
            
            return [TextContent(
                type="text",
                text=f"✓ Indexed {len(result.functions)} functions and {len(result.calls)} call relationships.\n"
                     f"Output: {out_path}"
            )]
        
        elif name == "get_impact":
            function_name = arguments["function_name"]
            index_path = arguments.get("index_path")
            depth = arguments.get("depth", 2)
            
            idx = get_index(index_path)
            if not idx:
                return [TextContent(type="text", text="Error: No index loaded. Use load_index or index_module first.")]
            
            analyzer = ImpactAnalyzer(idx)
            result = analyzer.analyze(function_name, depth=depth)
            
            # Format output
            callers = [c for c in result.calls if c["type"] == "called_by"]
            callees = [c for c in result.calls if c["type"] == "calls"]
            
            output = f"## Impact Analysis: {function_name}\n\n"
            output += f"**Risk Level:** {result.risk_level}\n\n"
            
            if callers:
                output += f"### Called By ({len(callers)} callers)\n"
                for c in callers[:10]:
                    output += f"- {c['target']} (line {c['line']})\n"
                if len(callers) > 10:
                    output += f"- ... +{len(callers) - 10} more\n"
                output += "\n"
            
            if callees:
                output += f"### Calls ({len(callees)} dependencies)\n"
                for c in callees[:10]:
                    output += f"- {c['target']} (line {c['line']})\n"
                if len(callees) > 10:
                    output += f"- ... +{len(callees) - 10} more\n"
                output += "\n"
            
            if result.functions:
                f = result.functions[0]
                output += f"**Defined at:** {f.file}:{f.line}\n"
            
            return [TextContent(type="text", text=output)]
        
        elif name == "search_functions":
            query = arguments["query"]
            index_path = arguments.get("index_path")
            limit = arguments.get("limit", 20)
            
            idx = get_index(index_path)
            if not idx:
                return [TextContent(type="text", text="Error: No index loaded.")]
            
            query_lower = query.lower().replace("*", "")
            matches = [f for f in idx.functions if query_lower in f.name.lower()][:limit]
            
            if not matches:
                return [TextContent(type="text", text=f"No functions found matching: {query}")]
            
            output = f"## Functions matching '{query}'\n\n"
            for f in matches:
                output += f"- **{f.name}** ({Path(f.file).name}:{f.line}) [{f.language}]\n"
            
            return [TextContent(type="text", text=output)]
        
        elif name == "get_callers":
            function_name = arguments["function_name"]
            index_path = arguments.get("index_path")
            
            idx = get_index(index_path)
            if not idx:
                return [TextContent(type="text", text="Error: No index loaded.")]
            
            analyzer = ImpactAnalyzer(idx)
            callers = analyzer.get_callers(function_name)
            
            if not callers:
                return [TextContent(type="text", text=f"No callers found for: {function_name}")]
            
            output = f"## Functions that call {function_name}\n\n"
            for caller, line in callers:
                output += f"- {caller} (line {line})\n"
            
            return [TextContent(type="text", text=output)]
        
        elif name == "get_callees":
            function_name = arguments["function_name"]
            index_path = arguments.get("index_path")
            
            idx = get_index(index_path)
            if not idx:
                return [TextContent(type="text", text="Error: No index loaded.")]
            
            analyzer = ImpactAnalyzer(idx)
            callees = analyzer.get_callees(function_name)
            
            if not callees:
                return [TextContent(type="text", text=f"No callees found for: {function_name}")]
            
            output = f"## Functions called by {function_name}\n\n"
            for callee, line in callees:
                output += f"- {callee} (line {line})\n"
            
            return [TextContent(type="text", text=output)]
        
        elif name == "load_index":
            index_path = arguments["index_path"]
            set_default = arguments.get("set_default", True)
            
            path = Path(index_path)
            if not path.exists():
                return [TextContent(type="text", text=f"Error: Index file not found: {path}")]
            
            idx = load_index(path)
            _indexes[str(path.absolute())] = idx
            
            if set_default:
                _default_index_path = path.absolute()
            
            return [TextContent(
                type="text",
                text=f"✓ Loaded index: {idx.module}\n"
                     f"  - {len(idx.functions)} functions\n"
                     f"  - {len(idx.calls)} call relationships"
            )]
        
        else:
            return [TextContent(type="text", text=f"Unknown tool: {name}")]
    
    except Exception as e:
        return [TextContent(type="text", text=f"Error: {str(e)}")]


async def main():
    """Run the MCP server."""
    async with stdio_server() as (read_stream, write_stream):
        await app.run(read_stream, write_stream, app.create_initialization_options())


def run_server():
    """Entry point for running the server."""
    asyncio.run(main())


if __name__ == "__main__":
    run_server()
