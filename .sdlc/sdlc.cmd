@echo off
REM SDLC Pipeline CLI Shortcut
REM Usage: sdlc show | sdlc metrics | sdlc quick fix billing "desc"
set "PYTHONIOENCODING=utf-8"
python "%~dp0pipeline_state.py" %*
