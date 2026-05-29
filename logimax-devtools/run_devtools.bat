@echo off
:: Logimax DevTools - Development Startup Script
:: Starts both frontend and backend servers

echo ========================================
echo   Logimax DevTools - Dev Server
echo ========================================
echo.

:: Check for arguments
if "%1"=="frontend" goto frontend
if "%1"=="backend" goto backend
if "%1"=="--help" goto help
if "%1"=="-h" goto help

:: Default: Start both
echo Starting both servers...
echo.

:: Start backend in new window
start "DevTools Backend" cmd /k "cd /d %~dp0backend && uv run uvicorn app.main:app --reload --port 8800"

:: Wait a moment for backend to start
timeout /t 2 /nobreak > nul

:: Start frontend in new window
start "DevTools Frontend" cmd /k "cd /d %~dp0frontend && npm run dev"

echo.
echo Servers starting...
echo   Frontend: http://localhost:5173
echo   Backend:  http://localhost:8800
echo   API Docs: http://localhost:8800/docs
echo.
echo Close the terminal windows to stop the servers.
goto end

:frontend
echo Starting frontend only...
cd /d %~dp0frontend
npm run dev
goto end

:backend
echo Starting backend only...
cd /d %~dp0backend
uv run uvicorn app.main:app --reload --port 8800
goto end

:help
echo.
echo Usage: run_devtools.bat [option]
echo.
echo Options:
echo   (none)      Start both frontend and backend
echo   frontend    Start frontend only
echo   backend     Start backend only
echo   --help      Show this help
echo.

:end
