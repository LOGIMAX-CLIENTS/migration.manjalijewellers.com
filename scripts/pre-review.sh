#!/bin/sh

# Pre-review/Pre-commit Hook
# Prevents committing debug code, syntax errors, and common security issues.

FILES=$(git diff --cached --name-only --diff-filter=ACM)
FORBIDDEN_PHP="print_r|var_dump|printf|die\("
FORBIDDEN_JS="console\.log|console\.dir|alert\("
CONFLICT_MARKERS="<<<<<<<|=======|>>>>>>>"

EXIT_CODE=0

echo "🔍 Running Pre-review checks..."

for FILE in $FILES; do
    # Skip deleted files
    if [ ! -f "$FILE" ]; then
        continue
    fi

    # 1. Check for Merge Conflicts (All files)
    if grep -Eqn "$CONFLICT_MARKERS" "$FILE"; then
        echo "❌ ERROR: Merge conflict markers found in $FILE"
        EXIT_CODE=1
    fi

    # Check PHP files
    if echo "$FILE" | grep -q "\.php$"; then
        # 2. PHP Syntax Check (Lint)
        if ! php -l "$FILE" > /dev/null 2>&1; then
            echo "❌ ERROR: PHP Syntax Error in $FILE"
            php -l "$FILE"
            EXIT_CODE=1
        fi

        # 3. Forbidden Debug Code
        if grep -Eqn "$FORBIDDEN_PHP" "$FILE"; then
            echo "❌ ERROR: Forbidden debug code found in $FILE:"
            grep -EZn "$FORBIDDEN_PHP" "$FILE"
            EXIT_CODE=1
        fi

        # 4. Basic SQL Injection Warning (Variables in double-quoted query strings)
        # Looks for: $this->db->query("... $var ...") pattern
        if grep -n '\->query[[:space:]]*(' "$FILE" | grep -q '"[^"]*\$[a-zA-Z0-9_]\+'; then
             echo "⚠️  WARNING: Potential SQL Injection detected in $FILE (Variable in query string). Use Binding."
             grep -n '\->query[[:space:]]*(' "$FILE" | grep '"[^"]*\$[a-zA-Z0-9_]\+'
             # We warn but don't block for heuristic SQLi to avoid false positives blocking work
        fi
    fi

    # Check JS files
    if echo "$FILE" | grep -q "\.[jt]sx?$"; then
        if grep -Eqn "$FORBIDDEN_JS" "$FILE"; then
            echo "❌ ERROR: Forbidden debug code found in $FILE:"
            grep -EZn "$FORBIDDEN_JS" "$FILE"
            EXIT_CODE=1
        fi
    fi
done

if [ $EXIT_CODE -ne 0 ]; then
    echo ""
    echo "🚫 Commit blocked. Please fix the errors above."
    exit 1
fi

echo "✅ Pre-review checks passed."
exit 0
