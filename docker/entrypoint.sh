#!/bin/sh
set -e
PORT="${PORT:-80}"

# Render يمرّر PORT ديناميكياً؛ Apache يجب أن يستمع على نفس المنفذ
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
if [ -f /etc/apache2/sites-available/000-default.conf ]; then
  sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
fi

exec apache2-foreground
