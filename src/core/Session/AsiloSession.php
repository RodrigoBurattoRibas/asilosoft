<?php


final class AsiloSession
{
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function regenerateId(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
            session_regenerate_id(true);
        }
    }

    public function flash(string $key, ?string $value = null): ?string
    {
        $sessionKey = 'asilosoft_flash_' . $key;

        if ($value !== null) {
            $this->set($sessionKey, $value);
            return null;
        }

        $message = $this->get($sessionKey);
        $this->remove($sessionKey);

        return is_string($message) ? $message : null;
    }
}
