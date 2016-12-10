@echo off
REM  -----------------------
REM |  SHAME! SHAME! SHAME! |
REM  -----------------------

for /F "tokens=*" %%A in (.env) do set %%A

@echo on
docker-compose exec -d postgres psql -U postgres -c 'create database %MICROMESSAGE_DATABASE_NAME%'
docker-compose exec -d web php bin/console.php orm:schema-tool:update --force
