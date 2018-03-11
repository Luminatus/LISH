# LI.SH
A Php link shortener.

## Introduction

LI.SH (short for Link Shortener) is a Php based link shortening service created as a minor test project. The project consists of a REST API which provides the link shortening service, and a Php site collection which provides a user interface for the API.

## Requirements

LI.SH requires Php5 > 5.6.0 or Php7. No further dependency or framework required.

## Installation

The site uses an SQLite3 database to store data, which needs to be created before using the site. The file db.php contains the php script used to create the database and table structure.

## Features

- Create and open custom shortlinks for any URL
- Automatic conflict-handling, ensuring that all custom shortlinks are unique
- Automatic URL validation, ensuring only URLs to existing sites are registered
- Admin authentication for elevated API methods
- URL redirection, which allows the use of API methods similar to directories

## How to use

- To open the user interface, just open the root directory from your browser. The user interface can be used to create new shortlinks.
- To open a shortlink, simply type /code after the root path, where code is the custom shortlink code.
- The admin page, used to print out all url link pairs is accessed through /admin.
- To directly access the API, use /api/method_name/param1/param2/.../, where method_name is the name of the API method you wish to call, and param1,param2,etc are the GET parameters for that method. If an API endpoint is accessible through POST request, the parameters still need to be sent via POST. (See the [API section below](/README.md#API) )

## API

API functions consist of the name of the API endpoint, and the request method separated by an underscore. When accessing the API through an HTTP request, only the endpoint name must be given, not the full function name.
(eg.: /api/open/mylink instead of /api/open_get/mylink)

API calls through a GET method will require the parameters put into the URL separated by forward slashes (/param1/param2), instead of using a GET query string (?param1=val1&param2=val2). API calls sent via POST remain the same.

The public API methods are the following:
- `open_get(code)`
  - **Task:** returns the URL associated with the shorthand code stored in 'code'
  - **Params:**
     - `code`: The custom shorthand code associated with a registered URL.
  - **Return:** The URL associated with the shorthand code, or error message is the URL cannot be fetched. 
  
- `create_post()`
  - **Task:** Register a new URL/code pair.
  - **Params:**
      - `full_url`: A functioning URL
      - `short_url`: A custom code to be assigned to the real URL.
  - **Return:** An object containing the registered URL (not necessarily the same as 'full_url') and the custom code in the same structure as the parameters.
  
- `check_get(code)`
  - **Task:** Checks whether 'code' is still available.
  - **Params:**
    - `code`: A custom shorthand code enetered by the user.
  - **Return:**TRUE if the code is available, or FALSE if it is taken.
  
- `list_all_post(pw)`
  - **Task:** An admin-level API call, that returns all url/code pairs from the database.
  - **Params:**
    - `pw`: The admin password, entered by an admin user.
  - **Return:** An array containing link/code pairs in a (full_url, short_url) structure.
