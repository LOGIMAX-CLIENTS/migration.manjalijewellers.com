"""
LCA Plugin - MCP Server
Exposes backend tools to AI Agents (Antigravity) via MCP.
"""
import asyncio
import json
from mcp.server import Server
from mcp.server.stdio import stdio_server
from mcp.types import Tool, TextContent
from typing import Any, List

from ..services.workflow_service import WorkflowService
from ..services.search_service import SearchService
from ..services.ai_service import AIService

# We need a way to access the services. 
# In a real app, these would be injected or singletons.
# For this MCP script, we might need to initialize them.

app = Server("lca-backend")

_workflow_service = None

def get_workflow_service():
    global _workflow_service
    if not _workflow_service:
        # Initialize services
        # Note: This duplicates main.py initialization, but permissible for a standalone MCP process
        search_service = SearchService()
        ai_service = AIService()
        _workflow_service = WorkflowService(search_service, ai_service)
    return _workflow_service

@app.list_tools()
async def list_tools() -> list[Tool]:
    return [
        Tool(
            name="list_pending_explanations",
            description="List tasks waiting for AI explanation.",
            inputSchema={
                "type": "object",
                "properties": {},
            }
        ),
        Tool(
            name="get_function_context",
            description="Get the code and context for a specific task/function.",
            inputSchema={
                "type": "object",
                "properties": {
                    "task_id": {"type": "string"},
                },
                "required": ["task_id"]
            }
        ),
        Tool(
            name="submit_explanation",
            description="Submit the AI explanation for a task.",
            inputSchema={
                "type": "object",
                "properties": {
                    "task_id": {"type": "string"},
                    "explanation": {"type": "string"}
                },
                "required": ["task_id", "explanation"]
            }
        )
    ]

@app.call_tool()
async def call_tool(name: str, arguments: dict) -> list[TextContent]:
    service = get_workflow_service()
    
    if name == "list_pending_explanations":
        tasks = await service.list_change_requests(status="pending_explanation")
        return [TextContent(type="text", text=json.dumps(tasks, indent=2))]
    
    elif name == "get_function_context":
        task_id = arguments["task_id"]
        task = await service.get_change_request(task_id)
        if not task:
            return [TextContent(type="text", text="Task not found")]
        
        # Fetch code via search service (implied in task info or fetch fresh)
        # simplified context retrieval
        return [TextContent(type="text", text=json.dumps(task, indent=2))]

    elif name == "submit_explanation":
        task_id = arguments["task_id"]
        explanation = arguments["explanation"]
        
        # Update task directly in storage
        # We save to 'ai_explanation' and update status to 'explanation_complete'
        await service.storage.update_task(task_id, ai_explanation=explanation)
        await service.storage.update_status(task_id, "explanation_complete")
        
        return [TextContent(type="text", text="Explanation submitted successfully.")]
    
    return [TextContent(type="text", text=f"Unknown tool: {name}")]

async def main():
    async with stdio_server() as (read_stream, write_stream):
        await app.run(read_stream, write_stream, app.create_initialization_options())

if __name__ == "__main__":
    import asyncio
    asyncio.run(main())
