"""
SDLC Package Builder
--------------------------
Creates the sdlc-tools distribution folder.
Run via package.bat (double-click).
"""

import os
import sys
import shutil
import subprocess
from pathlib import Path
from datetime import datetime


SCRIPT_DIR = Path(__file__).parent.resolve()
PROJECT_ROOT = SCRIPT_DIR.parent


def main():
    print()
    print("  =============================================")
    print("   SDLC Package Builder")
    print("  =============================================")
    print()

    # 1. Ask output path
    default_out = r"D:\sdlc-tools"
    raw = input(f"  Enter output path [{default_out}]: ").strip()
    # Strip BOM: UTF-8 BOM appears as \xef\xbb\xbf when decoded as cp1252/latin-1
    raw = raw.lstrip('\xef\xbb\xbf').replace('\ufeff', '').strip()
    output = Path(raw) if raw else Path(default_out)

    # 2. Ask build mode
    print()
    print("  Build mode:")
    print()
    print("    [1] Lite    (~220 MB) - Model downloaded by installer")
    print("    [2] Full    (~4.5 GB) - Model included offline")
    print()
    mode_input = input("  Choose (1 or 2) [1]: ").strip()
    include_model = mode_input == "2"

    mode_label = "FULL - offline capable" if include_model else "LITE - model downloaded on install"
    print()
    print(f"  Mode:   {mode_label}")
    print(f"  Output: {output}")
    print()

    output.mkdir(parents=True, exist_ok=True)

    steps_done = []
    steps_warn = []

    # ── Step 1: Copy LCA Index ──
    print("  [1/6] Copying LCA index...")
    lca_src = PROJECT_ROOT / ".lca"
    lca_dst = output / "lca-index"

    if (lca_src / "index.json").exists():
        lca_dst.mkdir(exist_ok=True)
        file_count = 0
        for item in lca_src.rglob("*"):
            if item.is_file():
                rel = item.relative_to(lca_src)
                target = lca_dst / rel
                target.parent.mkdir(parents=True, exist_ok=True)
                shutil.copy2(item, target)
                file_count += 1
        size_mb = sum(f.stat().st_size for f in lca_dst.rglob("*") if f.is_file()) / (1024 * 1024)
        print(f"         [DONE] {file_count} files, {size_mb:.1f} MB")
        steps_done.append("LCA index")
    else:
        print("         [WARN] No .lca/ found. Run: lca index build admin")
        steps_warn.append("LCA index missing")

    # ── Step 2: Copy ChromaDB ──
    print("  [2/6] Copying ChromaDB store...")
    chroma_src = PROJECT_ROOT / "chroma_store"
    chroma_dst = output / "chroma-store"

    if (chroma_src / "chroma.sqlite3").exists():
        chroma_dst.mkdir(exist_ok=True)
        for item in chroma_src.rglob("*"):
            if item.is_file():
                rel = item.relative_to(chroma_src)
                target = chroma_dst / rel
                target.parent.mkdir(parents=True, exist_ok=True)
                shutil.copy2(item, target)
        print("         [DONE]")
        steps_done.append("ChromaDB")
    else:
        print("         [WARN] No chroma_store/ found")
        steps_warn.append("ChromaDB missing")

    # ── Step 3: Copy BGE-M3 Model (conditional) ──
    if include_model:
        print("  [3/6] Copying BGE-M3 model (4.3 GB - takes a while)...")
        model_src = Path.home() / ".cache" / "huggingface" / "hub" / "models--BAAI--bge-m3"
        model_dst = output / "bge-m3"

        if (model_src / "snapshots").exists():
            model_dst.mkdir(exist_ok=True)
            file_count = 0
            all_files = [f for f in model_src.rglob("*") if f.is_file()]
            total = len(all_files)

            for i, item in enumerate(all_files, 1):
                rel = item.relative_to(model_src)
                target = model_dst / rel
                target.parent.mkdir(parents=True, exist_ok=True)
                shutil.copy2(item, target)
                file_count += 1
                if i % 10 == 0 or i == total:
                    print(f"\r         Copying... {i}/{total} files", end="", flush=True)

            print()
            print(f"         [DONE] {file_count} files")
            steps_done.append("BGE-M3 model")
        else:
            print(f"         [WARN] Model not found at: {model_src}")
            print("         Installer will download it instead.")
            steps_warn.append("BGE-M3 model not found locally")
    else:
        print("  [3/6] Skipping model (Lite mode - will download on install)")
        # Remove old model if present from a previous Full build
        old_model = output / "bge-m3"
        if old_model.exists():
            print("         Removing old model copy...")
            shutil.rmtree(old_model, ignore_errors=True)
        print("         [DONE]")

    # ── Step 4: Copy Knowledge Brain ──
    print("  [4/6] Copying Knowledge Brain...")
    brain_src = PROJECT_ROOT / "knowledge_brain"
    brain_dst = output / "knowledge-brain"

    if brain_src.exists() and any(brain_src.rglob("*.md")):
        brain_dst.mkdir(exist_ok=True)
        file_count = 0
        for item in brain_src.rglob("*"):
            if item.is_file():
                rel = item.relative_to(brain_src)
                target = brain_dst / rel
                target.parent.mkdir(parents=True, exist_ok=True)
                shutil.copy2(item, target)
                file_count += 1
        size_kb = sum(f.stat().st_size for f in brain_dst.rglob("*") if f.is_file()) / 1024
        print(f"         [DONE] {file_count} files, {size_kb:.0f} KB")
        steps_done.append("Knowledge Brain")
    else:
        print("         [WARN] No knowledge_brain/ found (optional - discovery still works)")
        steps_warn.append("Knowledge Brain missing (optional)")

    # ── Step 5: Build HTML docs ──
    print("  [5/6] Building HTML documentation...")
    build_docs = SCRIPT_DIR / "engine" / "build_docs.py"
    try:
        subprocess.run(
            [sys.executable, str(build_docs)],
            capture_output=True, text=True, timeout=30,
            cwd=str(PROJECT_ROOT),
        )
        if (SCRIPT_DIR / "docs" / "INSTALLATION.html").exists():
            print("         [DONE]")
            steps_done.append("HTML docs")
        else:
            print("         [WARN] HTML docs not built - markdown files still available")
            steps_warn.append("HTML docs not built")
    except Exception as e:
        print(f"         [WARN] {e}")
        steps_warn.append("HTML docs build error")

    # ── Step 6: Copy install script + docs ──
    print("  [6/6] Creating install script and docs...")

    # Install files
    shutil.copy2(SCRIPT_DIR / "install-package.bat", output / "install.bat")
    shutil.copy2(SCRIPT_DIR / "install-package.py", output / "install.py")

    # Docs
    docs_dst = output / "docs"
    docs_dst.mkdir(exist_ok=True)
    docs_src = SCRIPT_DIR / "docs"
    if docs_src.exists():
        for item in docs_src.rglob("*"):
            if item.is_file():
                rel = item.relative_to(docs_src)
                target = docs_dst / rel
                target.parent.mkdir(parents=True, exist_ok=True)
                shutil.copy2(item, target)

    readme_src = SCRIPT_DIR / "README.md"
    if readme_src.exists():
        shutil.copy2(readme_src, docs_dst / "README.md")

    print("         [DONE]")
    steps_done.append("Install script + docs")

    # ── Create README.txt ──
    now = datetime.now().strftime("%Y-%m-%d %H:%M")
    model_line = "  - bge-m3/       AI embedding model  ~4.3 GB\n" if include_model else ""
    internet_line = "  - Internet connection (to download AI model)\n" if not include_model else ""

    readme_content = f"""=============================================
  SDLC Pipeline Tools - Setup Package
=============================================

  Type:    {mode_label}
  Created: {now}

  INSTRUCTIONS:
  1. Download this entire folder
  2. Double-click "install.bat"
  3. Follow the on-screen instructions
  4. Done!

  CONTENTS:
  - lca-index/       Code analysis index  ~210 MB
  - chroma-store/    Semantic search DB   ~0.2 MB
  - knowledge-brain/ Module brain docs    ~0.5 MB
  - docs/            Documentation
  - install.bat      Setup launcher
  - install.py       Setup script
{model_line}
  PRE-REQUISITES:
  - Python 3.10+  https://python.org
  - Git           https://git-scm.com
  - Project repo already cloned
{internet_line}
  QUESTIONS? Ask your team lead.
=============================================
"""
    (output / "README.txt").write_text(readme_content, encoding="utf-8")

    # ── Summary ──
    total_files = sum(1 for _ in output.rglob("*") if _.is_file())
    total_size = sum(f.stat().st_size for f in output.rglob("*") if f.is_file())
    size_mb = total_size / (1024 * 1024)

    print()
    print("  =============================================")
    print(f"   Package Ready! ({mode_label})")
    print("  =============================================")
    print()
    print(f"   Location: {output}")
    print(f"   Files:    {total_files}")
    print(f"   Size:     {size_mb:.1f} MB")
    print()

    if steps_warn:
        print("   Warnings:")
        for w in steps_warn:
            print(f"     - {w}")
        print()

    print("   Next steps:")
    if include_model:
        print("   1. Zip this folder (~4.5 GB)")
    else:
        print("   1. Zip this folder (~220 MB - quick!)")
    print("   2. Upload to Google Drive")
    print("   3. Share the link with your team")
    if not include_model:
        print("   4. Users need internet for model download")
    print()


if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n  Cancelled.")
    except Exception as e:
        print(f"\n  Error: {e}")
        import traceback
        traceback.print_exc()
