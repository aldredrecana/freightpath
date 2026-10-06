-- FreightPath database setup
-- Run ONCE on a new computer, as the PostgreSQL superuser (postgres).
-- It is safe to run again: it skips anything that already exists.
--
-- Usage (PowerShell, from the project folder):
--   & "C:\Program Files\PostgreSQL\18\bin\psql.exe" -h 127.0.0.1 -U postgres -f database-admin\setup.sql
--
-- BEFORE running: change the three placeholder passwords below, and use the
-- same passwords in your .env file.

-- 1. Roles ------------------------------------------------------------------
DO $$
BEGIN
    IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'freightpath_owner') THEN
        CREATE ROLE freightpath_owner LOGIN PASSWORD 'change_me_owner';
    END IF;

    IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'freightpath_app') THEN
        CREATE ROLE freightpath_app LOGIN PASSWORD 'change_me_app';
    END IF;

    IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'freightpath_readonly') THEN
        CREATE ROLE freightpath_readonly LOGIN PASSWORD 'change_me_ro';
    END IF;
END
$$;

-- 2. Database (created only if missing) --------------------------------------
SELECT 'CREATE DATABASE freightpath OWNER freightpath_owner'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'freightpath')
\gexec

-- 3. Privileges (inside the freightpath database) ----------------------------
\connect freightpath

-- Nobody gets access by default.
REVOKE ALL ON SCHEMA public FROM PUBLIC;
GRANT USAGE ON SCHEMA public TO freightpath_app, freightpath_readonly;

-- Tables the owner creates later (the Laravel migrations) get these privileges automatically.
ALTER DEFAULT PRIVILEGES FOR ROLE freightpath_owner IN SCHEMA public
    GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO freightpath_app;
ALTER DEFAULT PRIVILEGES FOR ROLE freightpath_owner IN SCHEMA public
    GRANT USAGE, SELECT ON SEQUENCES TO freightpath_app;
ALTER DEFAULT PRIVILEGES FOR ROLE freightpath_owner IN SCHEMA public
    GRANT SELECT ON TABLES TO freightpath_readonly;

-- Same privileges for tables that already exist (harmless on a fresh database).
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO freightpath_app;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO freightpath_app;
GRANT SELECT ON ALL TABLES IN SCHEMA public TO freightpath_readonly;