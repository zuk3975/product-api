# Product api (demo project)

## Description
REST API endpoint providing list of products and calculated discounts if any.

- Use Doctrine ORM for persistence
- Use API Platform for API - probably overkill (because there is only one endpoint and even that one is custom), but wasn't time consuming
- Architecture quite simple and should be self-explanatory. Namespaces organized by type. In small applications like that, domain driven approach would be an overkill.
- Behat tests cover integration with API and persistence, unit tests some of the services that are more isolated.
- Dedicated Docker container services used for development and testing
- Discounts are separate entities, contains target type and id. Target type for now can be "product" or "category", while target id references the related entity by id. This allows to extend behavior easy if needed and seems quite simple (if let's say we need to add discount to brands or attributes even)
- When discounts are applied, they are all first fetched, before looping through products. This is assuming that there won't ever be alot of discount entities (not thousands). In case there is, that part should be overwritten with more complex sql query, fetching only discounts related tou found products.

### How to run

1. Run `cp .env.example .env` - copy .env.example to .env and adjust it to your needs if needed (change ports, credentials etc.)
2. **Run `make run` and wait until docker services are running**
3. Visit `http://localhost:8080/api` for interactive API documentation - You can see available endpoints and test them by clicking "Try it out" (or just use postman)
4. Run `make help` to see other available commands
5. Run `make test` to run tests
