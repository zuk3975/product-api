CONTAINER_NAME=product-api-php
.PHONY: ssh help

.DEFAULT_GOAL := help

help:
	@echo "Available make commands:"
	@echo "  make ssh - SSH into the php container"
	@echo "  make seed - Seed application database with required data"
	@echo "  make test - Run all tests"

ssh:
	docker exec -it $(CONTAINER_NAME) /bin/bash

seed:
	docker exec -it $(CONTAINER_NAME) php bin/console app:seed

test:
	docker exec -it $(CONTAINER_NAME) php bin/phpunit
