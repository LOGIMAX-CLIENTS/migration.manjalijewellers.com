import asyncio
import os
from app.plugins.lca.services.index_service import IndexService
from app.plugins.lca.core.storage import SQLiteStorage

async def run_indexer():
    print("Starting manual indexer...")
    storage = SQLiteStorage("lca_index.db") # Use the real DB file
    # await storage.init_db() - Called internally
    
    service = IndexService(storage)
    
    # Path to index
    target_path = "c:\\xampp\\htdocs\\etail_v3\\admin"
    if not os.path.exists(target_path):
        print(f"ERROR: Path does not exist: {target_path}")
        # Try finding where we are
        print(f"Current CWD: {os.getcwd()}")
        return

    print(f"Indexing path: {target_path}")
    
    try:
        index = await service.index_directory(
            path=target_path,
            name="etail-admin-manual",
            on_progress=lambda p: print(f"Progress: {p.processed_files}/{p.total_files} - {p.current_file} (Errors: {len(p.errors)})")
        )
        print(f"Indexing complete! Functions: {len(index.functions)}, Calls: {len(index.calls)}")
    except Exception as e:
        print(f"Indexing failed: {e}")
        import traceback
        traceback.print_exc()

if __name__ == "__main__":
    asyncio.run(run_indexer())
