.PHONY: build up down restart ps logs shell install composer-dump migrate seed-example bootstrap-example test analyse validate css-dev css-build
build:
	docker compose build
up:
	docker compose up -d
down:
	docker compose down
restart:
	docker compose restart app
ps:
	docker compose ps
logs:
	docker compose logs -f --tail=200 app
shell:
	docker compose exec app bash
install:
	docker compose exec app composer install
	npm install
composer-dump:
	docker compose exec app composer dump-autoload
migrate:
	docker compose exec app vendor/bin/phinx migrate
seed-example:
	docker compose exec app vendor/bin/phinx seed:run -s ExampleNoteSeed
bootstrap-example:
	docker compose exec app vendor/bin/phinx migrate
	docker compose exec app vendor/bin/phinx seed:run -s ExampleNoteSeed
test:
	docker compose exec app composer test
analyse:
	docker compose exec app composer analyse
validate:
	docker compose exec app composer validate-project
css-dev:
	npm run css:dev
css-build:
	npm run css:build
