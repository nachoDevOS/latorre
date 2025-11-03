<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Laravel Template Voyager Example

## Instalación
```
composer install
cp .env.example .env
php artisan example:install
sudo chmod -R 775 storage bootstrap/cache
chown -R www-data storage bootstrap/cache
```

## Versión de Laravel
Laravel Framework 10.0.0

## Requisistos
- php >= 8.1
- Extenciones **php-mbstring php-intl php-dom php-gd php-xml php-zip php-curl php-fpm php-mysql**


## Dockerfile
Crear en la Raiz del proyecto los siguientes archivos:
Dockerfile
unit.json

Ejecutar.
```
docker build -t example .
docker run -e DB_DATABASE=example -e DB_HOST=host.docker.internal -p 8000:8000 -t example
```
Ejemplo
```
docker run  -e DB_CONNECTION=mysql -e DB_HOST=host.docker.internal -e DB_PORT=3306 -e DB_DATABASE=example -e DB_USERNAME=root -e DB_CONNECTION_SOLUCION_DIGITAL=mysql -e DB_HOST_SOLUCION_DIGITAL=host.docker.internal -e DB_PORT_SOLUCION_DIGITAL=3306 -e DB_DATABASE_SOLUCION_DIGITAL=soluciondigital -e DB_USERNAME_SOLUCION_DIGITAL=root -p 8000:8000 -t example
```
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Laravel Template Voyager Example

## Instalación
```
composer install
cp .env.example .env
php artisan example:install
sudo chmod -R 775 storage bootstrap/cache
chown -R www-data storage bootstrap/cache
```

## Versión de Laravel
Laravel Framework 10.0.0

## Requisistos
- php >= 8.1
- Extenciones **php-mbstring php-intl php-dom php-gd php-xml php-zip php-curl php-fpm php-mysql**


## Dockerfile
Crear en la Raiz del proyecto los siguientes archivos:
Dockerfile
unit.json

Ejecutar.
```
docker build -t example .
docker run -e DB_DATABASE=example -e DB_HOST=host.docker.internal -p 8000:8000 -t example
```
Ejemplo
```
docker run  -e DB_CONNECTION=mysql -e DB_HOST=host.docker.internal -e DB_PORT=3306 -e DB_DATABASE=example -e DB_USERNAME=root -e DB_CONNECTION_SOLUCION_DIGITAL=mysql -e DB_HOST_SOLUCION_DIGITAL=host.docker.internal -e DB_PORT_SOLUCION_DIGITAL=3306 -e DB_DATABASE_SOLUCION_DIGITAL=soluciondigital -e DB_USERNAME_SOLUCION_DIGITAL=root -p 8000:8000 -t example
```


## Configuración de Nginx (nginx.conf o tu sitio)
```sh
client_max_body_size 300M;
client_body_timeout 300s;
client_header_timeout 300s;
keepalive_timeout 300s;
send_timeout 300s;
fastcgi_read_timeout 300s;
fastcgi_send_timeout 300s;
fastcgi_connect_timeout 300s;
proxy_read_timeout 300s;
```
## Configuración de PHP (php.ini)
```sh
upload_max_filesize = 300M
post_max_size = 300M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
```
## Configuración específica para tu sitio (en server block)
```sh
server {
    listen 80;
    server_name tu-dominio.com;
    
    # Configuración para uploads grandes
    client_max_body_size 300M;
    client_body_timeout 300s;
    client_header_timeout 300s;
    
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_read_timeout 300s;
        fastcgi_send_timeout 300s;
        fastcgi_connect_timeout 300s;
    }
    
    # O si usas PHP-FPM en puerto
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_read_timeout 300s;
    }
}
```# latorre
