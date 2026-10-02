@echo off
set PGBIN="C:\Program Files\PostgreSQL\18\bin"
set /p PGPASSWORD=Enter freightpath_owner password:
set BACKUP_DIR=%~dp0backups

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

for /f %%i in ('powershell -command "Get-Date -Format yyyyMMdd_HHmmss"') do set STAMP=%%i

%PGBIN%\pg_dump.exe -h 127.0.0.1 -p 5432 -U freightpath_owner -Fc -f "%BACKUP_DIR%\freightpath_%STAMP%.backup" freightpath

if %ERRORLEVEL% EQU 0 (
    echo Backup saved: %BACKUP_DIR%\freightpath_%STAMP%.backup
) else (
    echo Backup FAILED.
)
pause