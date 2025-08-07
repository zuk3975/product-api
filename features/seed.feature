Feature: Data seeding
    In order to have products and discounts available in the application
    As an administrator
    I want to create products and discounts from a file

    @clear-database
    Scenario: Seeding products from a valid seed file
        When I seed the products
        Then the following products should be created in the database:
            | sku    | name                            | category | price |
            | 000001 | BV Lean leather ankle boots     | boots    | 89000 |
            | 000002 | BV Lean leather ankle boots     | boots    | 99000 |
            | 000003 | Ashlington leather ankle boots  | boots    | 71000 |
            | 000004 | Naima embellished suede sandals | sandals  | 79500 |
            | 000005 | Nathane leather sneakers        | sneakers | 59000 |

    Scenario: Seeding discounts from a valid seed file
        Given the following products exists in the database:
            | sku    | name                            | category | price |
            | 000001 | BV Lean leather ankle boots     | boots    | 89000 |
            | 000002 | BV Lean leather ankle boots     | boots    | 99000 |
            | 000003 | Ashlington leather ankle boots  | boots    | 71000 |
            | 000004 | Naima embellished suede sandals | sandals  | 79500 |
            | 000005 | Nathane leather sneakers        | sneakers | 59000 |
        When I seed the discounts
        Then the following discounts should exist in the database:
            | product | category | percent |
            | 000003  |          | 15      |
            |         | boots    | 30      |
