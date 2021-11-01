@api
Feature: users cannot move (rename) a file to a blacklisted name
  As an administrator
  I want to be able to prevent users from moving (renaming) files to specified file names
  So that I can prevent unwanted file names existing in the cloud storage

  Background:
    Given using new DAV path
    And user "Alice" has been created with default attributes and without skeleton files
    And user "Alice" has uploaded file with content "ownCloud test text file 0" to "textfile0.txt"
    And the administrator has enabled async operations

  Scenario: rename a file to a filename that is banned by default
    When user "Alice" moves file "/textfile0.txt" asynchronously to "/.htaccess" using the WebDAV API
    Then the HTTP status code should be "403"
    And user "Alice" should see the following elements
      | /textfile0.txt |

  Scenario: rename a file to a filename that is banned by default
    When user "Alice" moves file "/textfile0.txt" asynchronously to "/.htaccess" using the WebDAV API
    Then the HTTP status code should be "403"
    And user "Alice" should see the following elements
      | /textfile0.txt |

  Scenario: rename a file to a filename that is banned by default
    When user "Alice" moves file "/textfile0.txt" asynchronously to "/.htaccess" using the WebDAV API
    Then the HTTP status code should be "403"
    And user "Alice" should see the following elements
      | /textfile0.txt |

  Scenario: rename a file to a filename that is banned by default
    When user "Alice" moves file "/textfile0.txt" asynchronously to "/.htaccess" using the WebDAV API
    Then the HTTP status code should be "403"
    And user "Alice" should see the following elements
      | /textfile0.txt |

  Scenario: rename a file to a filename that is banned by default
    When user "Alice" moves file "/textfile0.txt" asynchronously to "/.htaccess" using the WebDAV API
    Then the HTTP status code should be "403"
    And user "Alice" should see the following elements
      | /textfile0.txt |

  Scenario: rename a file to a filename that is banned by default
    When user "Alice" moves file "/textfile0.txt" asynchronously to "/.htaccess" using the WebDAV API
    Then the HTTP status code should be "403"
    And user "Alice" should see the following elements
      | /textfile0.txt |

  Scenario: rename a file to a filename that is banned by default
    When user "Alice" moves file "/textfile0.txt" asynchronously to "/.htaccess" using the WebDAV API
    Then the HTTP status code should be "403"
    And user "Alice" should see the following elements
      | /textfile0.txt |

  Scenario: rename a file to a filename that is banned by default
    When user "Alice" moves file "/textfile0.txt" asynchronously to "/.htaccess" using the WebDAV API
    Then the HTTP status code should be "403"
    And user "Alice" should see the following elements
      | /textfile0.txt |

  Scenario: rename a file to a filename that is banned by default
    When user "Alice" moves file "/textfile0.txt" asynchronously to "/.htaccess" using the WebDAV API
    Then the HTTP status code should be "403"
    And user "Alice" should see the following elements
      | /textfile0.txt |

  Scenario: rename a file to a filename that is banned by default
    When user "Alice" moves file "/textfile0.txt" asynchronously to "/.htaccess" using the WebDAV API
    Then the HTTP status code should be "403"
    And user "Alice" should see the following elements
      | /textfile0.txt |
