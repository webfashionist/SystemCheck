<?php

namespace webfashionist;

use mysqli_sql_exception;
use PDO;
use PDOException;

/**
 * Class SystemCheck
 * @package webfashionist
 */
class SystemCheck
{

    /**
     * Returns the current PHP version
     * @return string
     */
    public static function getPHPversion(): string
    {
        return phpversion();
    }


    /**
     * Returns the port of the server
     * @return string
     */
    public static function serverPort()
    {
        return self::filter_server("SERVER_PORT", FILTER_SANITIZE_STRING);
    }


    /**
     * Returns the core settings
     * @param string $directive Directive
     * @return string
     */
    public static function getCore($directive)
    {
        $value = ini_get($directive);

        if ($value == 1) {
            return "On";
        }
        if ($value == "" || (!$value && $value != 0)) {
            return '<i style="font-size:small;">no value</i>';
        }
        return $value;
    }


    /**
     * Returns the path of the PHP executable - if available
     * @return string
     */
    public static function whichPHP()
    {
        $paths = [
            getenv('PHPBIN'),
            self::filter_server('_', FILTER_SANITIZE_STRING),
            self::filter_server('PHPRC', FILTER_SANITIZE_STRING),
            self::filter_server('PHP_PEAR_SYSCONF_DIR', FILTER_SANITIZE_STRING),
            PHP_BINDIR,
            exec("which php"), // MacOS and Linux OS (checks the CLI PHP binary)
        ];

        foreach($paths as $path) {
            if($path) {
                break;
            }
        }

        if (!file_exists($path)) {
            $path = "Unknown";
        }

        return $path;
    }


    /**
     * Returns the client-side MySQL version
     * @return string
     */
    public static function getMySQLClientVersion()
    {
        if (function_exists("shell_exec")) {
            $version = shell_exec("mysql -v");
            if ($version) {
                return $version;
            }
        }
        $version = mysqli_get_client_version();
        if ($version) {
            // reformat version number
            $mainVersion = floor($version / 10000);
            $minorVersion = floor($version / 100) - ($mainVersion * 100);
            $subVersion = $version - ($mainVersion * 10000) - ($minorVersion * 100);
            return $mainVersion . "." . $minorVersion . "." . $subVersion;
        }
        return "Unknown";
    }


    /**
     * Returns the server-side MySQL version
     * @param string $host Hostname
     * @param string $user Username
     * @param string $password Password
     * @return string array|string
     */
    public static function getMySQLServerVersion($host, $user, $password)
    {
        // save occured errors
        $error = [];

        if (self::isExtensionLoaded("PDO")) {
            // connect with PDO - preferred method here but needs a database name for the connection

            try {
                // connect to database
                $pdo = new PDO("mysql:host=" . $host, $user, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                // fetch version
                $version = $pdo->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));
                // close connection
                unset($pdo);

                if ($version) {
                    // reformat version number
                    $explode = explode("-", $version);
                    return $explode[0];
                }
            } catch (PDOException $exception) {
                // PDO error occured
                $error[] = "<b>PDO</b>: " . $exception->getMessage();
            }
        }

        if (self::isExtensionLoaded("mysqli")) {
            // connect with MySQLi
            try {
                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                $mysqli = new \mysqli($host, $user, $password);
                if (mysqli_connect_errno()) {
                    // MySQLi error occured
                    $error[] = mysqli_connect_error();
                } else {
                    // fetch version
                    $version = (isset($mysqli->server_version) ? $mysqli->server_version : '');
                    // close connection
                    $mysqli->close();

                    if ($version) {
                        // reformat version number
                        $mainVersion = floor($version / 10000);
                        $minorVersion = floor($version / 100) - ($mainVersion * 100);
                        $subVersion = $version - ($mainVersion * 10000) - ($minorVersion * 100);
                        return $mainVersion . "." . $minorVersion . "." . $subVersion;
                    }
                }
            } catch (mysqli_sql_exception $exception) {
                // MySQLi error occured
                $error[] = "<b>MySQLi</b>: " . $exception->getMessage();
            }


        }

        return (is_array($error) && count($error) > 0 ? implode("<br><br>\r\n", $error) : "Unknown");
    }


    /**
     * Returns the IIS (Internet Information Services) version
     * @return string
     */
    public static function getIISVersion()
    {
        $command = 'powershell "get-itemproperty HKLM:\SOFTWARE\Microsoft\InetStp\ | select setupstring,versionstring"';

        if (function_exists("exec")) {
            $version = exec($command);
            if ($version) {
                return $version;
            }
        }
        return "Unknown/Not installed";
    }


    /**
     * Returns the Nginx version
     * @return string
     */
    public static function getNginxVersion()
    {
        if (function_exists("shell_exec")) {
            $version = shell_exec("nginx -V");
            if ($version) {
                // match only the Apache version from output
                preg_match("/nginx\/([\.0-9]+)/i", $version, $match);
                if (isset($match[1]) && $match[1]) {
                    return $match[1];
                }
            }
        }
        return "Unknown/Not installed";
    }


    /**
     * Returns the Apache version
     * @return string
     */
    public static function getApacheVersion()
    {
        if (function_exists("apache_get_version")) {
            $version = apache_get_version();
            if ($version) {
                // match only the Apache version from output
                preg_match("/Apache\/([.0-9]+)/i", $version, $match);
                if (isset($match[1]) && $match[1]) {
                    return $match[1];
                }
            }
        }
        return "Unknown/Not installed";
    }


    /**
     * Checks if the system version is the same or higher than the needed version
     * @param string $systemVersion
     * @param string $neededVersion
     * @return bool
     */
    public static function compareVersion($systemVersion, $neededVersion)
    {
        return !version_compare($systemVersion, $neededVersion, "<");
    }


    /**
     * Check if a given PHP extension is loaded or not
     * @param string $extension
     * @return bool
     */
    public static function isExtensionLoaded($extension)
    {
        return extension_loaded($extension);
    }

    /**
     * @param string $key
     * @param int $filter
     * @return mixed|null
     */
    private static function filter_server(string $key, int $filter = FILTER_SANITIZE_STRING)
    {
        if(!isset($_SERVER[$key])) {
            return null;
        }
        return filter_var($_SERVER[$key], $filter);
    }

}