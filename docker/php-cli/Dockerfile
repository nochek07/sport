FROM php:7.2.32-cli

ARG INSTALL_XDEBUG
ARG INSTALL_COMPOSER

RUN apt-get update && apt-get install -y wget git unzip

# XDebug
RUN if [ -n "$INSTALL_XDEBUG" ] && [ "$INSTALL_XDEBUG" = "true" ]; then \
        pecl install xdebug-2.9.6 \
        && docker-php-ext-enable xdebug; \
    fi

# Install Composer
RUN if [ -n "$INSTALL_COMPOSER" ] && [ "$INSTALL_COMPOSER" = "true" ]; then \
        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
            && composer --version; \
    fi

# Install PDO
RUN docker-php-ext-install pdo pdo_mysql