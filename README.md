# Bitácora industrial

Aplicación web interna para registrar trabajos de operación y mantenimiento en una planta industrial de cannabis. Está basada en Laravel, PostgreSQL, Redis, Nginx y Docker Compose.

## Funciones principales

- CRUD de zonas, salas, contenedores, departamentos y equipos de trabajo.
- Inventario de aires acondicionados, deshumidificadores, sensores y otros activos.
- Cuentas separadas para técnicos y supervisores.
- Códigos QR imprimibles para abrir la ficha de trabajo de cada dispositivo.
- Inicio y finalización rápida de tareas desde un teléfono.
- Horarios automáticos e inalterables para técnicos.
- Corrección manual de registros y horarios exclusivamente por supervisores.

## Requisitos

- Docker Engine 24 o posterior.
- Docker Compose v2 (`docker compose`).
- En Windows: Docker Desktop con WSL2.
- En Linux: usuario con permiso para ejecutar Docker.

No es necesario instalar PHP, Composer, PostgreSQL, Redis ni Nginx en el anfitrión.

## Instalación en Linux

```bash
git clone https://github.com/vgdevelop/bitacora.git
cd bitacora
cp .env.example .env
nano .env
chmod +x docker/install.sh
./docker/install.sh
```

Antes de ejecutar el instalador cambia como mínimo:

```dotenv
DB_PASSWORD=una_clave_segura
ADMIN_EMAIL=admin@tu-dominio.local
ADMIN_PASSWORD=una_clave_segura_para_el_supervisor
APP_URL=http://IP_DEL_SERVIDOR:8000
```

`APP_URL` se incorpora en los códigos QR. Debe ser una IP o nombre DNS accesible desde los teléfonos de los técnicos.

## Instalación manual en Linux o Windows

```bash
cp .env.example .env
# Editar .env antes de continuar
docker compose build
docker compose run --rm app composer install --no-interaction --prefer-dist --optimize-autoloader
docker compose run --rm app php artisan key:generate --force
docker compose run --rm app php artisan migrate --seed --force
docker compose up -d
```

En PowerShell sustituye `cp` por `Copy-Item .env.example .env`.

## Operación habitual

```bash
docker compose up -d
docker compose ps
docker compose logs -f app web
docker compose down
```

Para actualizar:

```bash
git pull
docker compose build
docker compose run --rm app composer install --no-interaction --prefer-dist --optimize-autoloader
docker compose run --rm app php artisan migrate --force
docker compose up -d
```

PostgreSQL y Redis usan volúmenes Docker. `docker compose down` conserva los datos; `docker compose down -v` los elimina permanentemente.

## Copia de seguridad

Los respaldos contienen datos operativos y credenciales cifradas, por lo que
`database/backups/` está excluido de Git. Para crear un respaldo local en formato
custom de PostgreSQL:

```bash
mkdir -p database/backups
docker compose exec -T postgres sh -c 'pg_dump -Fc -U "$POSTGRES_USER" "$POSTGRES_DB"' > database/backups/bitacora.dump
```

Para restaurarlo sobre una base vacía:

```bash
docker compose exec -T postgres sh -c 'pg_restore --clean --if-exists -U "$POSTGRES_USER" -d "$POSTGRES_DB"' < database/backups/bitacora.dump
```

El seeder incluye la configuración base de departamentos, equipos, 10
contenedores de floración, 4 salas y sus dispositivos. No incluye usuarios
operativos ni registros de trabajo.

## Stack

- Laravel 13 y PHP 8.4
- PostgreSQL 17
- Redis 8
- Nginx 1.28
- Blade y CSS nativo
- `endroid/qr-code`
