@echo off
setlocal
cd /d "%~dp0"

if not exist artisan (
  echo error: run this script from the Knowledge Hub project root.
  exit /b 1
)

where php >nul 2>&1
if errorlevel 1 (
  echo error: php is not on PATH.
  exit /b 1
)

if not exist vendor\autoload.php (
  echo error: run composer install first.
  exit /b 1
)

php artisan hub:link-storage
exit /b %ERRORLEVEL%
