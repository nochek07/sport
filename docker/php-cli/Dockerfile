FROM php:7.4-cli

ARG INSTALL_XDEBUG
ARG PUID
ARG PGID
ARG USER

RUN apt-get update && apt-get install -y wget git unzip

# XDebug
RUN if [ -n "$INSTALL_XDEBUG" ] && [ "$INSTALL_XDEBUG" = "true" ]; then \
        pecl install xdebug-3.0.3 \
        && docker-php-ext-enable xdebug; \
    fi

# Install PDO
RUN docker-php-ext-install pdo pdo_mysql

RUN addgroup --gid ${PGID} ${USER} || true && \
    adduser --uid ${PUID} --gid ${PGID} --gecos '' --disabled-password ${USER} || true