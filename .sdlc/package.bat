@echo off
title SDLC Package Builder
color 0E
python --version >nul 2>&1
if errorlevel 1 (
    echo Python not found. Install Python first.
    pause
    exit /b 1
)
python "%~dp0package.py"
pause
