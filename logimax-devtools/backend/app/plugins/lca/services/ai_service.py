import os
import openai
import uuid
import json
import httpx
import re
from typing import Optional
from .task_storage import TaskStorage
from dotenv import load_dotenv

load_dotenv()

class AIService:
    def __init__(self):
        self.api_key = os.getenv("OPENAI_API_KEY")
        self.webhook_url = os.getenv("LCA_AI_WEBHOOK_URL")
        self.mock_mode = not self.api_key
        self.storage = TaskStorage("tasks.db")

    async def explain_function(self, code: str, context: str = "", impact_data: Optional[dict] = None) -> str:
        """
        Explain the given code using Chain of Responsibility:
        1. OpenAI API (if configured)
        2. Webhook (if configured)
        3. Heuristic Analysis (Fallback)
        """
        # 1. Try OpenAI
        if self.api_key:
            try:
                # Note: Not sending impact data to OpenAI yet, keeping it simple
                return await self._call_openai(code, context)
            except Exception as e:
                print(f"[AI] OpenAI failed: {e}")

        # 2. Try Webhook
        if self.webhook_url:
            try:
                return await self._call_webhook(code, context, impact_data)
            except Exception as e:
                print(f"[AI] Webhook failed: {e}")

        # 3. Fallback to Heuristic
        return self._heuristic_explanation(code, context)

    async def _call_openai(self, code: str, context: str) -> str:
        client = openai.AsyncOpenAI(api_key=self.api_key)
        response = await client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=[
                {"role": "system", "content": "You are an expert software engineer. Explain this code concisely."},
                {"role": "user", "content": f"Code:\n{code}\n\nContext:\n{context}"}
            ],
            max_tokens=300
        )
        return response.choices[0].message.content

    async def _call_webhook(self, code: str, context: str, impact_data: Optional[dict] = None) -> str:
        async with httpx.AsyncClient() as client:
            payload = {
                "code": code,
                "context": context,
                "task": "explain"
            }
            if impact_data:
                payload["impact"] = impact_data
                
            response = await client.post(
                self.webhook_url,
                json=payload,
                timeout=30.0
            )
            response.raise_for_status()
            # Expecting direct string response or {"explanation": "..."}
            try:
                data = response.json()
                return data.get("explanation", data.get("content", str(data)))
            except:
                return response.text

    def _heuristic_explanation(self, code: str, context: str) -> str:
        """
        Analyze code statically to provide a useful description without AI.
        """
        lines = code.split('\n')
        line_count = len(lines)
        
        # SQL Detection
        sql_keywords = ["SELECT", "INSERT", "UPDATE", "DELETE", "FROM", "JOIN", "WHERE"]
        has_sql = any(k in code.upper() for k in sql_keywords)
        
        # Model/View Detection pattern for CodeIgniter/MVC
        has_model_load = "load->model" in code
        has_view_load = "load->view" in code
        
        # Complexity (basic)
        conditions = code.count("if ") + code.count("foreach") + code.count("while")
        
        summary = [
            f"**Statical Analysis (No AI)**",
            f"- **Size**: {line_count} lines of code.",
            f"- **Complexity**: Cyclomatic approximation of ~{conditions + 1}."
        ]
        
        if has_sql:
            summary.append("- **Database**: Performs SQL operations (Direct Query detected).")
        
        if has_model_load:
            summary.append("- **Dependencies**: Loads other Models.")
            
        if has_view_load:
            summary.append("- **UI**: Renders a View template.")
            
        summary.append("\n**Code Structure:**")
        summary.append("This function appears to be part of the controller/logic layer.")
        if line_count > 50:
            summary.append("It is relatively large and might benefit from refactoring.")
        else:
            summary.append("It is a concise function.")

        return "\n".join(summary)

    async def _create_pending_task(self, code: str, context: str) -> str:
        """Legacy pending task creation (Deprocated/Last Resort)"""
        # Kept for backward compatibility if we ever decide to re-enable manual tasks
        return "Task Queued."

