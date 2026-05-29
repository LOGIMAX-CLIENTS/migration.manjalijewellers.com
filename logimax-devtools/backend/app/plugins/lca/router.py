"""
LCA Plugin - FastAPI Router
API endpoints for code analysis
"""
from fastapi import APIRouter, HTTPException, BackgroundTasks
from typing import Optional

from .schemas import (
    IndexRequest, SearchRequest, ImpactRequest, GraphRequest,
    IndexStatusResponse, SearchResponse, ImpactResponse, GraphResponse, ExplanationResponse,
    FunctionResponse, FunctionDetailResponse, IndexProgressResponse
)
from .services import IndexService, SearchService, ImpactService, AIService, WorkflowService, QualityService
from lca_core.lca.storage import SQLiteStorage


# Shared storage instance
storage = SQLiteStorage(db_path="lca_index.db")

# Service instances
index_service = IndexService(storage)
search_service = SearchService(storage)
impact_service = ImpactService(storage)
ai_service = AIService()
workflow_service = WorkflowService(search_service, ai_service)
quality_service = QualityService(impact_service)

# Track active indexing jobs
active_jobs: dict[str, IndexProgressResponse] = {}


def create_router() -> APIRouter:
    """Create and configure the LCA router"""
    router = APIRouter(tags=["LCA"])
    
    # ============ Index Endpoints ============
    
    @router.get("/indexes", summary="List all indexes")
    async def list_indexes() -> list[str]:
        """Get list of all available indexes"""
        return await index_service.list_indexes()
    
    @router.get("/status/{name}", response_model=IndexStatusResponse, summary="Get index status")
    async def get_index_status(name: str) -> IndexStatusResponse:
        """Get status and statistics for an index"""
        status = await index_service.get_index_status(name)
        return IndexStatusResponse(**status)
    
    @router.post("/index", summary="Start indexing")
    async def start_indexing(
        request: IndexRequest,
        background_tasks: BackgroundTasks
    ) -> dict:
        """Start indexing a directory in the background"""
        
        async def run_indexing():
            def on_progress(progress):
                active_jobs[request.name] = IndexProgressResponse(
                    total_files=progress.total_files,
                    processed_files=progress.processed_files,
                    current_file=progress.current_file,
                    progress=progress.progress,
                    is_complete=progress.is_complete,
                    errors=progress.errors,
                )
            
            try:
                await index_service.index_directory(
                    path=request.path,
                    name=request.name,
                    exclude_patterns=request.exclude_patterns,
                    on_progress=on_progress,
                )
            finally:
                # Mark as complete
                if request.name in active_jobs:
                    active_jobs[request.name].is_complete = True
        
        # Initialize job status
        active_jobs[request.name] = IndexProgressResponse(
            total_files=0,
            processed_files=0,
            current_file="Starting...",
            progress=0.0,
            is_complete=False,
        )
        
        background_tasks.add_task(run_indexing)
        
        return {"status": "started", "name": request.name}
    
    @router.get("/index/progress/{name}", response_model=IndexProgressResponse, summary="Get indexing progress")
    async def get_index_progress(name: str) -> IndexProgressResponse:
        """Get progress of an active indexing job"""
        if name not in active_jobs:
            raise HTTPException(status_code=404, detail="No active indexing job for this name")
        return active_jobs[name]
    
    @router.delete("/index/{name}", summary="Delete index")
    async def delete_index(name: str) -> dict:
        """Delete an index"""
        deleted = await index_service.delete_index(name)
        if not deleted:
            raise HTTPException(status_code=404, detail="Index not found")
        return {"deleted": True}
    
    # ============ Search Endpoints ============
    
    @router.get("/search/{index_name}", response_model=SearchResponse, summary="Search functions")
    async def search_functions(
        index_name: str,
        q: str,
        limit: int = 20
    ) -> SearchResponse:
        """Search for functions by name pattern"""
        results = await search_service.search(index_name, q, limit)
        return SearchResponse(
            results=[FunctionResponse(
                name=fn.name,
                qualified_name=fn.qualified_name,
                file=fn.file,
                line=fn.line,
                end_line=fn.end_line,
                class_name=fn.class_name,
            ) for fn in results],
            total=len(results),
        )
    
    @router.get("/function/{index_name}/{function_name}", summary="Get function details")
    async def get_function_details(
        index_name: str,
        function_name: str
    ) -> FunctionDetailResponse:
        """Get detailed information about a function"""
        details = await search_service.get_function_details(index_name, function_name)
        if not details:
            raise HTTPException(status_code=404, detail="Function not found")
        return FunctionDetailResponse(**details)
    
    @router.get("/callers/{index_name}/{function}", summary="Get callers")
    async def get_callers(index_name: str, function: str) -> dict:
        """Get all functions that call the specified function"""
        callers = await search_service.get_callers(index_name, function)
        return {"function": function, "callers": callers, "count": len(callers)}
    
    @router.get("/callees/{index_name}/{function}", summary="Get callees")
    async def get_callees(index_name: str, function: str) -> dict:
        """Get all functions called by the specified function"""
        callees = await search_service.get_callees(index_name, function)
        return {"function": function, "callees": callees, "count": len(callees)}
    
    # ============ Impact Endpoints ============
    
    @router.get("/impact/{index_name}/{function}", response_model=ImpactResponse, summary="Impact analysis")
    async def get_impact(
        index_name: str,
        function: str,
        depth: int = 2
    ) -> ImpactResponse:
        """Analyze the impact of changing a function"""
        result = await impact_service.analyze_impact(index_name, function, depth)
        return ImpactResponse(
            function=result.function,
            function_info=result.function_info,
            calls=[{
                "target": c.target,
                "line": c.line,
                "call_type": c.call_type
            } for c in result.calls],
            level2={
                k: {
                    "calls": v.calls,
                    "called_by": v.called_by
                } for k, v in result.level2.items()
            },
            risk_level=result.risk_level,
            total_impact=result.total_impact,
        )
    
    @router.get("/graph/{index_name}/{function}", response_model=GraphResponse, summary="Get call graph")
    async def get_call_graph(
        index_name: str,
        function: str,
        depth: int = 2,
        direction: str = "both"
    ) -> GraphResponse:
        """Get subgraph for visualization"""
        graph = await impact_service.get_call_graph(index_name, function, depth, direction)
        return GraphResponse(**graph)

    @router.get("/explain/{index_name}/{function}", response_model=ExplanationResponse, summary="Explain function")
    async def explain_function(index_name: str, function: str) -> ExplanationResponse:
        """Explain a function using AI"""
        # Get function details to extract code
        details = await search_service.get_function_details(index_name, function)
        if not details or not details.get("code"):
            raise HTTPException(status_code=404, detail="Function code not found")
            
        # Get impact analysis to enrich the context
        try:
            impact = await impact_service.analyze_impact(index_name, function, depth=1)
            impact_data = {
                "risk_level": impact.risk_level,
                "total_impact": impact.total_impact,
                "direct_calls": len(impact.calls),
                "level2_impact": len(impact.level2)
            }
        except Exception as e:
            print(f"Impact analysis failed: {e}")
            impact_data = None
        
        explanation = await ai_service.explain_function(
            details["code"], 
            context=f"Function: {function}",
            impact_data=impact_data
        )
        return ExplanationResponse(
            function=function,
            explanation=explanation,
            is_mock=ai_service.mock_mode
        )
    
    # ============ Workflow Endpoints ============
    
    @router.post("/workflow/create", summary="Create change request")
    async def create_change_request(
        index_name: str,
        target_function: str,
        intent: str,
        title: str = None
    ):
        """Create a new change request workflow"""
        cr = await workflow_service.create_change_request(
            index_name=index_name,
            target_function=target_function,
            intent=intent,
            title=title
        )
        return cr  # Already a dict from SQLite
    
    @router.post("/workflow/{cr_id}/analyze", summary="Analyze impact")
    async def analyze_impact(cr_id: str, depth: int = 3):
        """Run impact analysis on a change request"""
        try:
            report = await workflow_service.analyze_impact(cr_id, depth)
            return report  # Already a dict
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/workflow/{cr_id}/plan", summary="Generate AI plan")
    async def generate_plan(cr_id: str):
        """Generate an AI implementation plan"""
        try:
            plan = await workflow_service.generate_plan(cr_id)
            return {"plan": plan}
        except ValueError as e:
            raise HTTPException(status_code=400, detail=str(e))
    
    @router.post("/workflow/{cr_id}/approve", summary="Approve plan")
    async def approve_plan(cr_id: str):
        """Approve the plan and start execution tracking"""
        try:
            cr = await workflow_service.approve_plan(cr_id)
            return cr  # Already a dict from SQLite
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.get("/workflow/{cr_id}", summary="Get change request")
    async def get_change_request(cr_id: str):
        """Get details of a change request"""
        cr = await workflow_service.get_change_request(cr_id)
        if not cr:
            raise HTTPException(status_code=404, detail="Change request not found")
        return cr  # Already a dict from SQLite
    
    @router.get("/workflow", summary="List change requests")
    async def list_change_requests():
        """List all change requests"""
        tasks = await workflow_service.list_change_requests()
        return tasks  # Already a list of dicts from SQLite
    
    @router.delete("/workflow/{cr_id}", summary="Delete change request")
    async def delete_change_request(cr_id: str):
        """Delete a change request"""
        success = await workflow_service.delete_change_request(cr_id)
        if not success:
            raise HTTPException(status_code=404, detail="Change request not found")
        return {"deleted": True}
    
    @router.patch("/workflow/{cr_id}/task/{task_id}", summary="Update task status")
    async def update_task_status(cr_id: str, task_id: str, status: str):
        """Update the status of a subtask"""
        try:
            result = await workflow_service.update_subtask_status(cr_id, task_id, status)
            return result  # Already a dict
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
            
    # ============ Tools Endpoints ============

    @router.post("/gen-test", summary="Generate Unit Test")
    async def generate_test(
        file_path: str,
        qualified_name: Optional[str] = None
    ):
        """Generate a PHPUnit test skeleton for a controller file"""
        from .tools.test_generator import TestGenerator
        import os
        
        # Security/Validation: ensure file exists
        if not os.path.exists(file_path):
             raise HTTPException(status_code=404, detail=f"File not found: {file_path}")
             
        try:
            gen = TestGenerator(file_path)
            gen.parse()
            code = gen.generate()
            return {"code": code, "success": True}
        except Exception as e:
            raise HTTPException(status_code=500, detail=str(e))

    # ============ Mock Endpoints (Simulation) ============
    
    @router.post("/webhook-mock", summary="Simulate AI Webhook")
    async def webhook_mock(payload: dict):
        """Mock external AI provider for testing"""
        import asyncio
        await asyncio.sleep(1) # Simulate network latency
        
        code = payload.get("code", "")
        context = payload.get("context", "")
        impact = payload.get("impact", {})
        
        # Simple local analysis to simulate AI understanding
        lines = code.split('\n')
        line_count = len(lines)
        has_sql = any(k in code.upper() for k in ["SELECT", "INSERT", "UPDATE", "DELETE", "FROM", "JOIN"])
        complexity = code.count("if ") + code.count("foreach") + code.count("while") + 1
        
        # Construct a "Natural Language" response
        narrative = f"This function contains **{line_count} lines** of code."
        
        if has_sql:
            narrative += " It interacts directly with the database using SQL queries."
        else:
            narrative += " It appears to handle logic without direct SQL execution."
            
        if complexity > 5:
            narrative += f" The logic is moderately complex (Cyclomatic complexity ~{complexity}), involving multiple control structures."
        else:
            narrative += " The control flow is straightforward and concise."
            
        # Add Impact Analysis Commentary
        impact_narrative = ""
        calls = 0
        if impact:
            risk = impact.get('risk_level', 'Unknown')
            calls = impact.get('direct_calls', 0)
            impact_narrative = (
                f"\n\n**Impact Analysis (via System Intelligence):**\n"
                f"- **Risk Assessment**: {risk}\n"
                f"- **Dependencies**: Calls {calls} other functions directly.\n"
                f"*(This data was pre-calculated by the internal analyzer and sent to the AI)*"
            )

        # Generate Recommendations
        recommendations = []
        if line_count > 30:
            recommendations.append(f"- **Extract Method**: Function is long ({line_count} lines). Consider breaking it down into smaller helper functions.")
        
        if complexity > 8:
            recommendations.append(f"- **Simplify Logic**: High complexity ({complexity}). Reduce nesting levels or use early returns.")
            
        if has_sql:
            recommendations.append("- **Architecture Violation**: Direct SQL detected. Move query logic to a Model class to improve separation of concerns.")
            
        if calls > 4:
            recommendations.append(f"- **Reduce Coupling**: High dependency count ({calls}). Consider grouping related calls or using a Service layer.")

        rec_text = ""
        if recommendations:
            rec_text = "\n\n**Refactoring Recommendations:**\n" + "\n".join(recommendations)
        else:
            rec_text = "\n\n**Refactoring Recommendations:**\n- Code looks healthy. No immediate refactoring needed."
            
        explanation = (
            f"**[Webhook Simulation]**\n\n"
            f"Here is the AI analysis of `{context}`:\n\n"
            f"{narrative}"
            f"{impact_narrative}"
            f"{rec_text}\n\n"
            f"**Key Observations:**\n"
            f"- **Layer**: Controller/Logic\n"
            f"- **Maintainability**: {'Low' if recommendations else 'High'}\n\n"
            f"*(Generated by Local Simulation Engine)*"
        )
        return {"explanation": explanation}
    
    @router.post("/quality/gate", summary="Evaluate Quality Gate")
    async def evaluate_quality_gate(payload: dict):
        """
        Run the Quality Gate checks.
        Payload: { "type": "BUGFIX"|"CR"|"NR", "files": ["path/to/file.php"] }
        """
        change_type = payload.get("type", "BUGFIX").upper()
        files = payload.get("files", [])
        index_name = payload.get("index_name", "default")
        
        report = await quality_service.evaluate_change(change_type, files, index_name)
        return report

    # ============ Pipeline Workflow Endpoints ============
    
    from .services import PipelineService, GitService
    pipeline_service = PipelineService(db_path="pipeline.db")
    git_service = GitService()
    
    @router.post("/pipeline/task", summary="Create new pipeline task")
    async def create_pipeline_task(payload: dict):
        """
        Create a new development task.
        Payload: { "title": str, "type": "BUGFIX"|"CR"|"NR", "description": str, ... }
        """
        task = await pipeline_service.create_task(
            title=payload.get("title", "Untitled Task"),
            type=payload.get("type", "BUGFIX"),
            description=payload.get("description", ""),
            index_name=payload.get("index_name", "etail-admin"),
            target_files=payload.get("target_files", []),
            target_functions=payload.get("target_functions", []),
            assigned_to=payload.get("assigned_to"),
            priority=payload.get("priority", "MEDIUM"),
        )
        return task.to_dict()
    
    @router.get("/pipeline", summary="List pipeline tasks")
    async def list_pipeline_tasks(
        status: str = None,
        type: str = None,
        assigned_to: str = None,
        limit: int = 50
    ):
        """List all pipeline tasks with optional filters"""
        tasks = await pipeline_service.list_tasks(
            status=status, type=type, assigned_to=assigned_to, limit=limit
        )
        return [task.to_dict() for task in tasks]
    
    @router.get("/pipeline/{task_id}", summary="Get pipeline task")
    async def get_pipeline_task(task_id: str):
        """Get a specific task by ID"""
        task = await pipeline_service.get_task(task_id)
        if not task:
            raise HTTPException(status_code=404, detail="Task not found")
        return task.to_dict()
    
    @router.delete("/pipeline/{task_id}", summary="Delete pipeline task")
    async def delete_pipeline_task(task_id: str):
        """Delete a task"""
        deleted = await pipeline_service.delete_task(task_id)
        if not deleted:
            raise HTTPException(status_code=404, detail="Task not found")
        return {"status": "deleted", "task_id": task_id}
    
    @router.post("/pipeline/{task_id}/analyze", summary="Run impact analysis")
    async def analyze_pipeline_task(task_id: str):
        """Stage 2: Run impact analysis on task targets"""
        try:
            impact_report = await pipeline_service.run_impact_analysis(task_id)
            return {"status": "analyzed", "impact_report": impact_report}
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/pipeline/{task_id}/plan", summary="Generate AI plan")
    async def plan_pipeline_task(task_id: str):
        """Stage 3: Generate AI implementation plan"""
        try:
            plan = await pipeline_service.generate_plan(task_id)
            return {"status": "planned", "plan": plan}
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/pipeline/{task_id}/submit-plan", summary="Submit for plan approval")
    async def submit_plan_for_approval(task_id: str):
        """Submit task for plan approval"""
        try:
            task = await pipeline_service.submit_for_plan_approval(task_id)
            return task.to_dict()
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/pipeline/{task_id}/approve-plan", summary="Approve plan")
    async def approve_plan(task_id: str, payload: dict):
        """Stage 4: Approve the implementation plan"""
        try:
            task = await pipeline_service.approve_plan(
                task_id,
                approved_by=payload.get("approved_by", "system"),
                comments=payload.get("comments")
            )
            return task.to_dict()
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/pipeline/{task_id}/reject-plan", summary="Reject plan")
    async def reject_plan(task_id: str, payload: dict):
        """Reject the implementation plan"""
        try:
            task = await pipeline_service.reject_plan(
                task_id,
                rejected_by=payload.get("rejected_by", "system"),
                comments=payload.get("comments", "")
            )
            return task.to_dict()
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/pipeline/{task_id}/start", summary="Start implementation")
    async def start_implementation(task_id: str):
        """Stage 5: Mark task as in progress"""
        try:
            task = await pipeline_service.start_implementation(task_id)
            return task.to_dict()
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/pipeline/{task_id}/complete", summary="Complete implementation")
    async def complete_implementation(task_id: str, payload: dict):
        """Mark implementation as complete"""
        try:
            task = await pipeline_service.complete_implementation(
                task_id,
                changed_files=payload.get("changed_files", [])
            )
            return task.to_dict()
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/pipeline/{task_id}/test", summary="Run tests")
    async def run_tests(task_id: str):
        """Stage 6: Run automated tests"""
        try:
            results = await pipeline_service.run_tests(task_id)
            return {"status": "tested", "results": results}
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/pipeline/{task_id}/commit", summary="Create local commit")
    async def create_commit(task_id: str, payload: dict = None):
        """Stage 7: Create local commit (not pushed)"""
        try:
            task = await pipeline_service.create_commit(
                task_id,
                message=payload.get("message") if payload else None
            )
            return task.to_dict()
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/pipeline/{task_id}/submit-commit", summary="Submit commit for approval")
    async def submit_commit_for_approval(task_id: str):
        """Submit commit for approval before push"""
        try:
            task = await pipeline_service.submit_for_commit_approval(task_id)
            return task.to_dict()
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/pipeline/{task_id}/approve-commit", summary="Approve commit")
    async def approve_commit(task_id: str, payload: dict):
        """Stage 8: Approve commit for push"""
        try:
            task = await pipeline_service.approve_commit(
                task_id,
                approved_by=payload.get("approved_by", "system")
            )
            return task.to_dict()
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/pipeline/{task_id}/push", summary="Push and create PR")
    async def push_and_create_pr(task_id: str):
        """Stage 9: Push branch and create PR"""
        try:
            task = await pipeline_service.push_and_create_pr(task_id)
            return task.to_dict()
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    @router.post("/pipeline/{task_id}/merge", summary="Mark as merged")
    async def mark_merged(task_id: str, payload: dict):
        """Stage 10: Mark PR as merged (after TL merges in GitHub)"""
        try:
            task = await pipeline_service.mark_merged(
                task_id,
                merged_by=payload.get("merged_by", "system")
            )
            return task.to_dict()
        except ValueError as e:
            raise HTTPException(status_code=404, detail=str(e))
    
    # ============ Git Helper Endpoints ============
    
    @router.get("/git/branch", summary="Get current branch")
    async def get_current_branch():
        """Get the current Git branch"""
        branch = await git_service.get_current_branch()
        return {"branch": branch}
    
    @router.get("/git/diff", summary="Get diff")
    async def get_diff(base: str = "main"):
        """Get diff between current branch and base"""
        diff = await git_service.get_diff(base)
        return {"diff": diff}
    
    @router.get("/git/changed-files", summary="Get changed files")
    async def get_changed_files(base: str = "main"):
        """Get list of files changed compared to base"""
        files = await git_service.get_changed_files(base)
        return {"files": files}

    return router
