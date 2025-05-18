FROM php:8.2-apache

ARG PHP_MEMORY_LIMIT=256M
ARG PHP_MAX_EXECUTION_TIME=30
ARG PHP_UPLOAD_MAX_FILESIZE=20M
ARG PHP_POST_MAX_SIZE=20M

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpng-dev \
        libzip-dev \
        zlib1g-dev \
        libonig-dev \
        curl \
        libapache2-mod-security2 \
        modsecurity-crs \
        libapache2-mod-evasive \
        nano \
        sendmail \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libxpm-dev \
        libicu-dev \
        libxml2-dev \
        libmagic1 \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        mysqli \
        pdo \
        pdo_mysql \
        zip \
        fileinfo \
        mbstring \
        gd

# Configure PHP
RUN echo "memory_limit = ${PHP_MEMORY_LIMIT}" >> /usr/local/etc/php/conf.d/docker-php-memory-limit.ini \
    && echo "max_execution_time = ${PHP_MAX_EXECUTION_TIME}" >> /usr/local/etc/php/conf.d/docker-php-max-execution-time.ini \
    && echo "upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE}" >> /usr/local/etc/php/conf.d/docker-php-upload-max-filesize.ini \
    && echo "post_max_size = ${PHP_POST_MAX_SIZE}" >> /usr/local/etc/php/conf.d/docker-php-post-max-size.ini

RUN mkdir -p /var/lib/php/sessions && \
    chown -R www-data:www-data /var/lib/php/sessions && \
    chmod 700 /var/lib/php/sessions

# Configure Apache
RUN a2enmod rewrite headers ssl evasive security2  

# Configuración de ModSecurity
COPY conf/security.conf /etc/apache2/conf-available/security.conf
COPY conf/security2.conf /etc/apache2/mods-available/security2.conf
COPY conf/apache2.conf /etc/apache2/apache2.conf

# Copiar configuración de evasive
COPY conf/mod-evasive.conf /etc/apache2/mods-available/evasive.conf

# Enlace del CRS
RUN ln -s /usr/share/modsecurity-crs/crs-setup.conf.example /etc/modsecurity/crs-setup.conf

# Activa ModSecurity
RUN sed -i 's/SecRuleEngine DetectionOnly/SecRuleEngine On/' /etc/modsecurity/modsecurity.conf || true

RUN a2enconf security

# Oculta la versión de PHP
RUN sed -i 's/expose_php = On/expose_php = Off/' $PHP_INI_DIR/php.ini
RUN echo "extension=fileinfo" >> $PHP_INI_DIR/php.ini


# Configuración adicional para PHP
RUN echo "session.cookie_httponly = 1" >> $PHP_INI_DIR/php.ini \
    && echo "session.cookie_secure = 1" >> $PHP_INI_DIR/php.ini

# Crear directorio para logs de evasive
RUN mkdir -p /var/log/mod_evasive && chmod 777 /var/log/mod_evasive

# no-root user
RUN useradd -r -u 1000 -g www-data webuser

# PHP logs
RUN mkdir -p /var/log/php \
    && chown -R www-data:www-data /var/log/php \
    && chmod 755 /var/log/php

# file permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 750 /var/www/html

USER webuser

HEALTHCHECK --interval=30s --timeout=3s --retries=3 \
    CMD curl -f http://localhost/ || exit 1
