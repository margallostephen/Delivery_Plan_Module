@echo off
setlocal

:: Set your link here
set "URL=http://localhost/Delivery_Plan_Module/auto_send_email_report.php/"

:: Set the path to Brave Browser (adjust if installed in a different location)
set "BRAVE_PATH=C:\Program Files\BraveSoftware\Brave-Browser\Application\brave.exe"

:: Check if Brave is already running
tasklist /FI "IMAGENAME eq brave.exe" 1>NUL | find /I "brave.exe" >NUL
if "%errorlevel%"=="0" (
    echo Brave is already running. Opening new tab...
    start "" "%BRAVE_PATH%" --new-tab "%URL%"
) else (
    echo Brave is not running. Opening a solo tab...
    start "" "%BRAVE_PATH%" --new-window "%URL%"
)

:: Wait for 60 seconds (1 minute)
timeout /t 60 /nobreak >nul

:: Close only that specific tab (not the entire Brave if more tabs are open)
taskkill /IM brave.exe /F

echo Brave browser closed.
endlocal
exit