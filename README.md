# WebGuard - Proyecto Web Seguro con Docker

WebGuard es un proyecto que configura un entorno de desarrollo web seguro utilizando Docker. Permite a los usuarios crear su propia página web sin preocuparse demasiado por la seguridad, ya que se implementan medidas de protección preconfiguradas.

## Tabla de Contenidos

- [Descripción](#descripción)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Guia de uso](#Guia-de-uso)
- [Contribuciones](#contribuciones)


## Descripción

WebGuard es una solución completa de contenedores Docker para alojar y gestionar sitios web con una configuración de seguridad avanzada.

Webguard por defecto te protege de:

- **Exposición de información**: Toda la información que suele ser publica por defecto como el software utilizado y sus versiones y archivos de configuración, en webguard, está todo ocultado
- **Vulnerabilidades de software**: Gracias a la estructura de docker basta con reinciar las maquinas para actualizar todo el software a las ultimas versiones
- **XSS/Inyecciones SQL/Archivos maliciosos**: Webguard está precofigurado para pasar por un filtro todo lo que se suba a la web 
-
-

 El proyecto consta de tres contenedores:

- **Apache**: Servidor web para alojar la aplicación.
- **MySQL**: Base de datos para gestionar el contenido de la página web.
- **PhpMyAdmin**: Interfaz web para gestionar la base de datos MySQL de manera sencilla.


## Requisitos

- Docker instalado en tu máquina.
- Docker Compose instalado (si usas `docker-compose.yml` para orquestar los contenedores).

## Instalación

## Guia de uso
