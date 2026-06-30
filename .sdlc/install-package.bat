@echo off
title SDLC Pipeline Installer
color 0B

echo.
echo  SDLC Pipeline Engine - Setup v5.0
echo  ==================================
echo.

REM Check Python exists
python --version >nul 2>&1
if errorlevel 1 (
    echo  Python is not installed or not in PATH.
    echo.
    echo  To fix:
    echo    1. Go to https://python.org/downloads
    echo    2. Download Python 3.12 or latest
    echo    3. CHECK "Add Python to PATH" during install
    echo    4. Restart your computer
    echo    5. Run this installer again
    echo.
    pause
    exit /b 1
)

REM Run the Python installer
python "%~dp0install.py"

echo.
echo  Press any key to exit...
pause >nul
