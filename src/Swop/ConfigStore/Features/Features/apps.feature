@apps
Feature: Apps API
  In order to manipulate apps
  As an API client
  I should be able to request the Apps API

  Background:
    Given the following groups:
      | name         |
      | My app group |
    Given the following apps:
      | name     | group_id | description    | config_items                                           |
      | my-app   | 1        | null           | {"same": 1, "different": "yes", "not_in_myapp3": 1337} |
      | my-app-2 | null     | My description | {}                                                     |
      | my-app-3 | 1        | My description | {"not_in_myapp": 1337, "same": 1, "different": "no"}   |
    Given I set header "accept" with value "application/json"

  @api
  Scenario: Gets an existing application
    Given I send a GET request to "/apps/my-app"
    Then the response code should be 200
    And the response should contain json:
    """
      {
        "id": 1,
        "name": "my-app",
        "description": null,
        "group": {
          "id": 1,
          "name": "My app group"
        },
        "config_items": [
          {
            "key": "different",
            "value": "yes"
          },
          {
            "key": "not_in_myapp3",
            "value": 1337
          },
          {
            "key": "same",
            "value": 1
          }
        ]
      }
    """

  @api
  Scenario: List apps
    Given I send a GET request to "/apps"
    Then the response code should be 200
    And the response should contain json:
    """
      [
        {
          "id": 1,
          "name": "my-app",
          "description": null,
          "group": {
            "id": 1,
            "name": "My app group"
          },
          "config_items": [
            {
              "key": "different",
              "value": "yes"
            },
            {
              "key": "not_in_myapp3",
              "value": 1337
            },
            {
              "key": "same",
              "value": 1
            }
          ]
        },
        {
          "id": 2,
          "name": "my-app-2",
          "description": "My description",
          "group": null,
          "config_items": []
        },
        {
          "id": 3,
          "name": "my-app-3",
          "description": "My description",
          "group": {
            "id": 1,
            "name": "My app group"
          },
          "config_items": [
            {
              "key": "different",
              "value": "no"
            },
            {
              "key": "not_in_myapp",
              "value": 1337
            },
            {
              "key": "same",
              "value": 1
            }
          ]
        }
      ]
    """

  @api
  Scenario: Gets an unkwown app
    Given I send a GET request to "/apps/9999"
    Then the response code should be 404

  @api
  Scenario: Gets a diff between two app configurations
    Given I send a GET request to "/apps/my-app/diff/my-app-3"
    Then the response code should be 200
    And the response should contain json:
    """
      {
        "app_left": {
          "id": 1,
          "name": "my-app",
          "config_items": [
            {
              "key": "different",
              "value": "yes"
            },
            {
              "key": "not_in_myapp3",
              "value": 1337
            },
            {
              "key": "same",
              "value": 1
            }
          ]
        },
        "app_right": {
          "id": 3,
          "name": "my-app-3",
          "config_items": [
            {
              "key": "different",
              "value": "no"
            },
            {
              "key": "not_in_myapp",
              "value": 1337
            },
            {
              "key": "same",
              "value": 1
            }
          ]
        },
        "keys_union": ["different", "not_in_myapp", "not_in_myapp3", "same"],
        "identical": ["same"],
        "different": ["different"],
        "missing_left": ["not_in_myapp"],
        "missing_right": ["not_in_myapp3"]
      }
    """

  @api
  Scenario: Gets a diff between two app which aren't in the same group
    Given I send a GET request to "/apps/my-app/diff/my-app-2"
    Then the response code should be 400
