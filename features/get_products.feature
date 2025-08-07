Feature: Get products
    As an api user
    I want to retrieve a list of products

    @reset-database
    Scenario: Get products with various filters
        Given the following products exists in the database:
            | sku    | name                            | category | price |
            | 000001 | BV Lean leather ankle boots     | boots    | 89000 |
            | 000002 | BV Lean leather ankle boots     | boots    | 99000 |
            | 000003 | Ashlington leather ankle boots  | boots    | 71000 |
            | 000004 | Naima embellished suede sandals | sandals  | 79500 |
            | 000005 | Nathane leather sneakers        | sneakers | 59000 |
        When I get products with filters: "limit=3" I should see the following results:
            | sku    | name                            | category | price_original | price_final | discount_percentage | currency |
            | 000001 | BV Lean leather ankle boots     | boots    | 89000          | 62300       | 30%                 | EUR      |
            | 000002 | BV Lean leather ankle boots     | boots    | 99000          | 69300       | 30%                 | EUR      |
            | 000003 | Ashlington leather ankle boots  | boots    | 71000          | 49700       | 30%                 | EUR      |
        When I get products without filters I should see the following results:
            | sku    | name                            | category | price_original | price_final | discount_percentage | currency |
            | 000001 | BV Lean leather ankle boots     | boots    | 89000          | 62300       | 30%                 | EUR      |
            | 000002 | BV Lean leather ankle boots     | boots    | 99000          | 69300       | 30%                 | EUR      |
            | 000003 | Ashlington leather ankle boots  | boots    | 71000          | 49700       | 30%                 | EUR      |
            | 000004 | Naima embellished suede sandals | sandals  | 79500          | 79500       |                     | EUR      |
            | 000005 | Nathane leather sneakers        | sneakers | 59000          | 59000       |                     | EUR      |
        When I get products with filters: "priceLessThan=71000" I should see the following results:
            | sku    | name                            | category | price_original | price_final | discount_percentage | currency |
            | 000003 | Ashlington leather ankle boots  | boots    | 71000          | 49700       | 30%                 | EUR      |
            | 000005 | Nathane leather sneakers        | sneakers | 59000          | 59000       |                     | EUR      |
        When I get products with filters: "category=sneakers" I should see the following results:
            | sku    | name                            | category | price_original | price_final | discount_percentage | currency |
            | 000005 | Nathane leather sneakers        | sneakers | 59000          | 59000       |                     | EUR      |
