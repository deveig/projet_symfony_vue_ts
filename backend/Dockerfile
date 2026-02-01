FROM php:8.3.30RC1-fpm-alpine3.23
ARG DB_APP_SECRET
ARG DB_URL
ARG DB_MESSENGER
ENV DB_APP_SECRET=$DB_APP_SECRET
ENV DB_URL=$DB_URL
ENV DB_MESSENGER=$DB_MESSENGER
WORKDIR /usr/local/etc/php/
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN apk add curl
RUN ( curl -sSLf https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - || echo 'return 1' ) | sh -s \ pdo_mysql
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
WORKDIR /var/www/
RUN mkdir project
WORKDIR /var/www/project/
COPY --exclude=nginx . .
RUN echo "APP_SECRET=$DB_APP_SECRET" > .env
RUN echo "DATABASE_URL=$DB_URL" >> .env
RUN echo "MESSENGER_TRANSPORT_DSN=$DB_MESSENGER" >> .env
RUN composer require symfony/requirements-checker
RUN composer dump-env prod
RUN APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
CMD ["php-fpm"]
