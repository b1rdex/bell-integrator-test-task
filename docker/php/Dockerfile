FROM php:7.4-fpm

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
COPY --from=composer:2 /usr/bin/composer /usr/bin/

ADD https://raw.githubusercontent.com/vishnubob/wait-for-it/81b1373f17855a4dc21156cfe1694c31d7d1792e/wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it

RUN apt-get update && apt-get install -yy unzip && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions opcache apcu pdo_mysql zip xdebug

WORKDIR /var/www

CMD composer install ; wait-for-it db:3306 -- bin/console doctrine:migrations:migrate ; php-fpm
