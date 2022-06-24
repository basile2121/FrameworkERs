<?php 

namespace App\Session;

class Session 
{
    /**
     * Assure que la session est toujours lancée
     */
    public function ensureStarted(){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
    }

    /**
     * Récupération de la valeur de la clef passé en paramétre
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function get(string $key, $default = null): mixed
    {
        $this->ensureStarted();
        if (array_key_exists($key, $_SESSION)){
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
     * Définis une valeur en session
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value): void{
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * Supprime une valeur en session
     * @param string $key
     */
    public function delete(string $key) : void{
        $this->ensureStarted();
        unset ($_SESSION[$key]);
    }
}