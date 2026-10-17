#!/bin/sh
set -eu

cd /var/www/html

if [ "$(id -u)" = "0" ]; then
  if [ "${HOST_UID:-0}" != "0" ]; then
    sh docker/fix-permissions.sh
    exec gosu "${HOST_UID}:${HOST_GID:-$HOST_UID}" "$0" "$@"
  fi

  chown -R app:app storage bootstrap/cache
  if [ -d vendor ]; then
    chown -R app:app vendor
  fi
  find storage bootstrap/cache -type d -exec chmod 750 {} +
  find storage bootstrap/cache -type f ! -name 'oauth-*.key' -exec chmod 640 {} +
  for key in storage/oauth-private.key storage/oauth-public.key; do
    if [ -f "$key" ]; then
      chmod 600 "$key"
    fi
  done
  exec gosu app:app "$0" "$@"
fi

export DB_CONNECTION="${DB_CONNECTION:-mysql}"
export DB_HOST="${DB_HOST:-mysql}"
export DB_PORT="${DB_PORT:-3306}"
export DB_DATABASE="${DB_DATABASE:-barber_saas}"
export DB_USERNAME="${DB_USERNAME:-barber}"
export DB_PASSWORD="${DB_PASSWORD:-secret}"

if [ ! -f .env ]; then
  cp .env.example .env
fi

composer install --no-interaction --prefer-dist

echo "Waiting for MySQL..."
until php -r 'try { new PDO(sprintf("mysql:host=%s;port=%s;dbname=%s", getenv("DB_HOST"), getenv("DB_PORT"), getenv("DB_DATABASE")), getenv("DB_USERNAME"), getenv("DB_PASSWORD")); } catch (Throwable $e) { fwrite(STDERR, $e->getMessage() . PHP_EOL); exit(1); }'; do
  sleep 2
done

if ! grep -Eq '^APP_KEY=.+' .env; then
  php artisan key:generate --ansi
fi

php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port=8085
