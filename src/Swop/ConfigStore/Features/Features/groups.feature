@groups
Feature: Groups API
  In order to manipulate groups
  As an API client
  I should be able to request the Groups API

  Background:
    Given the following groups:
      | id | name           |
      | 1  | My app group   |
      | 2  | My app group 2 |
    Given the following apps:
      | name     | group_id |
      | my-app   | 1        |
      | my-app-2 | 1        |
    Given I set header "accept" with value "application/json"

  @api
  Scenario: Gets an existing group
    Given I send a GET request to "/groups/1"
    Then the response code should be 200
    And the response should contain json:
    """
      {
        "id": 1,
        "name": "My app group",
        "apps": [
          {
            "id": 1,
            "name": "my-app"
          },
          {
            "id": 2,
            "name": "my-app-2"
          }
        ]
      }
    """

  @api
  Scenario: List groups
    Given I send a GET request to "/groups"
    Then the response code should be 200
    And the response should contain json:
    """
      [
        {
          "id": 1,
          "name": "My app group",
          "apps": [
            {
              "id": 1,
              "name": "my-app"
            },
            {
              "id": 2,
              "name": "my-app-2"
            }
          ]
        },
        {
          "id": 2,
          "name": "My app group 2",
          "apps": []
        }
      ]
    """

  @api
  Scenario: Gets an unkwown group
    Given I send a GET request to "/groups/9999"
    Then the response code should be 404
