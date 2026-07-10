# Frontend Web - Sistema de Carnetización

Frontend construido con Laravel Blade + Vite para el sistema de carnetización.

## Requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18
- NPM o Yarn

## Instalación

1. Instalar dependencias PHP:
```bash
composer install
```

2. Instalar dependencias Node:
```bash
npm install
```

3. Configurar variables de entorno:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurar URL del backend API en `.env`:
```env
API_URL=http://localhost:8000
VITE_API_URL=http://localhost:8000
```

## Desarrollo

1. Iniciar servidor Laravel:
```bash
php artisan serve --port=3000
```

2. En otra terminal, iniciar Vite:
```bash
npm run dev
```

El frontend estará disponible en: `http://localhost:3000`

## Build para Producción

```bash
npm run build
```

Los assets compilados se generarán en `public/build/`

## Estructura

- `resources/views/` - Vistas Blade
- `resources/js/` - JavaScript/Vue components
- `resources/css/` - Estilos CSS
- `public/` - Assets públicos
- `routes/web.php` - Rutas web

## Configuración de Nginx

Ver archivo `nginx.conf` para configuración de producción.

## Consumo de API

El frontend consume el backend API usando Axios. Configuración en `resources/js/bootstrap.js`:

```javascript
axios.defaults.baseURL = import.meta.env.VITE_API_URL;
axios.defaults.withCredentials = true;
```

## Deployment

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan config:cache
php artisan view:cache
```
