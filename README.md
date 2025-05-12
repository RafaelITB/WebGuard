# WebGuard - Proyecto Web Seguro con Docker

WebGuard es un proyecto que configura un entorno de desarrollo web seguro utilizando Docker. Permite a los usuarios crear su propia página web sin preocuparse demasiado por la seguridad, ya que se implementan medidas de protección preconfiguradas.

## Tabla de Contenidos

- [Descripción](#descripción)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Guia de uso](#Guia-de-uso)
- [Contribuciones](#contribuciones)


## Descripción

WebGuard es una solución completa de contenedores Docker para alojar y gestionar sitios web con una configuración de seguridad avanzada. El proyecto consta de tres contenedores:

- **Apache**: Servidor web para alojar la aplicación.
- **MySQL**: Base de datos para gestionar el contenido de la página web.
- **PhpMyAdmin**: Interfaz web para gestionar la base de datos MySQL de manera sencilla.

Webguard por defecto te protege de:

- **Exposición de información**: Toda la información que suele ser publica por defecto como el software utilizado y sus versiones y archivos de configuración, en webguard, está todo ocultado
- **Vulnerabilidades de software**: Gracias a la estructura en docker basta con reinciar las maquinas para actualizar todo el software a las ultimas versiones
- **XSS/Inyecciones-SQL/Archivos maliciosos**: Webguard está precofigurado para pasar por un filtro todo lo que se suba a la web y bloquear todo lo malintencionado, eres libre de hacer formularios, 
- **DDOS y escaneos de web**: Usando herramientas preconfiguradas, webguard bloqueará cualquier ip que haga muchas solicitudes malintencionadas, puedes cambiar cuantas solicitudes y cuanto dura el bloqueo de ip a tu gusto
- **Comprometer la maquina fisica**: De nuevo, al ser un Docker, incluso en el caso de que un atacante pueda comprometer la web, no podrá acceder a la maquina donde está hosteado el web server 
## Requisitos

Docker y Docker compose instalados

## Instalación

- Descarga el proyecto y mueve la carpeta al lugar donde quieras tener tu servidor web
- Entra al directorio
```bash
cd webguard
```
- Antes de iniciar el webserver modifica las contraseñas por defecto de mysql y phpmyadmin en el archivo ```.env```, para evitar conflictos usa la misma contraseña en las 3 lineas, si quieres modificar los nombres recuerda crear los usuarios en el Dockerfile.
```bash
./Webguard/.env
____________________________
MYSQL_PASSWORD=TuContraseña         
MYSQL_ROOT_PASSWORD=TuContraseña
PMA_PASSWORD=TuContraseña
```
- Ejecuta el script **restart.sh**, recuerda usar el usuario **root** para ejecutarlo. Este mismo comando sirve para reinicar el webserver para mantener todo actualizado y aplicar nuevas configuraciones
```bash
bash restart.sh
```
- listo. Ya puedes acceder a la web por defecto buscando la ip de tu maquina o con ```localhost``` en un navegador, y si entras por el puerto ```:8080``` accederás a la web de PhpMyadmin para configurar las bases de datos

Los archivos de la web se encuentran todos en ```www``` sé libre de usar hacer tu web ahí mismo. Pero primero, recomendamos que sigas la guia de uso para hacer que tu web cifre las comunicaciones por https y para que sepas bien como añadir funciones nuevas en tu web usando nuestras herramientas de seguridad.

Recuerda que el puerto ```8080``` **No** debe ser accesible a los usuarios, por lo que no recomendamos abrirlo a internet. 

## Guia de uso


