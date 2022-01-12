FROM mctekk/phalconphp:7.4-debian

WORKDIR /app

ENV USER_WRITABLE=www-data
#Copy source code
COPY . ./

RUN composer clearcache
RUN composer dump-autoload
RUN composer install
RUN composer upgrade

RUN chown -R $USER_WRITABLE /app/storage/logs /app/storage/cache

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN chmod -R 777 .

EXPOSE 9000
