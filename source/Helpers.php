<?php

use Source\Model\User;

/**
 * @param string $path
 * @param bool $time
 * @return string
 */
function asset(string $file, string $path = "/admin", $time = true): string
{
    $file = URL_BASE . "/views{$path}/assets/{$file}";
    $fileOnDir = dirname(__DIR__, 1) . "/views/$path}/assets/{$file}";
    if ($time && file_exists($fileOnDir)) {
        $file .= "?time=" . filemtime($fileOnDir);
    }
    return $file;
}

/**
 * Cria resumo da string
 * @param [type] $string o texto
 * @param [type] $chars a qtd de caracteres
 * @return void
 */

function resume(string $string, $chars = 180)
{
    return strip_tags(mb_strimwidth($string, 0, $chars + 3, "..."));
}

function toDatePtBr($date, $showTime = true)
{
    $DateTime = new DateTime($date);
    if ($showTime)
        return $DateTime->format("d/m/Y H:i:s");
    return $DateTime->format("d/m/Y");
}

function formatPrice($vlprice)
{
    if (!$vlprice > 0) $vlprice = 0;
    return number_format($vlprice, 2, ",", ".");
}
function formatDate($date)
{
    return date("d/m/Y", strtotime($date));
}
function checkLogin($inadmin = true)
{
    return User::checkLogin($inadmin);
}

function getUserName()
{
    $user = User::getFromSession();
    return $user->getdesperson();
}

/**
 * [Retorna a url base da aplicação]
 * @param  string $path [Caminho]
 * @return [type]       [String]
 */
function url(string $path): string
{
    if ($path)
        return URL_BASE . $path;
    return URL_BASE;
}

/**
 * [Retorna a url base da aplicação]
 * @param  string $path [Site ou Admin]
 * @return [type]       [String]
 */
function assets(string $path): string
{
    return URL_BASE . '/views/' . $path . '/assets/';
}

/**
 * [Retorna class de mensagem padrão]
 * @param  string $message [Mensagem]
 * @param  string $type    [Tipo de alerta]
 * @return [type]          [String]
 */
function message(string $message, string $type): string
{
    return "<div class=\"message {$type}\">{$message}</div>";
}
