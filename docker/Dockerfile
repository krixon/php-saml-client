FROM php:7.2-cli-alpine3.8

ARG UID

# Create a user account manually rather than using:
# adduser -D -u $UID -h /home/dev dev
# This is necessary to support $UIDs > 256000 on alpine/busybox.
RUN echo "dev:x:$UID:$UID::/home/dev:" >> /etc/passwd \
    && echo "dev:!:$(($(date +%s) / 60 / 60 / 24)):0:99999:7:::" >> /etc/shadow \
    && echo "dev:x:$UID:" >> /etc/group \
    && mkdir /home/dev && chown dev: /home/dev

RUN apk add --no-cache libmcrypt libmcrypt-dev libxml2-dev openssl curl g++ make autoconf

RUN pecl install xdebug \
    && { \
        echo 'xdebug.remote_enable=1'; \
        echo 'xdebug.remote_autostart=0'; \
        echo 'xdebug.remote_connect_back=0'; \
        echo 'xdebug.idekey = PHPSTORM'; \
        } > /usr/local/etc/php/conf.d/99-docker-php-ext-xdebug.ini \
    && docker-php-ext-enable xdebug --ini-name 99-docker-php-ext-xdebug.ini \
    && docker-php-ext-install -j$(getconf _NPROCESSORS_ONLN) xml

RUN apk del g++ make autoconf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

WORKDIR /app

USER dev

# Update PATH to include project binaries.
ENV PATH="/app/vendor/bin:${PATH}"
