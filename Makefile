CONTAINER_NAME=product-api-php
TEST_CONTAINER_NAME=product-api-php_test
.PHONY: ssh help

.DEFAULT_GOAL := help

help:
	@echo "Available make commands:"
	@echo "  make run - Run the application with both dev and test containers"
	@echo "  make seed - Seed application database with required data"
	@echo "  make test - Run all tests"

run:
	docker compose -f compose.yaml -f compose.test.yaml up --build && \
	docker exec -it $(CONTAINER_NAME) sh -c "\
		php bin/console doctrine:database:create --if-not-exists \
	"

seed:
	docker exec -it $(CONTAINER_NAME) php bin/console app:seed

test:
	docker exec -it $(TEST_CONTAINER_NAME) sh -c "\
		php bin/phpunit && \
		php vendor/bin/behat \
	"
