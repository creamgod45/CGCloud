-- CGCloud PostgreSQL Initialization Script
-- User: cgcloud | Schema: public | Permissions: SELECT, INSERT, UPDATE, DELETE

-- Grant CONNECT on database
GRANT CONNECT ON DATABASE cgcloud TO cgcloud;

-- Grant USAGE on public schema
GRANT USAGE ON SCHEMA public TO cgcloud;

-- Grant CRUD privileges on all existing tables
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO cgcloud;

-- Grant USAGE + SELECT on all sequences (required for INSERT with SERIAL/BIGSERIAL columns)
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO cgcloud;

-- Grant EXECUTE on all existing functions
GRANT EXECUTE ON ALL FUNCTIONS IN SCHEMA public TO cgcloud;

-- ─── Default Privileges (applies to future objects) ───────────────────────────

-- Future tables
ALTER DEFAULT PRIVILEGES IN SCHEMA public
    GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO cgcloud;

-- Future sequences
ALTER DEFAULT PRIVILEGES IN SCHEMA public
    GRANT USAGE, SELECT ON SEQUENCES TO cgcloud;

-- Future functions
ALTER DEFAULT PRIVILEGES IN SCHEMA public
    GRANT EXECUTE ON FUNCTIONS TO cgcloud;
