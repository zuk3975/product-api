Feature: Product seeding
    In order to have products available in the application
    As an administrator
    I want to seed products from a file

    Scenario: Seeding products from a valid seed file
        When I seed the products
        Then the following products should be created in the database:
            | sku    | name                            | category | price |
            | 000001 | BV Lean leather ankle boots     | boots    | 89000 |
            | 000002 | BV Lean leather ankle boots     | boots    | 99000 |
            | 000003 | Ashlington leather ankle boots  | boots    | 71000 |
            | 000004 | Naima embellished suede sandals | sandals  | 79500 |
            | 000005 | Nathane leather sneakers        | sneakers | 59000 |
