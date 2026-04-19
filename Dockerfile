FROM php:8.2-apache-bookworm

WORKDIR /var/www/html

# mod_rewrite + السماح بقواعد .htaccess
RUN a2enmod rewrite \
  && sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

COPY . .

COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
  && chown -R www-data:www-data /var/www/html

EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
