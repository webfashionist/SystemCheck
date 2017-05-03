<?php

require_once __DIR__ . '/../src/SystemCheck.class.php';

$SystemCheck = new webfashionist\SystemCheck();

// system requirements
define("PHP_REQUIRED", "7.0");
define("MYSQL_REQUIRED", "5.0");
define("MYSQL_SERVER_REQUIRED", "5.0");
define("APACHE_REQUIRED", "2.4");
define("NGINX_REQUIRED", "");
define("IIS_REQUIRED", "");

// extensions
define("OPENSSL_REQUIRED", true);
define("PDO_REQUIRED", true);
define("MYSQLI_REQUIRED", false);
define("IMAGICK_REQUIRED", false);

// database connection data
define("DB_USERNAME", "");
define("DB_PASSWORD", "");
define("DB_HOST", "localhost");


?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Check system environment</title>

        <style>
            * {
                margin:0;
                padding:0;
                -webkit-box-sizing:border-box;
                -moz-box-sizing:border-box;
                box-sizing:border-box;
            }
            body {
                background-color:#EFEFEF;
                font-family: Calibri,Verdana,Helvetica,sans-serif;
            }

            h2 {
                margin:10px 0;
                color:#2980b9;
            }

            table {
                margin:20px auto;
                border:none;
                width:400px;
                max-width:100%;
            }

            table td {
                padding:10px;
                border-bottom:#FAFAFA solid 1px;
                color:#333;
            }
        </style>
    </head>
    <body>

        <table>
            <tr>
                <td colspan="3"><h2>Server</h2></td>
            </tr>


            <tr>
                <td>Apache</td>
                <td><?php echo $SystemCheck::getApacheVersion(); ?></td>
                <td><?php echo ($SystemCheck::compareVersion($SystemCheck::getApacheVersion(), APACHE_REQUIRED) ? '<span style="color:green;">OK</span>' : '<span style="color:red;">NOT OK</span>'); ?></td>
            </tr>
            <tr>
                <td>Nginx</td>
                <td><?php echo $SystemCheck::getNginxVersion(); ?></td>
                <td><?php echo ($SystemCheck::compareVersion($SystemCheck::getNginxVersion(), NGINX_REQUIRED) ? '<span style="color:green;">OK</span>' : '<span style="color:red;">NOT OK</span>'); ?></td>
            </tr>
            <tr>
                <td>IIS</td>
                <td><?php echo $SystemCheck::getIISVersion(); ?></td>
                <td><?php echo ($SystemCheck::compareVersion($SystemCheck::getIISVersion(), IIS_REQUIRED) ? '<span style="color:green;">OK</span>' : '<span style="color:red;">NOT OK</span>'); ?></td>
            </tr>

            <tr>
                <td>PHP</td>
                <td><?php echo $SystemCheck::getPHPversion(); ?></td>
                <td><?php echo ($SystemCheck::compareVersion($SystemCheck::getPHPversion(), PHP_REQUIRED) ? '<span style="color:green;">OK</span>' : '<span style="color:red;">NOT OK</span>'); ?></td>
            </tr>
            <tr>
                <td>MySQL (client)</td>
                <td><?php echo $SystemCheck::getMySQLClientVersion(); ?></td>
                <td><?php echo ($SystemCheck::compareVersion($SystemCheck::getMySQLClientVersion(), MYSQL_REQUIRED) ? '<span style="color:green;">OK</span>' : '<span style="color:red;">NOT OK</span>'); ?></td>
            </tr>
            <tr>
                <td>MySQL (server)</td>
                <td><?php echo $SystemCheck::getMySQLServerVersion(DB_HOST, DB_USERNAME, DB_PASSWORD); ?></td>
                <td><?php echo ($SystemCheck::compareVersion($SystemCheck::getMySQLServerVersion(DB_HOST, DB_USERNAME, DB_PASSWORD), MYSQL_SERVER_REQUIRED) ? '<span style="color:green;">OK</span>' : '<span style="color:red;">NOT OK</span>'); ?></td>
            </tr>


            <tr>
                <td colspan="3"><h2>Extensions</h2></td>
            </tr>

            <tr>
                <td>OpenSSL</td>
                <td><?php echo ($SystemCheck::isExtensionLoaded("OpenSSL") ? 'Installed' : 'Not installed'); ?></td>
                <td><?php echo ($SystemCheck::isExtensionLoaded("OpenSSL") || !OPENSSL_REQUIRED? '<span style="color:green;">OK</span>' : '<span style="color:red;">NOT OK</span>'); ?></td>
            </tr>
            <tr>
                <td>PDO</td>
                <td><?php echo ($SystemCheck::isExtensionLoaded("pdo") ? 'Installed' : 'Not installed'); ?></td>
                <td><?php echo ($SystemCheck::isExtensionLoaded("pdo") || !PDO_REQUIRED ? '<span style="color:green;">OK</span>' : '<span style="color:red;">NOT OK</span>'); ?></td>
            </tr>
            <tr>
                <td>MySQLi</td>
                <td><?php echo ($SystemCheck::isExtensionLoaded("mysqli") ? 'Installed' : 'Not installed'); ?></td>
                <td><?php echo ($SystemCheck::isExtensionLoaded("mysqli") || !MYSQLI_REQUIRED ? '<span style="color:green;">OK</span>' : '<span style="color:red;">NOT OK</span>'); ?></td>
            </tr>
            <tr>
                <td>Imagick</td>
                <td><?php echo ($SystemCheck::isExtensionLoaded("imagick") ? 'Installed' : 'Not installed'); ?></td>
                <td><?php echo ($SystemCheck::isExtensionLoaded("imagick") || !IMAGICK_REQUIRED ? '<span style="color:green;">OK</span>' : '<span style="color:red;">NOT OK</span>'); ?></td>
            </tr>
        </table>

    </body>
</html>
