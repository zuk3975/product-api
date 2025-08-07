CONTAINER_NAME=product-api-php
TEST_CONTAINER_NAME=product-api-php_test
DB_CONTAINER_NAME=database
TEST_DB_CONTAINER_NAME=database
.PHONY: ssh help

.DEFAULT_GOAL := help

help:
	@echo "Available make commands:"
	@echo "  make run - Run the application with both dev and test containers"
	@echo "  make seed - Seed application database with required data"
	@echo "  make test - Run all tests"

run:
	@echo "🔧 Building and starting containers..."
	docker compose -f compose.yaml -f compose.test.yaml up -d --build

	@echo "⏳ Waiting for database to be healthy..."
	until [ "$$(docker inspect -f '{{.State.Health.Status}}' $(DB_CONTAINER_NAME))" = "healthy" ]; do \
		echo "🔄 Still waiting..."; \
		sleep 2; \
	done

	@echo "✅ Database is healthy. Running Symfony setup..."

	docker exec -it $(CONTAINER_NAME) composer install --prefer-dist --no-progress --no-interaction
	docker exec -it $(CONTAINER_NAME) php bin/console doctrine:database:create --if-not-exists

	@echo "🚀 Project is ready to go!"

seed:
	docker exec -it $(CONTAINER_NAME) php bin/console app:seed

test:
	docker exec -it $(TEST_CONTAINER_NAME) sh -c "\
		php bin/phpunit && \
		php vendor/bin/behat \
	"
