<?php

namespace App\Session;

/*
 * Interface de session
 */
interface SessionInterface
{
  public function start(): void;
  public function set(string $key, $value);
  public function get(string $key, $default);
  public function destroy(): void;
}
