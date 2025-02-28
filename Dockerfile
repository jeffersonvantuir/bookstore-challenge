FROM php:8.3-fpm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    apt update && apt install -y \
    procps \
    acl \
    apt-transport-https \
    build-essential \
    ca-certificates \
    coreutils \
    curl \
    file \
    gettext \
    git \
    cron \
    wget \
    zip \
    unzip \
    libssl-dev && \
    install-php-extensions \
    sockets \
    intl \
    sockets \
    opcache \
    mysqli \
    pdo_mysql \
    intl \
    pdo_pgsql \
    xsl \
    zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && ln -s $(composer config --global home) /root/composer && \
    apt-get clean && \
    apt-get autoremove && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/* && \
    rm -rf /var/tmp/* && \
    rm -rf /var/cache/apt/* && \
    rm -rf /var/cache/debconf/* && \
    rm -rf /var/cache/apt/archives/*

ENV ENV="/etc/profile"

WORKDIR /app
