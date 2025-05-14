# WebGuard - Proyecto Web Seguro con Docker

WebGuard es un proyecto que configura un entorno de desarrollo web seguro utilizando Docker. Permite a los usuarios crear su propia página web sin preocuparse en exceso por la seguridad, ya que incluye medidas de protección preconfiguradas.


## Tabla de Contenidos

- [Descripción](#descripción)
- [Requisitos](#requisitos)
- [Estructura de archivos](#estructura-de-archivos)
- [Instalación](#instalación)
- [Guía de uso](#guía-de-uso)
- [Contribuciones](#contribuciones)

## Descripción

WebGuard es una solución integral basada en contenedores Docker para alojar y gestionar sitios web con una configuración de seguridad avanzada. El proyecto consta de tres contenedores:

- **Apache**: Servidor web encargado de alojar la aplicación.
- **MySQL**: Sistema de gestión de bases de datos para el contenido del sitio web.
- **phpMyAdmin**: Interfaz web que permite administrar la base de datos MySQL de forma sencilla.


Webguard por defecto te protege de:

- **Exposición de información**: Toda la información que suele ser pública por defecto, como el software utilizado, sus versiones y archivos de configuración, en WebGuard está completamente ocultada.

- **Vulnerabilidades de software**: Gracias a la estructura basada en Docker, basta con reiniciar los contenedores para actualizar todo el software a las últimas versiones.

- **XSS / Inyecciones SQL / Archivos maliciosos**: WebGuard está preconfigurado para filtrar todo lo que se suba a la web y bloquear cualquier contenido malintencionado. Eres libre de crear formularios con mayor seguridad.

- **DDoS y escaneos web**: Usando herramientas preconfiguradas, WebGuard bloqueará cualquier IP que realice múltiples solicitudes maliciosas. Puedes personalizar la cantidad de solicitudes y la duración del bloqueo según tus necesidades.

- **Compromiso de la máquina física**: Al estar aislado en contenedores Docker, incluso si un atacante compromete la web, no podrá acceder al sistema físico donde se aloja el servidor web.

## Requisitos

Docker y Docker compose instalados

## Estructura de archivos

```
WebGuard/
├── Dockerfile
├── docker-compose.yml
├── restart.sh
├── conf
│   ├── apache2.conf
│   ├── mod-evasive.conf
│   ├── security.conf
│   └── security2.conf
├── dump
│   └── myDb.sql
└── www
   ├── index.html
   ├── infra.html
   ├── desarrollo.html
   ├── desarrollo2.html
   ├── desarrollo3.html
   ├── web.html
   ├── css
   │   └── styles.css
   ├── login.php
   ├── register.php
   ├── dashboard
   │   └── index.html
   ├── img
   │   └── ...
   ├── includes
   │   └── config.php
   ├── js
   │   ├── auth.js
   │   └── main.js
   └── upload
       └── upload.php
```
En la raíz se encuentran los archivos `Dockerfile` y `docker-compose`, además de un pequeño script para iniciar y reiniciar los contenedores rápidamente. Adicionalmente, hemos dividido el resto de los archivos en `/conf`, `/dump` y `/www`:
- **/conf**: Contiene todos los archivos de configuración que se incluirán en los contenedores una vez se inicien los Docker. Tenerlos organizados de esta manera facilita la modificación de la configuración según las necesidades.
- **/dump**: Un directorio con un solo archivo que sirve como punto de restauración de la base de datos. Este archivo no debe ser modificado.
- **/www**: Contiene los archivos de la web, como `.html`, `.css`, `.js` y `.php`. Por defecto, incluye nuestra web como ejemplo funcional y es dodnde podrás crear la web a tu gusto. Este directorio está directamente vinculado a los contenedores, por lo que los cambios se aplican instantáneamente.


## Instalación

- Descarga el proyecto y mueve la carpeta al lugar donde quieras tener tu servidor web.
- Entra al directorio del proyecto.
```bash
cd webguard
```
- Antes de iniciar el servidor web, modifica las contraseñas por defecto de MySQL y phpMyAdmin en el archivo `.env`. Para evitar conflictos, usa la misma contraseña en las tres líneas. Si deseas modificar los nombres de los usuarios, recuerda crearlos en el `Dockerfile`.
```bash
./Webguard/.env
____________________________
MYSQL_PASSWORD=TuContraseña         
MYSQL_ROOT_PASSWORD=TuContraseña
PMA_PASSWORD=TuContraseña
```
- Ejecuta el script **restart.sh**. Recuerda usar el usuario **root** para ejecutarlo. Este mismo comando sirve para reiniciar el servidor web, mantener todo actualizado y aplicar nuevas configuraciones.
```bash
./restart.sh
bash restart.sh
```
- Listo. Ya puedes acceder a la web por defecto buscando la IP de tu máquina o con `localhost` en un navegador. Si entras por el puerto `8080`, accederás a la web de phpMyAdmin para configurar las bases de datos.

Los archivos de la web se encuentran todos en el directorio `www`. Siéntete libre de usar esos archivos para hacer tu web, basándote en los ejemplos o no. Sin embargo, primero recomendamos que sigas esta breve guía de uso para hacer que tu web cifre las comunicaciones por HTTPS y para saber cómo añadir funciones nuevas en tu web utilizando nuestras herramientas de seguridad.

Recuerda que el puerto `8080` **no** debe ser accesible para los usuarios, por lo que no recomendamos abrirlo a Internet.

## Guía de uso

### Configuración de HTTPS

Para hacer que tu web funcione por HTTPS, primero obtén un certificado SSL. Hay muchas formas de hacerlo dependiendo de tus necesidades.

Luego de obtener los certificados, dirígete al archivo `docker-compose.yml` y descomenta la siguiente línea para abrir el puerto 443 en el contenedor:
```
    www:
        build: 
            context: .
            args:
                PHP_MEMORY_LIMIT: ${PHP_MEMORY_LIMIT}
                PHP_MAX_EXECUTION_TIME: ${PHP_MAX_EXECUTION_TIME}
                PHP_UPLOAD_MAX_FILESIZE: ${PHP_UPLOAD_MAX_FILESIZE}
                PHP_POST_MAX_SIZE: ${PHP_POST_MAX_SIZE}
        restart: unless-stopped
        environment:
            MYSQL_HOST: ${MYSQL_HOST}
            MYSQL_DATABASE: ${MYSQL_DATABASE}
            MYSQL_USER: ${MYSQL_USER}
            MYSQL_PASSWORD: ${MYSQL_PASSWORD}
        ports: 
            - "0.0.0.0:${APACHE_PORT}:80"
#            - "0.0.0.0:443:443"
        volumes:
            - ./www:/var/www/html:ro
        depends_on:
            - db
        networks:
            - frontend
            - backend
        healthcheck:
            test: ["CMD", "curl", "-f", "http://localhost"]
            interval: 10s
            timeout: 5s
            retries: 5

```

Luego, entra en el archivo `./conf/apache2.conf` y dirígete al final del mismo, donde encontrarás varias líneas comentadas. Descoméntalas y especifica tu dominio y tu certificado donde se indique. Es importante poner el certificado completo en lugar del path al archivo que lo contiene.

```
# <IfModule mod_ssl.c>
#     <VirtualHost *:443>
#         ServerAdmin <tu dominio>
#         DocumentRoot /var/www/html
#         ServerName <tu dominio>
# 
#         SSLEngine on
#         SSLCertificate <tu certificado>
#         SSLCertificateKey <tu certificado>
#         SSLCertificateChain <tu certificado>
# 
#         <Directory /var/www/html>
#             AllowOverride All
#             Require all granted
#         </Directory>
#     </VirtualHost>
#     <VirtualHost *:80>
#         ServerName <tu dominio>
#         Redirect permanent / https://<tu dominio>/
#     </VirtualHost>
# </IfModule>
```
El apartado comentado de `<VirtualHost *:80>` sirve para redirigir las comunicaciones que lleguen por el puerto 80 al 443, para que usen HTTPS. Si lo prefieres, puedes quitarlo y desactivar el puerto al completo comentando la línea `- "0.0.0.0:${APACHE_PORT}:80"` en el paso anterior.

### Cómo usar el login y registro por defecto

Una vez instalado, si has modificado la contraseña de phpMyAdmin y de SQL, el login y registro por defecto que incluye el proyecto no funcionarán. Debes modificar el inicio de ambos archivos (`login.php` y `register.php`), indicando las credenciales correctas de tu instalación.

```bash
// CONFIGURACIÓN DE BASE DE DATOS
$host = 'db';       // CAMBIAR: IP o hostname del servidor MySQL
$dbname = 'ymr';    // CAMBIAR: nombre de la base de datos
$user = 'user';     // CAMBIAR: usuario de la base de datos
$pass = 'webguard123'; // CAMBIAR: contraseña del usuario
```
Podrás ver los usuarios que se registren en phpMyAdmin. Por defecto, son necesarios los campos `username`, `email` y `password`. Las contraseñas se guardan de forma encriptada y se utiliza el nombre de usuario como clave.

### Configurar subida de archivos

Si deseas configurar un sistema de subida de archivos, debes usar el archivo `upload.php` para sanitizar los archivos que se suban. Para ello, puedes usar el siguiente ejemplo en un HTML:
```html
<form action="upload/upload.php" method="POST" enctype="multipart/form-data">
  <input type="text" name="dir" placeholder="Directory Name" required>
  <input type="text" name="filename" placeholder="File Name" required>
  <input type="file" name="file" required>
</form>
```

## contribuciones

Este ha sido un proyecto de estudiantes del Instituto Tecnológico de Barcelona (ITB). Para crear el proyecto nos hemos basado en los datos obtenidos después de implementar un honeypot y analizar qué es lo que más atención le daban los atacantes para poder defendernos contra estos.

Autores: Rafael Guiotto Silva, Yeray Lopéz Lino y Marc Guerra Hernández
