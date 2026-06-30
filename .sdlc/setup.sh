#!/bin/bash
# SDLC Pipeline Engine — Setup Script (Linux/macOS)
# Usage:
#   ./setup.sh
#   ./setup.sh --skip-rag
#   ./setup.sh --model-source /mnt/nas/shared/sdlc-tools/bge-m3

set -e

REQUIRED_PYTHON="3.10"
SKIP_RAG=false
MODEL_SOURCE=""

# Parse args
for arg in "$@"; do
    case $arg in
        --skip-rag) SKIP_RAG=true ;;
        --model-source=*) MODEL_SOURCE="${arg#*=}" ;;
        --help)
            echo "Usage: ./setup.sh [options]"
            echo ""
            echo "Options:"
            echo "  --skip-rag              Skip RAG/embedding setup (LCA-only mode)"
            echo "  --model-source=<path>   Path to pre-downloaded BGE-M3 model"
            echo "  --help                  Show this help"
            exit 0
            ;;
    esac
done

step() { echo -e "\n\033[36m[$1] $2\033[0m"; }
ok()   { echo -e "  \033[32m[OK]\033[0m $1"; }
warn() { echo -e "  \033[33m[WARN]\033[0m $1"; }
fail() { echo -e "  \033[31m[FAIL]\033[0m $1"; }

echo ""
echo "============================================"
echo "  SDLC Pipeline Engine — Setup v3.8.1"
echo "============================================"
echo ""

# ── Step 1: Check Python ──
step "1/7" "Checking Python..."

PYTHON_CMD=""
for cmd in python3 python; do
    if command -v $cmd &>/dev/null; then
        ver=$($cmd --version 2>&1 | grep -oP '\d+\.\d+')
        if [ "$(printf '%s\n' "$REQUIRED_PYTHON" "$ver" | sort -V | head -1)" = "$REQUIRED_PYTHON" ]; then
            PYTHON_CMD=$cmd
            ok "$($cmd --version 2>&1)"
            break
        fi
    fi
done
if [ -z "$PYTHON_CMD" ]; then
    fail "Python $REQUIRED_PYTHON+ required"
    exit 1
fi

# ── Step 2: Check Git ──
step "2/7" "Checking Git..."
if command -v git &>/dev/null; then
    ok "$(git --version)"
else
    fail "Git not found"
    exit 1
fi

# ── Step 3: Install LCA ──
step "3/7" "Installing LCA..."

LCA_PATH="logimax-devtools/backend/lca_core"
if [ -d "$LCA_PATH" ]; then
    $PYTHON_CMD -m pip install -e "$LCA_PATH" --quiet 2>/dev/null
    if command -v lca &>/dev/null; then
        ok "LCA installed: $(lca --version 2>&1)"
    else
        warn "LCA installed but not in PATH. Use: $PYTHON_CMD -m lca"
    fi
else
    warn "LCA source not found at $LCA_PATH"
fi

# ── Step 4: LCA Index ──
step "4/7" "Setting up LCA index..."

if [ -f ".lca/reports.json" ]; then
    size=$(du -sh .lca/ 2>/dev/null | cut -f1)
    ok "LCA index exists ($size)"
else
    echo "  Building LCA index (1-5 minutes)..."
    lca init . 2>/dev/null && lca index . 2>/dev/null && ok "Index built" || warn "Build manually: lca init . && lca index ."
fi

# ── Step 5: RAG Dependencies ──
if [ "$SKIP_RAG" = false ]; then
    step "5/7" "Installing RAG dependencies..."
    $PYTHON_CMD -m pip install chromadb sentence-transformers --quiet 2>/dev/null
    $PYTHON_CMD -c "import chromadb, sentence_transformers; print('ok')" 2>/dev/null && ok "chromadb + sentence-transformers" || warn "RAG deps failed (optional)"
else
    step "5/7" "Skipping RAG (--skip-rag)"
fi

# ── Step 6: BGE-M3 Model ──
if [ "$SKIP_RAG" = false ]; then
    step "6/7" "Setting up BGE-M3 model..."
    MODEL_DEST="$HOME/.cache/huggingface/hub/models--BAAI--bge-m3"

    if [ -d "$MODEL_DEST/snapshots" ]; then
        size=$(du -sh "$MODEL_DEST" 2>/dev/null | cut -f1)
        ok "BGE-M3 model exists ($size)"
    elif [ -n "$MODEL_SOURCE" ] && [ -d "$MODEL_SOURCE" ]; then
        echo "  Copying model from $MODEL_SOURCE..."
        mkdir -p "$MODEL_DEST"
        cp -r "$MODEL_SOURCE"/* "$MODEL_DEST/"
        ok "Model copied"
    else
        warn "No model found. Will auto-download on first use (~4.3 GB)"
        echo "  Or: ./setup.sh --model-source=/mnt/nas/shared/bge-m3"
    fi
else
    step "6/7" "Skipping model (RAG disabled)"
fi

# ── Step 7: Verify Pipeline ──
step "7/7" "Verifying SDLC pipeline..."

if [ -f ".sdlc/engine/cli.py" ]; then
    $PYTHON_CMD .sdlc/engine/cli.py banner 2>/dev/null && ok "Pipeline OK" || warn "Pipeline exists but failed"
else
    fail "Pipeline not found"
    exit 1
fi

echo ""
echo "============================================"
echo "  Setup Complete!"
echo "============================================"
echo ""
echo "  Quick start:"
echo "    $PYTHON_CMD .sdlc/engine/cli.py banner"
echo "    $PYTHON_CMD .sdlc/engine/cli.py start -t fix -m reports -s 'Test'"
echo ""
echo "  TIP: alias sdlc='$PYTHON_CMD .sdlc/engine/cli.py'"
echo ""
