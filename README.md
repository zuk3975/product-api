# Product api (demo project)

## Description
REST API endpoint providing list of products and calculated discounts if any.
Done in Symfony framework, using Doctrine ORM API Platform and docker.

### How to run

1. Copy .env.example to .env and adjust it to your needs if needed
2. **Run `make run` and wait until docker services are running**
3. Visit `http://localhost/api` for interactive API documentation - You can see available endpoints and test them by clicking "Try it out" (or just use postman)
4. Run `make help` to see other available commands
5. Run `make test` to run tests
