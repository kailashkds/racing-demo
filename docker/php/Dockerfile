FROM php:7.4-fpm-alpine

RUN apk add --update libzip-dev curl-dev git &&\
    docker-php-ext-install curl && \
    apk del gcc g++ &&\
    rm -rf /var/cache/apk/*
    
RUN apk update && apk add bash

RUN docker-php-ext-install pdo pdo_mysql json
RUN docker-php-ext-install sockets curl

RUN apk add --no-cache zip libzip-dev
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD . /var/www/demo
COPY setup.sh /usr/local/bin/setup
RUN chmod +x /usr/local/bin/setup

WORKDIR /var/www/demo

RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN export PATH="$HOME/.symfony5/bin:$PATH"

RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
RUN sed -ri -e 's!;date.timezone =!date.timezone = "America\/Los_Angeles"!g' /usr/local/etc/php/php.ini

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/setup"]