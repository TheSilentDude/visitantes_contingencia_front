# Guía de Despliegue - Frontend Web

## 📋 Requisitos Previos

- PHP >= 8.1
- Composer
- Node.js >= 18
- NPM o Yarn
- Nginx o Apache
- Acceso al **Backend API** (Puerto 8000 por defecto)

## 🚀 Despliegue en Desarrollo

### 1. Clonar/Copiar el Proyecto

```bash
cd /ruta/a/tu/proyecto/frontend-web
```

### 2. Instalar Dependencias PHP

```bash
composer install
```

### 3. Instalar Dependencias Node.js

```bash
npm install
# o
yarn install
```

### 4. Configurar Variables de Entorno

```bash
cp .env.example .env
```

Editar `.env`:

```env
APP_NAME="Carnetización Frontend"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:3000

# URL del Backend API (Proxy)
BACKEND_URL=http://localhost:8000
API_TIMEOUT=30

# Session (Manejo de estados)
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 5. Generar Application Key

```bash
php artisan key:generate
```

### 6. Compilar Assets (Desarrollo)

```bash
npm run dev
```

Este comando inicia el servidor de desarrollo de Vite con hot-reload.

### 7. Iniciar Servidor Laravel

En otra terminal:

```bash
php artisan serve --port=3000
```

El frontend estará disponible en: **http://localhost:3000**

## 🏭 Despliegue en Producción

### 1. Preparar el Servidor

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar PHP 8.1 y extensiones
sudo apt install php8.1 php8.1-fpm php8.1-pgsql php8.1-xml php8.1-mbstring \
    php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath -y

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y

# Instalar Nginx
sudo apt install nginx -y
```

### 2. Copiar Archivos al Servidor

```bash
# Desde tu máquina local
rsync -avz --exclude 'node_modules' --exclude 'vendor' \
    /ruta/local/frontend-web/ usuario@servidor:/var/www/frontend-web/
```

O usando Git:

```bash
# En el servidor
cd /var/www
git clone tu-repositorio.git frontend-web
cd frontend-web
```

### 3. Configurar Permisos

```bash
cd /var/www/frontend-web

# Dar permisos al usuario web
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 4. Instalar Dependencias

```bash
# Dependencias PHP (sin dev)
composer install --no-dev --optimize-autoloader

# Dependencias Node.js
npm install --production
```

### 5. Configurar Variables de Entorno

```bash
cp .env.example .env
nano .env
```

Configurar para producción:

```env
APP_NAME="Carnetización"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tudominio.com

# URL del Backend API (producción)
API_URL=https://api.tudominio.com
VITE_API_URL=https://api.tudominio.com

# Base de datos
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=carnetizacion
DB_USERNAME=carnet_user
DB_PASSWORD=password_seguro

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_DOMAIN=.tudominio.com

# Cache
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 6. Generar Key y Optimizar

```bash
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Compilar Assets para Producción

```bash
npm run build
```

Esto generará los assets optimizados en `public/build/`

### 8. Configurar Nginx

Crear archivo de configuración:

```bash
sudo nano /etc/nginx/sites-available/frontend-web
```

Contenido:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name tudominio.com www.tudominio.com;
    
    root /var/www/frontend-web/public;
    index index.php index.html;

    # Logs
    access_log /var/log/nginx/frontend-access.log;
    error_log /var/log/nginx/frontend-error.log;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript 
               application/x-javascript application/xml+rss 
               application/javascript application/json;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 9. Habilitar Sitio

```bash
# Crear enlace simbólico
sudo ln -s /etc/nginx/sites-available/frontend-web /etc/nginx/sites-enabled/

# Verificar configuración
sudo nginx -t

# Recargar Nginx
sudo systemctl reload nginx
```

### 10. Configurar SSL con Let's Encrypt

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtener certificado SSL
sudo certbot --nginx -d tudominio.com -d www.tudominio.com

# Renovación automática (ya configurada por defecto)
sudo certbot renew --dry-run
```

### 11. Configurar Supervisor (Opcional - para queues)

Si usas colas:

```bash
sudo apt install supervisor -y
sudo nano /etc/supervisor/conf.d/frontend-worker.conf
```

Contenido:

```ini
[program:frontend-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/frontend-web/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/frontend-web/storage/logs/worker.log
```

Iniciar:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start frontend-worker:*
```

## 🔄 Actualización del Frontend

### Script de Actualización

Crear archivo `deploy.sh`:

```bash
#!/bin/bash

echo "🚀 Iniciando despliegue del frontend..."

# Ir al directorio
cd /var/www/frontend-web

# Modo mantenimiento
php artisan down

# Actualizar código
git pull origin main

# Instalar dependencias
composer install --no-dev --optimize-autoloader
npm install --production

# Compilar assets
npm run build

# Limpiar y optimizar
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permisos
sudo chown -R www-data:www-data storage bootstrap/cache

# Salir de mantenimiento
php artisan up

echo "✅ Despliegue completado!"
```

Dar permisos:

```bash
chmod +x deploy.sh
```

Ejecutar:

```bash
./deploy.sh
```

## 🐳 Despliegue con Docker

### 1. Build de la Imagen

```bash
cd frontend-web
docker build -t carnetizacion-frontend:latest .
```

### 2. Ejecutar Contenedor

```bash
docker run -d \
  --name carnetizacion-frontend \
  -p 3000:80 \
  -v $(pwd)/.env:/var/www/.env \
  -v $(pwd)/storage:/var/www/storage \
  carnetizacion-frontend:latest
```

### 3. Con Docker Compose

Ya está configurado en el `docker-compose.yml` raíz:

```bash
# Desde la raíz del proyecto
docker-compose up -d frontend-web
```

## 📊 Monitoreo

### Logs

```bash
# Logs de Laravel
tail -f storage/logs/laravel.log

# Logs de Nginx
sudo tail -f /var/log/nginx/frontend-access.log
sudo tail -f /var/log/nginx/frontend-error.log

# Logs de PHP-FPM
sudo tail -f /var/log/php8.1-fpm.log
```

### Health Check

Crear endpoint de health check en `routes/web.php`:

```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'api_connection' => Http::get(env('API_URL') . '/health')->successful()
    ]);
});
```

## 🔒 Seguridad

### Checklist de Seguridad

- [ ] `APP_DEBUG=false` en producción
- [ ] `APP_ENV=production`
- [ ] SSL/TLS configurado (HTTPS)
- [ ] Permisos correctos en storage y bootstrap/cache
- [ ] Firewall configurado (UFW)
- [ ] Fail2ban instalado
- [ ] Backups automáticos configurados
- [ ] Variables sensibles en .env (no en código)
- [ ] CORS configurado correctamente en el backend
- [ ] Rate limiting habilitado

### Configurar Firewall

```bash
# Habilitar UFW
sudo ufw enable

# Permitir SSH
sudo ufw allow 22/tcp

# Permitir HTTP y HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Ver estado
sudo ufw status
```

## 🔧 Troubleshooting

### Error 500

```bash
# Ver logs
tail -f storage/logs/laravel.log

# Limpiar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Assets no cargan

```bash
# Recompilar assets
npm run build

# Verificar permisos
sudo chown -R www-data:www-data public/build
```

### Error de conexión con API

Verificar en `.env`:
```env
API_URL=https://api.tudominio.com
VITE_API_URL=https://api.tudominio.com
```

Y en `resources/js/bootstrap.js`:
```javascript
axios.defaults.baseURL = import.meta.env.VITE_API_URL;
```

## 📞 Soporte

Para más información:
- [README.md](README.md) - Documentación general
- [GUIA-MIGRACION.md](../GUIA-MIGRACION.md) - Guía de migración completa
- [Backend API](../backend-api/README.md) - Documentación del backend

---

**¡Frontend listo para producción!** 🎉
