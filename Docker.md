# Deming — Docker Deployment Guide

> **Deming** is an open-source ISMS management tool (ISO 27001 / NIS 2) built on Laravel.
> This guide covers everything you need to run it with Docker Compose.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Architecture Overview](#architecture-overview)
3. [Quick Start](#quick-start)
4. [Environment Configuration](#environment-configuration)
5. [Build](#build)
6. [Initialization Variables](#initialization-variables)
7. [Start](#start)
8. [Stop](#stop)
9. [Ports](#ports)
10. [First Connection](#first-connection)
11. [Logs](#logs)
12. [Shell Access](#shell-access)
13. [Database Operations](#database-operations)
14. [Persistent Volumes](#persistent-volumes)
15. [Production Considerations](#production-considerations)
16. [Troubleshooting](#troubleshooting)

---

## Prerequisites

| Requirement | Minimum version |
|---|---|
| Docker Engine | 24.x |
| Docker Compose plugin | v2.x (`docker compose`) |
| Git | any recent version |
| Free RAM | 512 MB |
| Free disk | 2 GB |

> **Note:** The legacy `docker-compose` (v1, Python) is **not** supported. Use `docker compose` (v2, Go plugin).

---

## Architecture Overview

The stack is composed of two services orchestrated by Docker Compose:

```
                    ┌─────────────────────────────────────────────┐
                    │              Docker network                  │
                    │                                             │
  Host :8000 ──────►│  nginx:80  ──► artisan serve:8000 (PHP)    │
                    │                        │                    │
                    │               ┌────────▼────────┐           │
                    │               │  mysql:3306     │           │
                    │               │  (internal)     │           │
                    │               └─────────────────┘           │
                    └─────────────────────────────────────────────┘
```

| Service | Role | Image |
|---|---|---|
| `deming` | Nginx (reverse proxy) + Laravel (`artisan serve`) | Debian + PHP 8.3 |
| `mysql` | Database | MySQL 9.5 |

**Important:** The web layer is **nginx → `php artisan serve`**, not nginx → php-fpm.
Nginx listens on port 80 inside the container and proxies all requests to `artisan serve`
on port 8000 (internal). From the host, the application is accessible on the port mapped
to container port 80.

---

## Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/dbarzin/deming.git
cd deming

# 2. Create the environment file
cp .env.example .env

# 3. Set mandatory DB variables for Docker
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/^DB_HOST=.*/DB_HOST=mysql/' .env

# 4. Start the stack (builds the image on first run)
docker compose up
```

The application will be available at **http://localhost:8000** after initialization
completes (≈ 60–90 s on first run).

---

## Environment Configuration

The `.env` file is mounted as a volume into the container —
**all configuration happens there**, not in `docker-compose.yml`.

### Mandatory variables for Docker

```dotenv
# Must be the Docker Compose service name — NOT 127.0.0.1
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=deming
DB_USERNAME=deming_user
DB_PASSWORD=your_password
```

### Full `.env` reference

```dotenv
# ── Application ──────────────────────────────────────────
APP_NAME=Deming
APP_ENV=local           # lease it to local for automatic migrations
APP_KEY=                # Auto-generated on first boot if empty
APP_DEBUG=true          # false in production
APP_URL=http://localhost:8000
APP_BANNER_TEST=false   # add a warning banner for test environeent

# ── Database ─────────────────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=mysql           # ← Docker service name, never 127.0.0.1
DB_PORT=3306
DB_DATABASE=deming
DB_USERNAME=deming_user
DB_PASSWORD=your_password

# ── Mail (optional) ──────────────────────────────────────
MAIL_HOST=smtp.localhost
MAIL_PORT=2525

# ── LDAP (optional) ──────────────────────────────────────
LDAP_ENABLED=false
```

### MySQL root password

The image uses `MYSQL_RANDOM_ROOT_PASSWORD=1` — a random root password is generated at
first start and printed in the MySQL logs. The application never uses the root account;
only `DB_USERNAME` / `DB_PASSWORD` matter. `MYSQL_ROOT_PASSWORD` is not required.

---

## Build

The Docker image is built automatically on first `docker compose up`. To rebuild manually:

```bash
# Standard rebuild (uses cache)
docker compose build deming

# Force full rebuild (after Dockerfile or script changes)
docker compose build --no-cache deming
```

### What the build does

1. Starts from a Debian Bookworm + PHP 8.3 base image
2. Installs Nginx, required PHP extensions, and Composer
3. Clones the Deming repository into `/var/www/deming`
4. Copies the Nginx vhost (`docker/deming.conf`) to `/etc/nginx/conf.d/deming.conf`
5. Copies initialization scripts (`entrypoint.sh`, `initialdb.sh`, etc.) to `/etc/`
6. Installs PHP dependencies via Composer

> After any change to `Dockerfile`, `entrypoint.sh`, `initialdb.sh` or other Docker
> scripts, always rebuild with `--no-cache` to ensure the new version is used:
> ```bash
> docker compose down
> docker compose build --no-cache deming
> docker compose up
> ```

---

## Initialization Variables

These variables in `docker-compose.yml` control the first-run initialization:

| Variable | Values | Description |
|---|---|---|
| `INITIAL_DB` | `EN` / `FR` | Run migrations and seed the database in English or French |
| `UPLOAD_DB_ISO27001` | `EN` / `FR` | Import the ISO 27001 control framework |
| `USE_DEMO_DATA` | `1` / unset | Generate demo controls, measures and audit data |
| `RESET_DB` | `EN` / `FR` | **⚠️ Wipe and recreate the entire database** |
| `DB_SLEEP` | integer (seconds) | Extra wait before migration attempts (default: 10) |

### Recommended lifecycle

```yaml
# docker-compose.yml — first run
environment:
  - INITIAL_DB=FR
  - UPLOAD_DB_ISO27001=FR
  - USE_DEMO_DATA=1
  - DB_SLEEP=10
  - TZ=Europe/Paris
  - APP_FORCE_HTTPS=false
```

```yaml
# docker-compose.yml — after first successful start (optimized)
environment:
  - TZ=Europe/Paris
  - APP_FORCE_HTTPS=false
  # All initialization variables removed — no unnecessary work on restart
```

> **Never** leave `RESET_DB` enabled after the first run — it wipes all data on every restart.
> **Never** set `APP_ENV` here — always set it in `.env`.

---

## Start

### Foreground (logs visible in terminal)

```bash
docker compose up
```

### Background (detached mode)

```bash
docker compose up -d
```

### Check status

```bash
docker compose ps
```

Expected output when healthy:

```
NAME              IMAGE           COMMAND                SERVICE  STATUS         PORTS
deming-deming-1   deming-deming   "/opt/entrypoint.sh"   deming   Up             9000/tcp, 0.0.0.0:8000->80/tcp
deming-mysql-1    mysql:9.5       "docker-entrypoint…"   mysql    Up (healthy)   3306/tcp, 33060/tcp
```

The `(healthy)` status on MySQL confirms the healthcheck passed before Deming started.

---

## Stop

### Stop containers (preserve volumes and images)

```bash
docker compose stop
```

### Stop and remove containers (preserve volumes)

```bash
docker compose down
```

### Stop, remove containers **and** volumes (⚠️ destroys all data)

```bash
docker compose down -v
```

### Restart a single service

```bash
docker compose restart deming
```

---

## Ports

| Host port | Container port | Service | Description |
|---|---|---|---|
| **8000** | **80** | `deming` | Web application — Nginx entry point |
| *(internal)* | 8000 | `deming` | `artisan serve` — proxied by Nginx, not directly accessible |
| *(not exposed)* | 3306 | `mysql` | MySQL — internal only |

The port mapping in `docker-compose.yml` must be:

```yaml
services:
  deming:
    ports:
      - "8000:80"   # host:container — nginx listens on container port 80
```

> ⚠️ **Common mistake:** `80:8000` is wrong (it maps host port 80 to container port 8000
> where nothing listens from outside). Always use `HOST_PORT:80`.

### Change the host port

```yaml
ports:
  - "80:80"     # serve on http://localhost
  - "8080:80"   # serve on http://localhost:8080
```

### Expose MySQL for a DB client (dev only)

```yaml
mysql:
  ports:
    - "3306:3306"
```

---

## First Connection

Once the stack is running and the logs show `Generate test data`, open:

```
http://localhost:8000
```

### Default credentials

| Role | Email | Password |
|---|---|---|
| Administrator | `admin@admin.com` | `password` |

> **Important:** Change the default password immediately after first login via
> **Settings → My profile → Change password**.

### Role hierarchy

| Role | Access level |
|---|---|
| Admin | Full access, user management |
| User | Controls, measures, actions |
| Auditee | Read-only on assigned controls |
| Auditor | Audit workflow access |

---

## Logs

### All services (follow mode)

```bash
docker compose logs -f
```

### Application logs only

```bash
docker compose logs -f deming
```

### Database logs only

```bash
docker compose logs -f mysql
```

### Laravel application log

```bash
docker compose exec deming tail -f /var/www/deming/storage/logs/laravel.log
```

### Nginx logs

```bash
docker compose exec deming tail -f /var/log/nginx/access.log
docker compose exec deming tail -f /var/log/nginx/error.log
```

### Last N lines

```bash
docker compose logs --tail=100 deming
```

### Normal startup sequence

A healthy first-run startup produces logs in this order:

```
mysql-1   | ready for connections. Version: '9.5.0'
mysql-1   | [Healthcheck] OK
deming-1  | Waiting for MySQL to be ready...
deming-1  | MySQL is ready.
deming-1  | Waiting for 10 seconds before executing migration...
deming-1  | Initialize database
deming-1  |    INFO  Nothing to migrate.
deming-1  |    INFO  Seeding database.
deming-1  |    INFO  Database cleared.
deming-1  |    INFO  103 lines inserted.
deming-1  |    INFO  5 new domains created.
deming-1  |    INFO  Generate test data.
deming-1  |    INFO  Encryption keys generated successfully.
deming-1  |    INFO  New client created successfully.
deming-1  | Starting periodic command scheduler: cron.
deming-1  | [NOTICE] fpm is running, ready to handle connections
```

The `WARN Command cancelled` messages during seeding are normal — they are produced by
internal seeders asking for confirmation in production mode and are not fatal.

---

## Shell Access

### Open a bash shell in the app container

```bash
docker compose exec deming bash
```

### Useful Artisan commands

```bash
# Clear all caches
docker compose exec deming php artisan cache:clear
docker compose exec deming php artisan config:clear
docker compose exec deming php artisan view:clear

# Run pending migrations
docker compose exec deming php artisan migrate --force

# Show current environment
docker compose exec deming php artisan env

# List all Artisan commands
docker compose exec deming php artisan list
```

---

## Database Operations

### Access the MySQL CLI

```bash
docker compose exec mysql mysql -u deming_user -pyour_password deming
```

### Backup the database

```bash
docker compose exec mysql \
  mysqldump -u deming_user -pyour_password deming \
  > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restore a backup

```bash
docker compose exec -T mysql \
  mysql -u deming_user -pyour_password deming \
  < backup_20250101_120000.sql
```

### Full reset (⚠️ destroys all data)

Set `RESET_DB=FR` (or `EN`) in `docker-compose.yml`, then:

```bash
docker compose down
docker compose up
```

Remove `RESET_DB` immediately after the reset completes.

---

## Persistent Volumes

| Volume | Container path | Contents |
|---|---|---|
| `deming_dbdata` | `/var/lib/mysql` | All database data |

The `.env` file and `docker/custom/` files are bind-mounted from the host directory —
they survive container restarts and removals as long as the project directory exists.

### List volumes

```bash
docker volume ls | grep deming
```

### Backup the database volume

```bash
docker compose exec mysql \
  mysqldump -u deming_user -pyour_password deming \
  > backup_$(date +%Y%m%d_%H%M%S).sql
```

---

## Production Considerations

### 1. Remove initialization variables after first run

```yaml
# Remove from docker-compose.yml environment after first successful start:
# - INITIAL_DB
# - UPLOAD_DB_ISO27001
# - USE_DEMO_DATA
# - DB_SLEEP
```

### 2. Secure the `.env` file

```bash
chmod 600 .env
```

Never commit `.env` to version control.

### 3. Set `APP_ENV` in `.env` only — never in `docker-compose.yml`

```dotenv
APP_ENV=production
APP_DEBUG=false
```

### 4. Enable automatic restarts

```yaml
services:
  deming:
    restart: unless-stopped
  mysql:
    restart: unless-stopped
```

### 5. Add HTTPS with a reverse proxy

Place Nginx or Traefik in front of the stack and terminate TLS there.
Update `APP_URL` and `APP_FORCE_HTTPS` in `.env`:

```dotenv
APP_URL=https://deming.example.com
APP_FORCE_HTTPS=true
```

### 6. Keep MySQL internal

Never expose port 3306 to the host in production.

---

## Troubleshooting

### Container loops on "Not ready, retrying"

MySQL is reachable at the network level but Laravel cannot connect.
The most common cause is a wrong value in `.env`:

```bash
docker compose exec deming grep '^DB_' .env
```

Both `DB_CONNECTION` and `DB_HOST` must be set to `mysql`:

```dotenv
DB_CONNECTION=mysql   # ← not 127.0.0.1
DB_HOST=mysql         # ← not 127.0.0.1
```

### "APPLICATION IN PRODUCTION — Command cancelled"

Seeders are blocked because `APP_ENV=production` is set in `docker-compose.yml`.
Remove it from `docker-compose.yml` and set it in `.env` instead.

### No response on port 8000 — connection reset

The port mapping is inverted. In `docker-compose.yml`:

```yaml
ports:
  - "8000:80"   # ✅ correct  — host 8000 → container nginx 80
  # - "80:8000" # ❌ wrong    — host 80 → container port 8000 (nothing there)
```

### Container starts but exits silently after cron

An initialization script exited with a non-zero code. Check:

```bash
docker compose logs deming | tail -50
```

The `|| echo "skipped"` guards in `entrypoint.sh` prevent optional scripts from
killing the startup. If a mandatory script fails, check its output for the root cause.

### Nginx "conflicting server name" warning

Two nginx configs both declare `server_name _;`. The Dockerfile should copy
`docker/deming.conf` to `deming.conf` (not `default.conf`). Rebuild to fix:

```bash
docker compose down
docker compose build --no-cache deming
docker compose up
```

### sed: cannot rename — Device or resource busy

`sed -i` cannot modify `.env` because it is a Docker bind mount. Never use `sed -i`
on the `.env` file from inside the container. Edit it on the host instead.

### Reset everything and start fresh

```bash
docker compose down -v
docker compose build --no-cache deming
docker compose up
```

### Diagnostic commands

```bash
# Nginx config syntax check
docker compose exec deming nginx -t

# Full active nginx configuration
docker compose exec deming nginx -T

# Nginx config files present
docker compose exec deming find /etc/nginx/conf.d /etc/nginx/sites-enabled -type f

# Check PHP version and key extensions
docker compose exec deming php -v
docker compose exec deming php -m | grep -E 'pdo|mbstring|xml|gd'

# Laravel environment
docker compose exec deming php artisan env
```

---

## Useful References

- **Project repository:** https://github.com/dbarzin/deming
- **Official documentation:** https://dbarzin.github.io/deming/
- **API documentation:** https://dbarzin.github.io/deming/api/
- **Issue tracker:** https://github.com/dbarzin/deming/issues
- **Discussions:** https://github.com/dbarzin/deming/discussions
- **License:** GPL-3.0

