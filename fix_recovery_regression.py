"""
fix_recovery_regression.py
Restores all files that the recovery commit (98f04be0) incorrectly changed
back to their pre-recovery QA state (6ccdf612 = last correct QA commit).

This is a targeted restore: for each file that the recovery commit touched
AND that differs between pre-recovery (6ccdf612) and recovery (98f04be0),
we restore from pre-recovery.
"""
import subprocess, os, sys

REPO = r'd:\xampp\htdocs\etail_development_src'
PRE_RECOVERY = '6ccdf612'   # last correct commit on QA before the recovery
RECOVERY_COMMIT = '98f04be0'

def run(cmd):
    return subprocess.run(cmd, shell=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE, cwd=REPO)

def main():
    # Get all files the recovery commit changed
    r = run('git show ' + RECOVERY_COMMIT + ' --name-only --format=')
    all_files = [f.decode('utf-8', errors='replace').strip()
                 for f in r.stdout.split(b'\n') if f.strip()]
    all_files = [f for f in all_files if f and not f.startswith('commit ')]

    print('Files changed by recovery commit: ' + str(len(all_files)))

    to_restore = []
    for f in all_files:
        # Check if this file differs between pre-recovery and at recovery commit
        r2 = run('git diff --quiet ' + PRE_RECOVERY + ' ' + RECOVERY_COMMIT + ' -- "' + f + '"')
        if r2.returncode != 0:  # non-zero = files differ
            to_restore.append(f)

    print('Files that differ from pre-recovery (need restore): ' + str(len(to_restore)))
    print()

    ok = fail = skip = 0
    for f in to_restore:
        # Check file exists in pre-recovery commit
        r3 = run('git cat-file -e ' + PRE_RECOVERY + ':"' + f + '"')
        if r3.returncode != 0:
            # File was NEW in the recovery commit (created), skip
            print('  SKIP (new file): ' + f)
            skip += 1
            continue

        # Restore from pre-recovery
        r4 = run('git checkout ' + PRE_RECOVERY + ' -- "' + f + '"')
        if r4.returncode == 0:
            print('  RESTORED: ' + f)
            ok += 1
        else:
            print('  ERROR: ' + f + ': ' + r4.stderr.decode('utf-8', errors='replace').strip())
            fail += 1

    print()
    print('Done: ' + str(ok) + ' restored, ' + str(fail) + ' errors, ' + str(skip) + ' skipped (new files)')

if __name__ == '__main__':
    main()
