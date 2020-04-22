FROM node AS webpack

COPY package.json package-lock.json webpack.config.js /app/

WORKDIR /app

RUN npm install
RUN webpack --mode production

FROM composer AS composer

COPY composer.* /app/

WORKDIR /app

RUN composer install --no-dev --ignore-platform-reqs && \
    rm /app/composer.json /app/composer.lock


FROM php:7.3-apache

RUN sed -ri -e 's!/var/www/html!/app/httpdocs!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!/app/httpdocs!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    echo "ServerTokens Prod" > /etc/apache2/conf-enabled/z-server-tokens.conf && \
    a2enmod rewrite && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    mkdir -p /app/twig-cache && \
    chown www-data: /app/twig-cache

COPY --from=composer /app/vendor /app/vendor

COPY bootstrap.php /app/
COPY httpdocs /app/httpdocs
COPY src /app/src