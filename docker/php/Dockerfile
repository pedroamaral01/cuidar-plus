FROM php:8.4-fpm-bookworm

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libicu-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        bcmath \
        intl \
        zip \
        gd \
        exif \
        pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Usuário com o mesmo UID/GID do host, para que os arquivos gerados dentro do
# container (cache, logs, storage) continuem editáveis fora dele.
RUN groupadd -g "${GID}" cuidar \
    && useradd -u "${UID}" -g cuidar -m -s /bin/bash cuidar

COPY docker/php/php.ini /usr/local/etc/php/conf.d/cuidar.ini

WORKDIR /var/www/html

USER cuidar

EXPOSE 9000

CMD ["php-fpm"]
