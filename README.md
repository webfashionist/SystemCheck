# SystemCheck
Check the server environment with PHP


## Server

Detects the following server configurations and versions:

- Apache
- Nginx
- IIS
- PHP
- MySQL (client + server)

## Extensions

Installed PHP extensions can be checked with `SystemCheck::isExtensionLoaded("name of the extension")`

In the `check.php` the following extensions are checked:

- OpenSSL
- PDO
- MySQLi
- icon
- JSON
- Imagick

## Details

- Server port
- PHP path to the executable file

## Core

Checks core server configurations, from `php.ini`.

The following directives are available in the `check.php` file:

- allow_url_fopen
- allow_url_include
- default_charset
- file_uploads
- display_errors
- display_startup_errors
- error_log
- log_errors
- date.timezone