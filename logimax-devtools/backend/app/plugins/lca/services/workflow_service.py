"""
LCA Plugin - Workflow Service (Persistent)
Handles change request workflows with SQLite persistence
"""
import uuid
from datetime import datetime
from typing import Optional

from .task_storage import TaskStorage
from .search_service import SearchService
from .ai_service import AIService


class WorkflowService:
    """Service for managing change request workflows with persistence"""
    
    def __init__(self, search_service: SearchService, ai_service: AIService, storage: Optional[TaskStorage] = None):
        self.search = search_service
        self.ai = ai_service
        self.storage = storage or TaskStorage("tasks.db")
    
    async def create_change_request(
        self,
        index_name: str,
        target_function: str,
        intent: str,
        title: Optional[str] = None
    ) -> dict:
        """Create a new change request (persisted)"""
        task_id = str(uuid.uuid4())[:8]
        
        task = await self.storage.create_task(
            id=task_id,
            title=title or f"Change: {target_function}",
            index_name=index_name,
            target_function=target_function,
            intent=intent
        )
        
        return task
    
    async def analyze_impact(self, task_id: str, depth: int = 3) -> dict:
        """Perform deep impact analysis on the target function"""
        task = await self.storage.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        # Update status to analyzing
        await self.storage.update_status(task_id, "analyzing")
        
        index_name = task["index_name"]
        target_function = task["target_function"]
        
        # Get direct callers
        try:
            direct_callers = await self.search.get_callers(index_name, target_function)
        except Exception:
            direct_callers = []
        
        # Get indirect callers (callers of callers) up to depth
        indirect_callers = []
        if depth > 1:
            for caller in direct_callers[:10]:  # Limit to avoid explosion
                try:
                    second_level = await self.search.get_callers(index_name, caller)
                    indirect_callers.extend(second_level)
                except Exception:
                    pass
        indirect_callers = list(set(indirect_callers) - set(direct_callers))
        
        # Collect affected files from callers
        affected_files = set()
        all_callers = direct_callers + indirect_callers
        for caller in all_callers[:20]:  # Limit file lookups
            try:
                details = await self.search.get_function_details(index_name, caller)
                if details and details.get("file"):
                    affected_files.add(details["file"])
            except Exception:
                pass
        
        # Identify entry points (controllers, handlers, etc.)
        entry_points = []
        for caller in all_callers:
            caller_lower = caller.lower()
            if any(kw in caller_lower for kw in ["controller", "handler", "api", "route", "endpoint"]):
                entry_points.append(caller)
        
        # Calculate risk level based on impact
        total_impact = len(direct_callers) + len(indirect_callers)
        if total_impact >= 20:
            risk_level = "critical"
        elif total_impact >= 10:
            risk_level = "high"
        elif total_impact >= 5:
            risk_level = "medium"
        else:
            risk_level = "low"
        
        # Build impact report
        impact_report = {
            "target_function": target_function,
            "direct_callers": direct_callers,
            "indirect_callers": indirect_callers,
            "affected_files": list(affected_files),
            "entry_points": entry_points,
            "risk_level": risk_level,
            "total_impact": total_impact
        }
        
        # Save to database
        await self.storage.update_task(task_id, impact_report=impact_report)
        await self.storage.update_status(task_id, "impact_done")
        
        return impact_report

    
    async def generate_plan(self, task_id: str) -> str:
        """Use AI to generate an implementation plan"""
        task = await self.storage.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        impact_report = task.get("impact_report")
        if not impact_report:
            raise ValueError("Impact analysis must be run first")
        
        # Get the target function's code
        try:
            details = await self.search.get_function_details(task["index_name"], task["target_function"])
            target_code = details.get("code", "") if details else ""
        except:
            target_code = ""
        
        # Build context for AI
        context = f"""
Change Request: {task['title']}
Intent: {task.get('intent', 'No intent specified')}
Target Function: {task['target_function']}

Impact Analysis:
- Direct Callers ({len(impact_report.get('direct_callers', []))}): {', '.join(impact_report.get('direct_callers', [])[:5])}
- Indirect Callers ({len(impact_report.get('indirect_callers', []))}): {', '.join(impact_report.get('indirect_callers', [])[:5])}
- Affected Files: {len(impact_report.get('affected_files', []))}
- Entry Points: {', '.join(impact_report.get('entry_points', [])[:3])}
- Risk Level: {impact_report.get('risk_level', 'unknown')}
"""
        
        # Generate plan using AI service
        plan_prompt = f"""
Based on the following change request, generate a step-by-step implementation plan.
Format as a numbered checklist with clear, actionable items.

{context}

Target Function Code:
```
{target_code[:2000]}
```

Generate a safe refactoring plan that minimizes risk and ensures backward compatibility where possible.
"""
        
        plan = await self.ai.explain_function(plan_prompt, context="implementation_plan")
        
        # Save to database
        await self.storage.update_task(task_id, ai_plan=plan)
        await self.storage.update_status(task_id, "pending_approval")
        
        return plan
    
    async def approve_plan(self, task_id: str) -> dict:
        """Approve the plan and move to execution phase"""
        task = await self.storage.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        # Parse AI plan into subtasks
        subtasks = []
        if task.get("ai_plan"):
            import re
            lines = task["ai_plan"].split('\n')
            task_order = 0
            for line in lines:
                if re.match(r'^\d+\.|^-\s*\[', line.strip()):
                    task_order += 1
                    subtasks.append({
                        "id": f"subtask-{task_order}",
                        "title": line.strip().lstrip('0123456789.-[] '),
                        "status": "pending",
                        "order": task_order
                    })
        
        # Save subtasks and update status
        await self.storage.update_task(task_id, subtasks=subtasks)
        await self.storage.update_status(task_id, "approved")
        
        return await self.storage.get_task(task_id)
    
    async def update_subtask_status(self, task_id: str, subtask_id: str, status: str) -> dict:
        """Update the status of a specific subtask"""
        task = await self.storage.get_task(task_id)
        if not task:
            raise ValueError(f"Task {task_id} not found")
        
        subtasks = task.get("subtasks", [])
        updated = False
        
        for subtask in subtasks:
            if subtask["id"] == subtask_id:
                subtask["status"] = status
                updated = True
                break
        
        if not updated:
            raise ValueError(f"Subtask {subtask_id} not found")
        
        # Save updated subtasks
        await self.storage.update_task(task_id, subtasks=subtasks)
        
        # Check if all subtasks are complete
        if all(s["status"] in ("completed", "skipped") for s in subtasks):
            await self.storage.update_status(task_id, "completed")
        
        return {"id": subtask_id, "status": status}
    
    async def get_change_request(self, task_id: str) -> Optional[dict]:
        """Get a task by ID"""
        return await self.storage.get_task(task_id)
    
    async def list_change_requests(self, status: Optional[str] = None) -> list[dict]:
        """List all tasks"""
        return await self.storage.list_tasks(status=status)
    
    async def delete_change_request(self, task_id: str) -> bool:
        """Delete a task"""
        return await self.storage.delete_task(task_id)
