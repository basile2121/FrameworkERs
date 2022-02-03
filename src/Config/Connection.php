<?php

/**
 * Database connection
 *
 *
 *
 * @author adapted from Benjamin Besse
 *
 * @link   http://fr3.php.net/manual/fr/book.pdo.php classe PDO
 */

namespace App\Config;

use PDO;
use PDOException;

/**
 *
 * This class only make a PDO
 *
 */
class Connection
{
    private PDO $pdoConnection;

    /**
     * Initialize connection
     *
     * @access public
     */
    public function __construct()
    {

    }

    public function init()
    {
        try {
            $this->pdoConnection = new PDO(
                'mysql:host=' . $_ENV['DB_HOST']. '; dbname=' .  $_ENV['DB_NAME'] . '; charset=utf8',
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD']
            );
            $this->pdoConnection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // show errors in DEV environment
            if ($_ENV['APP_ENV'] === 'dev') {
                $this->pdoConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }

        } catch (PDOException $e) {
            echo '<div class="error">Error !: ' . $e->getMessage() . '</div>';
        }
    }

    /**
     * @return PDO $pdo
     */
    public function getPdoConnection(): PDO
    {
        return $this->pdoConnection;
    }
}
