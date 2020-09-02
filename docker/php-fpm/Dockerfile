FROM php:7.2.32-fpm

ARG INSTALL_XDEBUG
ARG INSTALL_COMPOSER
ARG INSTALL_BINARY_SYMFONY

RUN apt-get update && apt-get install -y wget git unzip libicu-dev

RUN docker-php-ext-configure intl && docker-php-ext-install intl

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

# Install binary symfony
RUN if [ -n "$INSTALL_BINARY_SYMFONY" ] && [ "$INSTALL_BINARY_SYMFONY" = "true" ]; then \
        wget https://get.symfony.com/cli/installer -O - | bash \
        && mv /root/.symfony/bin/symfony /usr/local/bin/symfony; \
    fi

# Install PDO
RUN docker-php-ext-install pdo pdo_mysql