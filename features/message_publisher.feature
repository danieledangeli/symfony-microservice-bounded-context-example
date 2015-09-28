Feature: message publisher
  As an Publisher
  I need to be able to publish message on channel

  Scenario: A publisher can publish a new message on an existing channel, if authorized
    Given a publisher with id "899"
    And exists a "open" channel with id "3333"
    And the publisher is authorized to publish message on the channel
    When the publisher write the message "Hi guys"
    Then a new message will be created on the channel

  Scenario: A publisher cannot publish a message on an existing channel, if not authorized
    Given a publisher with id "899"
    And exists a "open" channel with id "3333"
    And the publisher is not authorized to publish message on the channel
    When the publisher write the message "Hi guys"
    Then the publisher is informed that is not authorized
    And no new messages will be added on that channel

  Scenario: A publisher cannot publish a new message on a not existing channel
    Given a publisher with id "899"
    And  a channel with id "3333" doesn't exists
    When the publisher write the message "Hi guys"
    Then the publisher is informed that the channel doesn't exists

  Scenario: A publisher can delete a message in an open channel
    Given a publisher with id "899"
    And exists a "open" channel with id "3333"
    And a message with id "1ca188d6-b1b9-4f4e-9b2d-67a68f409cac" on the channel "3333"
    And  the publisher is the owner of the message
    When the publisher delete the message
    Then the message will be deleted from the channel

  Scenario: A publisher cannot delete a message in a channel, if he's not the owner
    Given a publisher with id "899"
    And a message with id "1ca188d6-b1b9-4f4e-9b2d-67a68f409cac" on the channel "3333"
    And  the publisher is not the owner of the message
    When the publisher delete the message
    Then the user is informed that is not the owner of that message
    And the message will not be deleted from the channel

  Scenario: A publisher cannot publish a message into a closed channel
    Given a publisher with id "899"
    And exists a "closed" channel with id "3333"
    And the publisher is authorized to publish message on the channel
    When the publisher write the message "Hi guys"
    Then the publisher is informed that is not possible to perform action on a closed channel

  Scenario: A publisher cannot delete a message in a closed channel
    Given a publisher with id "899"
    And a message with id "1ca188d6-b1b9-4f4e-9b2d-67a68f409cac" on the channel "3333"
    And  the publisher is the owner of the message
    When the publisher delete the message
    Then the publisher is informed that is not possible to perform action on a closed channel