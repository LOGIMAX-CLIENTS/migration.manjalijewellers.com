"""
SDLC Pipeline Installer v5.0
-----------------------------
Run via install.bat (double-click).
Idempotent: re-run to resume if failed.
"""

import os
import sys
import shutil
import subprocess
import time
import logging
from pathlib import Path

# ── Constants ──
VERSION = "5.0"
PACKAGE_DIR = Path(__file__).parent.resolve()
LOG_FILE = PACKAGE_DIR / "install.log"
STEPS_TOTAL = 5

# ── Setup logging ──
logging.basicConfig(
    level=logging.DEBUG,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[
        logging.FileHandler(LOG_FILE, encoding="utf-8"),
    ],
)
log = logging.getLogger("installer")


# ── UI Helpers ──
def header(text):
    w = 50
    print(f"\n  +{'=' * w}+")
    print(f"  :  {text:<{w - 3}}:")
    print(f"  +{'=' * w}+\n")


def step_banner(step_num, title, pct):
    bar_len = 30
    filled = int(bar_len * pct / 100)
    bar = "#" * filled + "." * (bar_len - filled)
    print(f"\n  +{'─' * 48}+")
    print(f"  │  Step {step_num} of {STEPS_TOTAL} — {title:<30}│")
    print(f"  │  [{bar}] {pct:>3}%{' ' * 8}│")
    print(f"  +{'─' * 48}+\n")


def ok(msg):
    print(f"    [OK]   {msg}")


def skip(msg):
    print(f"    [SKIP] {msg}")


def fail(msg):
    print(f"    [FAIL] {msg}")


def warn(msg):
    print(f"    [WARN] {msg}")


def info(msg):
    print(f"    {msg}")


def ask(prompt, default="Y"):
    """Ask a Y/N question."""
    try:
        ans = input(f"    {prompt} ").strip().upper()
        return ans if ans else default
    except (EOFError, KeyboardInterrupt):
        return default


def ask_choice(prompt, max_val):
    """Ask for a numeric choice."""
    try:
        ans = input(f"    {prompt} ").strip()
        val = int(ans)
        if 1 <= val <= max_val:
            return val
    except (ValueError, EOFError, KeyboardInterrupt):
        pass
    return None


def ask_text(prompt):
    """Ask for free text input."""
    try:
        return input(f"    {prompt} ").strip()
    except (EOFError, KeyboardInterrupt):
        return ""


# ── Step Results Tracker ──
class Results:
    def __init__(self):
        self.steps = {}

    def set(self, step, status, detail=""):
        self.steps[step] = {"status": status, "detail": detail}

    def print_summary(self, project_path):
        labels = {
            1: "LCA Code Analyzer",
            2: "Code Index",
            3: "Search Database",
            4: "AI Model (4.3 GB)",
            5: "Pipeline Verify",
        }
        print("\n  " + "=" * 50)
        print("\n   SETUP COMPLETE — Installation Summary\n")
        print("  " + "─" * 50)
        print(f"   {'Component':<24}{'Status':<10}{'Detail'}")
        print("  " + "─" * 50)

        any_failed = False
        for i in range(1, STEPS_TOTAL + 1):
            s = self.steps.get(i, {"status": "PENDING", "detail": ""})
            icon = {"DONE": "[OK]  ", "SKIPPED": "[SKIP]", "FAILED": "[FAIL]", "WARN": "[WARN]"}.get(
                s["status"], "[----]"
            )
            print(f"   {icon} {labels.get(i, f'Step {i}'):<22}{s['detail']}")
            if s["status"] == "FAILED":
                any_failed = True

        print("  " + "─" * 50)

        if any_failed:
            print("\n   Some steps failed (marked [FAIL] above).")
            print("   Run this installer again — it will skip")
            print("   completed steps and retry only the failed ones.\n")
        else:
            print("\n   Everything installed successfully!\n")

        print("  " + "─" * 50)
        print("   YOUR PROJECT")
        print("  " + "─" * 50)
        print(f"\n   Path: {project_path}\n")
        print("   To verify, open a terminal and run:\n")
        print(f"     cd {project_path}")
        print("     python .sdlc\\engine\\cli.py banner\n")
        print("  " + "─" * 50)
        print("   WHAT'S NEXT?")
        print("  " + "─" * 50)
        print("\n   The AI coding assistant will automatically use")
        print("   these tools. No manual setup needed.\n")
        print("   Documentation:")
        print(f"     Quick Start:  {project_path}\\.sdlc\\docs\\QUICK_START.md")
        print(f"     Full Manual:  {project_path}\\.sdlc\\docs\\USER_MANUAL.md\n")
        print("  " + "=" * 50)


# ── Pre-requisite Checks ──
def check_prerequisites():
    """Check all prerequisites. Returns (passed: bool, project_path: Path)."""
    print("  " + "─" * 50)
    print("   CHECKING REQUIREMENTS")
    print("  " + "─" * 50 + "\n")

    all_ok = True

    # 1. Python
    print("  [1/4] Checking Python...")
    try:
        result = subprocess.run(
            [sys.executable, "--version"], capture_output=True, text=True, timeout=10
        )
        ver = result.stdout.strip()
        ok(f"Python {ver.replace('Python ', '')}")
        log.info(f"Python: {ver}")
    except Exception as e:
        fail(f"Python check failed: {e}")
        all_ok = False

    # 2. Git
    print("\n  [2/4] Checking Git...")
    try:
        result = subprocess.run(["git", "--version"], capture_output=True, text=True, timeout=10)
        ver = result.stdout.strip()
        ok(ver)
        log.info(f"Git: {ver}")
    except FileNotFoundError:
        fail("Git not found")
        info("")
        info("To fix:")
        info("  1. Go to https://git-scm.com/download/win")
        info("  2. Install with all default options")
        info("  3. Restart your computer")
        info("  4. Run this installer again")
        all_ok = False
    except Exception as e:
        fail(f"Git check failed: {e}")
        all_ok = False

    # 3. Package contents
    print("\n  [3/4] Checking package contents...")
    lca_path = PACKAGE_DIR / "lca-index"
    bge_path = PACKAGE_DIR / "bge-m3" / "snapshots"
    model_cached = (Path.home() / ".cache" / "huggingface" / "hub" / "models--BAAI--bge-m3" / "snapshots").exists()

    if not lca_path.exists():
        fail("lca-index folder missing")
        all_ok = False
    elif bge_path.exists():
        ok("All package files present (includes offline model)")
    elif model_cached:
        ok("Package files present (model already cached on this machine)")
    else:
        ok("Package files present (model will be downloaded from HuggingFace)")

    # 4. Project path
    print("\n  [4/4] Finding your project...")
    project_path = find_project()

    if project_path is None:
        all_ok = False
    else:
        # Verify LCA source in project
        lca_source = project_path / "logimax-devtools" / "backend" / "lca_core" / "pyproject.toml"
        if not lca_source.exists():
            print()
            fail("LCA source code not found in project")
            info("Your repo clone may be incomplete.")
            info("Run: git pull  in your project folder.")
            all_ok = False

    return all_ok, project_path


def find_project():
    """Auto-detect or ask for project path."""
    # Scan common locations
    scan_dirs = [
        Path(r"C:\xampp\htdocs"),
        Path(r"D:\xampp\htdocs"),
        Path(r"E:\xampp\htdocs"),
    ]

    found = []
    for scan_dir in scan_dirs:
        if scan_dir.exists():
            for child in scan_dir.iterdir():
                if child.is_dir():
                    sdlc_marker = child / ".sdlc" / "engine" / "cli.py"
                    if sdlc_marker.exists():
                        found.append(child)

    if not found:
        return ask_manual_path()

    if len(found) == 1:
        print()
        info(f"Found your project at:\n")
        info(f"  {found[0]}\n")
        answer = ask("Is this correct? (Y/N):", "Y")
        if answer == "N":
            return ask_manual_path()
        ok("Project confirmed")
        log.info(f"Project: {found[0]}")
        return found[0]

    # Multiple projects
    print()
    info(f"Found {len(found)} projects:\n")
    for i, p in enumerate(found, 1):
        info(f"  [{i}] {p}")
    print()

    pick = ask_choice(f"Enter number (1-{len(found)}):", len(found))
    if pick is None:
        return ask_manual_path()

    selected = found[pick - 1]
    print()
    info(f"Selected: {selected}\n")
    answer = ask("Is this correct? (Y/N):", "Y")
    if answer == "N":
        return ask_manual_path()

    ok("Project confirmed")
    log.info(f"Project: {selected}")
    return selected


def ask_manual_path():
    """Ask user for project path manually."""
    print()
    info("Could not auto-detect your project.\n")
    info("Please enter the full path to your project folder.\n")
    info("  Example: C:\\xampp\\htdocs\\etail_v3")
    info("  Example: D:\\xampp\\htdocs\\retail_v5\n")

    raw = ask_text("Path:")
    if not raw:
        fail("No path entered")
        return None

    project_path = Path(raw.strip().rstrip("\\"))

    if not project_path.exists():
        fail(f"Folder does not exist: {project_path}")
        return None

    sdlc_marker = project_path / ".sdlc" / "engine" / "cli.py"
    if not sdlc_marker.exists():
        fail("That folder doesn't have the SDLC pipeline")
        info("Make sure you've cloned the project repo first")
        info("and it contains the .sdlc folder.")
        return None

    ok("Project confirmed")
    log.info(f"Project (manual): {project_path}")
    return project_path


# ── Installation Steps ──
def step1_install_lca(project_path, results):
    """Install LCA + dependencies via pip."""
    step_banner(1, "Installing Code Analyzer", 20)

    # Check if already installed
    try:
        result = subprocess.run(["lca", "--version"], capture_output=True, text=True, timeout=10)
        if result.returncode == 0:
            info(f"Already installed: {result.stdout.strip()}")
            info("Checking for updates...")
    except FileNotFoundError:
        pass

    info("Installing LCA with AI search support...")
    info("This downloads Python packages.")
    info("Please wait, do NOT close this window.")
    info("Estimated time: 1-3 minutes")
    info("")
    info("(Output logged to install.log)")
    print()

    lca_path = project_path / "logimax-devtools" / "backend" / "lca_core"

    log.info(f"Step 1: pip install -e {lca_path}[semantic]")
    start = time.time()

    try:
        result = subprocess.run(
            [sys.executable, "-m", "pip", "install", "-e", f"{lca_path}[semantic]"],
            capture_output=True,
            text=True,
            timeout=300,
        )
        log.info(f"pip stdout:\n{result.stdout}")
        if result.stderr:
            log.info(f"pip stderr:\n{result.stderr}")

        if result.returncode != 0:
            warn("Full install had issues. Trying basic mode...")
            log.warning("Semantic install failed, trying basic")

            result = subprocess.run(
                [sys.executable, "-m", "pip", "install", "-e", str(lca_path)],
                capture_output=True,
                text=True,
                timeout=300,
            )
            log.info(f"pip basic stdout:\n{result.stdout}")

            if result.returncode != 0:
                fail("LCA installation failed")
                info("See install.log for details")
                results.set(1, "FAILED", "pip install failed — see install.log")
                return
            else:
                detail = "basic mode"
        else:
            detail = "with AI search"
    except subprocess.TimeoutExpired:
        fail("Installation timed out (5 min)")
        results.set(1, "FAILED", "timeout")
        return
    except Exception as e:
        fail(f"Installation error: {e}")
        log.error(f"Step 1 error: {e}", exc_info=True)
        results.set(1, "FAILED", str(e))
        return

    elapsed = time.time() - start
    log.info(f"Step 1 complete in {elapsed:.1f}s")

    # ── Version Guard: sentence_transformers >= 5.0 breaks BGE-M3 ──
    # sentence_transformers 5.x uses AutoProcessor which doesn't work with BGE-M3.
    # If pip resolved to 5.x, force downgrade to 4.x.
    try:
        ver_result = subprocess.run(
            [sys.executable, "-c", "import sentence_transformers; print(sentence_transformers.__version__)"],
            capture_output=True, text=True, timeout=10,
        )
        if ver_result.returncode == 0:
            st_ver = ver_result.stdout.strip()
            major = int(st_ver.split(".")[0])
            if major >= 5:
                warn(f"sentence-transformers {st_ver} is incompatible with BGE-M3")
                info("Auto-fixing: downgrading to 4.x...")
                fix_result = subprocess.run(
                    [sys.executable, "-m", "pip", "install", "sentence-transformers>=2.2.0,<5.0", "transformers>=4.40.0,<5.0"],
                    capture_output=True, text=True, timeout=120,
                )
                log.info(f"Version fix stdout:\n{fix_result.stdout}")
                if fix_result.returncode == 0:
                    ok("Fixed: sentence-transformers downgraded to 4.x")
                else:
                    warn("Auto-fix failed — run manually: pip install 'sentence-transformers<5.0'")
                    log.warning(f"Version fix failed: {fix_result.stderr}")
            else:
                log.info(f"sentence_transformers version OK: {st_ver}")
    except Exception as e:
        log.warning(f"Version check error: {e}")

    # Get version
    try:
        result = subprocess.run(["lca", "--version"], capture_output=True, text=True, timeout=10)
        if result.returncode == 0:
            detail += f" — {result.stdout.strip()}"
    except Exception:
        pass

    ok(f"Step 1 complete ({elapsed:.0f}s)")
    results.set(1, "DONE", detail)


def step2_copy_index(project_path, results):
    """Copy pre-built LCA index."""
    step_banner(2, "Copying Code Index", 40)

    dest = project_path / ".lca"
    dest_index = dest / "index.json"

    if dest_index.exists():
        size_mb = dest_index.stat().st_size / (1024 * 1024)
        skip(f"Already exists ({size_mb:.1f} MB)")
        results.set(2, "SKIPPED", "already installed")
        return

    source = PACKAGE_DIR / "lca-index"
    if source.exists():
        info("Copying pre-built code index...")
        info("This gives you instant search — no indexing wait.")

        try:
            dest.mkdir(exist_ok=True)
            file_count = 0
            for item in source.rglob("*"):
                if item.is_file():
                    rel = item.relative_to(source)
                    target = dest / rel
                    target.parent.mkdir(parents=True, exist_ok=True)
                    shutil.copy2(item, target)
                    file_count += 1

            ok(f"Step 2 complete ({file_count} files copied)")
            log.info(f"Step 2: copied {file_count} files to {dest}")
            results.set(2, "DONE", f"copied to .lca ({file_count} files)")
        except Exception as e:
            fail(f"Copy failed: {e}")
            log.error(f"Step 2 error: {e}", exc_info=True)
            results.set(2, "FAILED", str(e))
    else:
        info("No pre-built index in package. Building fresh...")
        info("This scans all your code. Takes 1-5 minutes.")

        try:
            admin_path = project_path / "admin"
            result = subprocess.run(
                ["lca", "index", "build", str(admin_path), "-m", "etail_v3", "-o", str(dest_index)],
                capture_output=True,
                text=True,
                timeout=600,
            )
            log.info(f"lca index output:\n{result.stdout}")
            if dest_index.exists():
                ok("Step 2 complete (built fresh index)")
                results.set(2, "DONE", "built fresh index")
            else:
                fail("Indexing failed")
                results.set(2, "FAILED", "indexing produced no output")
        except Exception as e:
            fail(f"Indexing failed: {e}")
            results.set(2, "FAILED", str(e))

    # Also copy knowledge brain if included in package
    brain_source = PACKAGE_DIR / "knowledge-brain"
    brain_dest = project_path / "knowledge_brain"
    if brain_source.exists() and any(brain_source.rglob("*.md")):
        if brain_dest.exists() and any(brain_dest.rglob("*.md")):
            skip("Knowledge brain already exists")
        else:
            info("Copying Knowledge Brain (module documentation)...")
            brain_dest.mkdir(exist_ok=True)
            brain_count = 0
            for item in brain_source.rglob("*"):
                if item.is_file():
                    rel = item.relative_to(brain_source)
                    target = brain_dest / rel
                    target.parent.mkdir(parents=True, exist_ok=True)
                    shutil.copy2(item, target)
                    brain_count += 1
            ok(f"Knowledge brain installed ({brain_count} files)")
            log.info(f"Step 2: copied {brain_count} brain files to {brain_dest}")


def step3_copy_chroma(project_path, results):
    """Copy ChromaDB store."""
    step_banner(3, "Copying Search Database", 60)

    dest = project_path / "chroma_store"
    dest_db = dest / "chroma.sqlite3"

    if dest_db.exists():
        skip("Already exists")
        results.set(3, "SKIPPED", "already installed")
        return

    source = PACKAGE_DIR / "chroma-store"
    source_db = source / "chroma.sqlite3"

    if source_db.exists():
        info("Copying semantic search database...")
        info("This enables AI-powered code search.")

        try:
            dest.mkdir(exist_ok=True)
            file_count = 0
            for item in source.rglob("*"):
                if item.is_file():
                    rel = item.relative_to(source)
                    target = dest / rel
                    target.parent.mkdir(parents=True, exist_ok=True)
                    shutil.copy2(item, target)
                    file_count += 1

            ok(f"Step 3 complete ({file_count} files)")
            log.info(f"Step 3: copied {file_count} files to {dest}")
            results.set(3, "DONE", "copied to chroma_store")
        except Exception as e:
            fail(f"Copy failed: {e}")
            log.error(f"Step 3 error: {e}", exc_info=True)
            results.set(3, "FAILED", str(e))
    else:
        skip("No search DB in package — will build on first use")
        results.set(3, "SKIPPED", "will build on first use")


def step4_install_model(project_path, results):
    """Install BGE-M3 model — offline copy or HuggingFace download."""
    step_banner(4, "Installing AI Language Model", 80)

    info("This is the largest component (4.3 GB).\n")

    cache_dir = Path.home() / ".cache" / "huggingface" / "hub" / "models--BAAI--bge-m3"
    snapshots = cache_dir / "snapshots"

    # ── Path 1: Already cached ──
    if snapshots.exists():
        info(f"Already installed at:")
        info(f"  {cache_dir}")
        skip("Already installed")
        results.set(4, "SKIPPED", "already installed")
        return

    source = PACKAGE_DIR / "bge-m3"
    source_snapshots = source / "snapshots"

    # ── Path 2: Offline copy from package ──
    if source_snapshots.exists():
        info("Found model in package — copying offline...")
        info(f"Destination: {cache_dir}")
        print()
        info("DO NOT close this window!")
        info("This takes 2-5 minutes depending on disk speed.")
        print()

        start = time.time()
        log.info(f"Step 4 (offline): copying {source} -> {cache_dir}")

        try:
            all_files = [f for f in source.rglob("*") if f.is_file()]
            total = len(all_files)
            cache_dir.mkdir(parents=True, exist_ok=True)

            for i, item in enumerate(all_files, 1):
                rel = item.relative_to(source)
                target = cache_dir / rel
                target.parent.mkdir(parents=True, exist_ok=True)
                shutil.copy2(item, target)

                if i % 5 == 0 or i == total:
                    pct = int(i * 100 / total)
                    print(f"\r    Copying... {i}/{total} files [{pct}%]", end="", flush=True)

            elapsed = time.time() - start
            print()

            if snapshots.exists():
                ok(f"AI model installed — offline copy ({elapsed:.0f}s)")
                log.info(f"Step 4 complete (offline) in {elapsed:.1f}s")
                results.set(4, "DONE", f"offline copy ({elapsed:.0f}s)")
            else:
                fail("Copy may have failed")
                results.set(4, "FAILED", "snapshots missing after copy")
            return

        except Exception as e:
            print()
            fail(f"Offline copy failed: {e}")
            info("Will try downloading instead...")
            log.error(f"Step 4 offline error: {e}", exc_info=True)

    # ── Path 3: Download from HuggingFace ──
    info("Downloading model from HuggingFace (BAAI/bge-m3)...")
    info("Size: ~4.3 GB — requires internet connection.")
    info("")
    info("DO NOT close this window!")
    info("Download time depends on your internet speed:")
    info("  100 Mbps  →  ~6 minutes")
    info("  50 Mbps   →  ~12 minutes")
    info("  10 Mbps   →  ~60 minutes")
    print()

    log.info("Step 4 (download): fetching BAAI/bge-m3 from HuggingFace")
    start = time.time()

    try:
        # huggingface_hub is installed as part of sentence-transformers (step 1)
        from huggingface_hub import snapshot_download

        info("Downloading... (progress shown below)")
        print()

        model_path = snapshot_download(
            "BAAI/bge-m3",
            cache_dir=str(cache_dir.parent.parent),  # .cache/huggingface/hub/
        )

        elapsed = time.time() - start

        if snapshots.exists() or Path(model_path).exists():
            ok(f"AI model downloaded ({elapsed:.0f}s)")
            log.info(f"Step 4 complete (download) in {elapsed:.1f}s")
            results.set(4, "DONE", f"downloaded ({elapsed:.0f}s)")
        else:
            fail("Download completed but model not found in cache")
            results.set(4, "FAILED", "cache path mismatch")

    except ImportError:
        fail("huggingface_hub not installed")
        info("This should have been installed in Step 1.")
        info("Try manually: pip install huggingface-hub")
        info("Then run this installer again.")
        results.set(4, "FAILED", "huggingface_hub not available")
    except Exception as e:
        fail(f"Download failed: {e}")
        info("")
        info("Possible causes:")
        info("  - No internet connection")
        info("  - Firewall blocking huggingface.co")
        info("  - Insufficient disk space (~5 GB needed)")
        info("")
        info("Alternative: Ask your team lead for the offline")
        info("package that includes the bge-m3 folder.")
        log.error(f"Step 4 download error: {e}", exc_info=True)
        results.set(4, "FAILED", f"download failed — {e}")


def step5_verify(project_path, results):
    """Verify the installation."""
    step_banner(5, "Verifying Installation", 100)

    info("Running pipeline health check...")

    cli_path = project_path / ".sdlc" / "engine" / "cli.py"
    try:
        result = subprocess.run(
            [sys.executable, str(cli_path), "banner"],
            capture_output=True,
            text=True,
            timeout=30,
        )
        if result.returncode == 0:
            ok("Pipeline is working")
            log.info("Step 5: banner OK")
        else:
            warn("Pipeline command had issues")
            info(f"Try manually: cd {project_path}")
            info("             python .sdlc\\engine\\cli.py banner")
            log.warning(f"Step 5 banner failed: {result.stderr}")
    except Exception as e:
        warn(f"Verify error: {e}")
        log.error(f"Step 5 error: {e}", exc_info=True)

    # Verify LCA command
    try:
        result = subprocess.run(["lca", "--version"], capture_output=True, text=True, timeout=10)
        if result.returncode == 0:
            ok(f"LCA command available ({result.stdout.strip()})")
        else:
            warn("LCA command not found in PATH")
            info("Close this window, open a new terminal, try: lca --version")
    except FileNotFoundError:
        warn("LCA command not found in PATH")
        info("Close this window, open a new terminal, try: lca --version")
    except Exception:
        pass

    results.set(5, "DONE", "all systems go")


# ── Main ──
def main():
    try:
        header(f"SDLC Pipeline Engine — Setup v{VERSION}")

        print("  This installer will set up the AI-powered code")
        print("  analysis and bug tracking pipeline on your machine.\n")
        print("  What it installs:")
        print("    * LCA Code Analyzer    (searches your codebase)")
        print("    * AI Semantic Search   (finds related code by meaning)")
        print("    * BGE-M3 Language Model  (4.3 GB — one-time copy)")
        print("    * Pre-built code index   (instant search, no wait)\n")
        print("  Total time: ~5-8 minutes (first run)")
        print("              ~30 seconds  (if resuming a failed run)\n")

        log.info(f"Installer v{VERSION} started")
        log.info(f"Package dir: {PACKAGE_DIR}")

        # Pre-requisite checks
        all_ok, project_path = check_prerequisites()

        if not all_ok:
            print(f"\n  {'─' * 50}")
            print("\n   [FAIL] CANNOT CONTINUE\n")
            print("   Fix the items marked [FAIL] above, then")
            print("   run this installer again. It will pick up")
            print("   where it left off.\n")
            print(f"  {'─' * 50}\n")
            log.warning("Prerequisites check failed")
            return

        print(f"\n  {'─' * 50}")
        print("\n   [OK] ALL CHECKS PASSED\n")
        print(f"  {'─' * 50}\n")
        print(f"  Project: {project_path}\n")
        print("  The installer will now set up 5 components.")
        print("  Steps that are already done will be skipped.\n")
        print("  +──────────────────────────────────────────────+")
        print("  │  Step  │  What                    │  Time    │")
        print("  +────────+──────────────────────────+──────────+")
        print("  │  1/5   │  Install LCA tools       │  ~2 min  │")
        print("  │  2/5   │  Copy code index          │  ~5 sec  │")
        print("  │  3/5   │  Copy search database     │  ~5 sec  │")
        print("  │  4/5   │  Download AI model        │  varies  │")
        print("  │  5/5   │  Verify everything         │  ~5 sec  │")
        print("  +──────────────────────────────────────────────+\n")

        go = ask("Press ENTER to start (or type N to cancel):", "Y")
        if go == "N":
            print("  Cancelled.")
            return

        print(f"\n  {'=' * 50}")
        print("   INSTALLING")
        print(f"  {'=' * 50}")

        results = Results()
        start_all = time.time()

        step1_install_lca(project_path, results)
        step2_copy_index(project_path, results)
        step3_copy_chroma(project_path, results)
        step4_install_model(project_path, results)
        step5_verify(project_path, results)

        elapsed = time.time() - start_all
        log.info(f"Installation complete in {elapsed:.1f}s")
        info(f"\n  Total time: {elapsed:.0f} seconds")

        results.print_summary(project_path)

    except KeyboardInterrupt:
        print("\n\n  Installation cancelled by user.")
        log.info("Cancelled by user")
    except Exception as e:
        print(f"\n\n  Unexpected error: {e}")
        print(f"  Check {LOG_FILE} for details.")
        log.error(f"Unexpected error: {e}", exc_info=True)


if __name__ == "__main__":
    main()
