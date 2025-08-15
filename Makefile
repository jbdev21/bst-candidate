setup: ## install deps and seed
	composer install
	cp -n .env.example .env || true
	php artisan key:generate
	npm i
	php artisan migrate --seed

run:
	php artisan serve

fresh:
	php artisan migrate:fresh --seed

test:
	php artisan test
