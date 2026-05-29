import urllib.request
import urllib.error
import json

url = "http://localhost:8800/api/lca/function/test-core/SQLiteStorage._init_db"

print(f"Requesting {url}...")
try:
    with urllib.request.urlopen(url) as response:
        print(f"Status Code: {response.status}")
        print("Headers:", response.headers)
        body = response.read().decode('utf-8')
        try:
            print("Body:", json.dumps(json.loads(body), indent=2))
        except:
            print("Body (Text):", body)
except urllib.error.HTTPError as e:
    print(f"Status Code: {e.code}")
    print("Headers:", e.headers)
    body = e.read().decode('utf-8')
    try:
        print("Body:", json.dumps(json.loads(body), indent=2))
    except:
        print("Body (Text):", body)
except Exception as e:
    print(f"Error: {e}")
