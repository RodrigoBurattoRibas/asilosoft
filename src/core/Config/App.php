<?php


final class App
{
    public static function init(): void
    {
        date_default_timezone_set('America/Sao_Paulo');
        setlocale(LC_ALL, 'pt_BR.UTF-8');
    }
}
