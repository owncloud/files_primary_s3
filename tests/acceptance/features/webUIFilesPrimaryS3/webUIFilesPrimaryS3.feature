@webUI
Feature: Files Primary S3
  As a user
  I want to run tests on s3 storage
  So that I can assert tests run the same on every storages

#  @issue-36803 @issue-files_primary_s3-351
#  Scenario: applicable user is able to share top-level of read-only storage
#    Given these users have been created with default attributes and without skeleton files:
#      | username |
#      | Alice    |
#      | Brian    |
#    And the administrator has enabled the external storage
#    And the administrator has browsed to the admin storage settings page
#    And the administrator has created the local storage mount "local_storage1" from the admin storage settings page
#    And the administrator has added user "Alice" as the applicable user for the last local storage mount from the admin storage settings page
#    And the administrator has enabled read-only for the last created local storage mount using the webUI
#    And the administrator has enabled sharing for the last created local storage mount using the webUI
#    And the user has re-logged in as "Alice" using the webUI
#    When the user shares folder "local_storage1" with user "Brian" using the webUI
#    Then no notification should be displayed on the webUI
#    And as "Brian" folder "local_storage1" should exist

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI

  Scenario: user removes the public link of a file
    Given user "Alice" has been created with default attributes and without skeleton files
    Given user "Alice" has uploaded file "filesForUpload/lorem.txt" to "/lorem.txt"
    And user "Alice" has created a public link share of file "/lorem.txt"
    And user "Alice" has logged in using the webUI
    When the user removes the public link of file "lorem.txt" using the webUI
    Then the public should see an error message "File not found" while accessing last created public link using the webUI