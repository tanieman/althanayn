FROM --platform=linux/amd64 php:8.2-apache-bookworm

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

# أدوات البناء + Composer (بدون COPY --from=composer لتفادي فشل سحب صورة إضافية)
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    git \
    unzip \
  && rm -rf /var/lib/apt/lists/* \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
  && composer --version

# mod_rewrite + السماح بقواعد .htaccess
RUN a2enmod rewrite \
  && sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

COPY . .

COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
# إزالة CRLF (ويندوز) حتى لا يفشل التشغيل على Linux
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
  && chmod +x /usr/local/bin/docker-entrypoint.sh \
  && chown -R www-data:www-data /var/www/html

EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
