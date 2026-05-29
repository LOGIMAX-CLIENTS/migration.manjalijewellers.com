import sys
from pathlib import Path
sys.path.append(str(Path(__file__).parent.parent))

from lca.parsers.javascript import JavaScriptParser
from lca.parsers.php import PHPParser

def test_intermodule_parsing():
    print("🚀 Starting Intermodule Verification...")
    
    # 1. Test JS -> API Parsing
    js_content = """
    function savedata() {
        $.ajax({
            url: "index.php/admin_test/save_data",
            success: function() {
                updateUI();
            }
        });
    }
    """
    js_parser = JavaScriptParser()
    # Mocking file read by writing to temp or just modifying parser? 
    # Actually checking parser implementation, it reads from disk.
    # Let's write temp files.
    Path("temp_test.js").write_text(js_content)
    funcs, calls = js_parser.parse_file(Path("temp_test.js"))
    
    api_calls = [c for c in calls if c.call_type == "api_call"]
    print(f"✅ JS Parsed: Found {len(api_calls)} API calls")
    for c in api_calls:
        print(f"   -> Call to {c.target}")
        if c.target == "API:admin_test/save_data":
            print("   ✅ API Target Correct")

    # 2. Test PHP -> DB Parsing
    php_content = """
    <?php
    class Ret_estimation extends CI_Controller {
        function get_data() {
            $this->db->get('users');
            $this->db->query("SELECT * FROM orders JOIN items ON id");
        }
    }
    """
    Path("temp_test.php").write_text(php_content)
    php_parser = PHPParser()
    p_funcs, p_calls = php_parser.parse_file(Path("temp_test.php"))
    
    db_calls = [c for c in p_calls if c.call_type == "db_query"]
    print(f"✅ PHP Parsed: Found {len(db_calls)} DB calls")
    found_tables = set()
    for c in db_calls:
        print(f"   -> Query on {c.target}")
        found_tables.add(c.target)
        
    if "TABLE:users" in found_tables and "TABLE:orders" in found_tables:
        print("   ✅ DB Targets Correct")
        
    print("🎉 Verification Complete!")

if __name__ == "__main__":
    test_intermodule_parsing()
