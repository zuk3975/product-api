Feature: Get products
    As an api user
    I want to retrieve a list of products

    @reset-database
    Scenario: Get products
        Given the following products exists in the database:
            | sku    | name                            | category | price |
            | 000001 | BV Lean leather ankle boots     | boots    | 89000 |
            | 000002 | BV Lean leather ankle boots     | boots    | 99000 |
            | 000003 | Ashlington leather ankle boots  | boots    | 71000 |
            | 000004 | Naima embellished suede sandals | sandals  | 79500 |
            | 000005 | Nathane leather sneakers        | sneakers | 59000 |
        When I retrieve products without filters limited to 4 I should get following results:
            | sku    | name                            | category | price_original | price_final | discount_percentage | currency |
            | 000001 | BV Lean leather ankle boots     | boots    | 71000          | 71000       | 30%                 | EUR      |
            | 000002 | BV Lean leather ankle boots     | boots    | 71000          | 71000       | 30%                 | EUR      |
            | 000003 | Naima embellished suede sandals | boots    | 71000          | 71000       | 30%                 | EUR      |
            | 000004 | Nathane leather sneakers        | sneakers | 59000          | 59000       | 10%                 | EUR      |
#        When I retrieve products with filters "category: boots, priceLessThan: 80000" I should get following results:
#            | sku    | name                           | category | price_original | price_final | discount_percentage | currency |
#            | 000003 | Ashlington leather ankle boots | boots    | 71000          | 71000       | 30%                 | EUR      |
#            | 000005 | Nathane leather sneakers       | sneakers | 59000          | 59000       | 10%                 | EUR      |
#
#
