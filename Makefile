up:
	docker compose up -d --build
	docker compose exec php composer install

down:
	docker compose down -v

start:
	docker compose start

stop:
	docker compose stop

bash:
	docker compose exec php bash

test:
	docker compose exec php php bin/phpunit

migrate:
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

cs:
	docker compose exec php vendor/bin/php-cs-fixer fix

stan:
	docker compose exec php vendor/bin/phpstan analyse
