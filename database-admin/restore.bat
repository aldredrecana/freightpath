@echo off
set PGBIN="C:\Program Files\PostgreSQL\18\bin"
set BACKUP_DIR=%~dp0backups

if "%~1"=="" (
    echo Usage: restore.bat backup_file_name
    echo Example: restore.bat freightpath_20261002_101500.backup
    pause
    exit /b 1
)

echo Enter the postgres superuser password when asked.
%PGBIN%\psql.exe -h 127.0.0.1 -p 5432 -U postgres -c "DROP DATABASE IF EXISTS freightpath_restore;"
%PGBIN%\psql.exe -h 127.0.0.1 -p 5432 -U postgres -c "CREATE DATABASE freightpath_restore OWNER freightpath_owner;"
%PGBIN%\pg_restore.exe -h 127.0.0.1 -p 5432 -U postgres -d freightpath_restore --no-owner --role=freightpath_owner "%BACKUP_DIR%\%~1"

echo Done. Check the freightpath_restore database in pgAdmin.
pause