FROM php:7.4-fpm

ARG INSTALL_XDEBUG
ARG INSTALL_COMPOSER
ARG INSTALL_BINARY_SYMFONY
ARG PUID
ARG PGID
ARG USER

RUN apt-get update && apt-get install -y wget git unzip libicu-dev

RUN docker-php-ext-configure intl && docker-php-ext-install intl

# XDebug
RUN if [ -n "$INSTALL_XDEBUG" ] && [ "$INSTALL_XDEBUG" = "true" ]; then \
        pecl install xdebug-3.0.3 \
        && docker-php-ext-enable xdebug; \
    fi

# Install Composer
RUN if [ -n "$INSTALL_COMPOSER" ] && [ "$INSTALL_COMPOSER" = "true" ]; then \
        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
            && composer --version; \
    fi

# Install binary symfony
RUN if [ -n "$INSTALL_BINARY_SYMFONY" ] && [ "$INSTALL_BINARY_SYMFONY" = "true" ]; then \
        curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
        && apt-get install symfony-cli; \
    fi

# Install PDO
RUN docker-php-ext-install pdo pdo_mysql

RUN addgroup --gid ${PGID} ${USER} || true && \
    adduser --uid ${PUID} --gid ${PGID} --gecos '' --disabled-password ${USER} || true