@apps
Feature: Config API
  In order to manipulate app configs
  As an API client
  I should be able to request the Config API

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
  Scenario: Gets an existing app config
    Given I send a GET request to "/apps/my-app/configs/same"
    Then the response code should be 200
    And the response should contain json:
    """
      {
        "key": "same",
        "value": 1
      }
    """

  @api
  Scenario: List configs of an app
    Given I send a GET request to "/apps/my-app/configs"
    Then the response code should be 200
    And the response should contain json:
    """
      [
        {
          "key": "different",
          "value": "yes",
          "app": {
            "name": "my-app",
            "id": 1
          }
        },
        {
          "key": "not_in_myapp3",
          "value": 1337,
          "app": {
            "name": "my-app",
            "id": 1
          }
        },
        {
          "key": "same",
          "value": 1,
          "app": {
            "name": "my-app",
            "id": 1
          }
        }
      ]
    """

  @api
  Scenario: List the configs from an unkwown app
    Given I send a GET request to "/apps/9999/configs"
    Then the response code should be 404

  @api
  Scenario: Gets a config from an unkwown app
    Given I send a GET request to "/apps/9999/configs/same"
    Then the response code should be 404

  @api
  Scenario: Gets an unknwon config from an existing app
    Given I send a GET request to "/apps/my-app/configs/unkown"
    Then the response code should be 404

  @api
  Scenario: Gets an app config competitors
    Given I send a GET request to "/apps/my-app/configs/different/diff"
    Then the response code should be 200
    And the response should contain json:
    """
    {
      "reference": {
        "key": "different",
        "value": "yes",
        "app": {
          "name": "my-app",
          "id": 1
        }
      },
      "competitors": [
        {
          "key": "different",
          "value": "no",
          "app": {
            "name": "my-app-3",
            "id": 3
          }
        }
      ]
    }
    """
