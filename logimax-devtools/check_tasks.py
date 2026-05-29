import sqlite3
import json

def check_tasks():
    conn = sqlite3.connect('backend/tasks.db')
    conn.row_factory = sqlite3.Row
    cursor = conn.cursor()
    cursor.execute("SELECT id, target_function, status, intent FROM tasks WHERE status = 'pending_explanation'")
    rows = cursor.fetchall()
    
    print(f"Found {len(rows)} pending tasks.")
    for row in rows:
        print(f"ID: {row['id']}")
        print(f"Target: {row['target_function']}")
        try:
            payload = json.loads(row['intent'])
            print(f"Code Snippet: {payload.get('code', '')[:200]}...") # Print first 200 chars
        except:
            print(f"Intent Raw: {row['intent']}")
        print("-" * 20)

if __name__ == "__main__":
    check_tasks()
