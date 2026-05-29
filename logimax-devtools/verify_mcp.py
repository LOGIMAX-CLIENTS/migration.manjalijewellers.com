import asyncio
import os
from backend.app.plugins.lca.services.ai_service import AIService
from backend.app.plugins.lca.services.task_storage import TaskStorage
from backend.app.plugins.lca.mcp.server import call_tool

# Mock environment
os.environ["OPENAI_API_KEY"] = "" # Force mock/pending mode

async def test_mcp_workflow():
    print("--- 1. Initializing Service ---")
    ai = AIService()
    
    print("\n--- 2. Creating Task (Simulating User Request) ---")
    code = "function test() { return true; }"
    context = "Function: test_func"
    result_msg = await ai.explain_function(code, context)
    print(f"Result Message: {result_msg}")
    
    assert "Pending AI Agent" in result_msg
    task_id = result_msg.split("Task ID: ")[1].split(")")[0]
    print(f"Task ID created: {task_id}")

    print("\n--- 3. Listing Pending Tasks via MCP (Simulating Agent) ---")
    # Call list_pending_explanations
    tools_result = await call_tool("list_pending_explanations", {})
    pending_tasks_json = tools_result[0].text
    print(f"Pending Tasks JSON: {pending_tasks_json}")
    
    assert task_id in pending_tasks_json
    
    print("\n--- 4. Getting Context via MCP ---")
    context_result = await call_tool("get_function_context", {"task_id": task_id})
    print(f"Context: {context_result[0].text}")
    
    print("\n--- 5. Submitting Explanation via MCP ---")
    explanation = "This is a test explanation from the Agent."
    submit_result = await call_tool("submit_explanation", {"task_id": task_id, "explanation": explanation})
    print(f"Submit Result: {submit_result[0].text}")
    
    print("\n--- 6. Verifying Task Update ---")
    storage = TaskStorage("tasks.db")
    task = await storage.get_task(task_id)
    print(f"Updated Task: {task}")
    
    # Check if explanation saved correctly
    # Note: ai_service saves to 'intent' as JSON initially, but submit_explanation saves to 'ai_explanation' column/field if it exists?
    # Our server implementation uses 'ai_explanation' key in update_task.
    # Let's see if TaskStorage supports it. (It relies on generic **kwargs usually)
    
    assert task.get("ai_explanation") == explanation
    print("SUCCESS: Workflow Verified!")

if __name__ == "__main__":
    # Ensure CWD is root of project for imports to work nicely or set PYTHONPATH
    # We will run this from c:\xampp\htdocs\etail_v3\logimax-devtools
    asyncio.run(test_mcp_workflow())
