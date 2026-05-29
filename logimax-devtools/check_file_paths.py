import sqlite3
import os

def check_paths():
    db_path = 'backend/lca_index.db'
    if not os.path.exists(db_path):
        print(f"DB not found at {db_path}")
        return

    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()
    cursor.execute("SELECT file FROM functions LIMIT 5")
    rows = cursor.fetchall()
    
    print("Sample File Paths in DB:")
    for row in rows:
        print(row[0])

if __name__ == "__main__":
    check_paths()
