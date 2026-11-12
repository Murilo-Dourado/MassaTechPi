<?php

date_default_timezone_set('America/Sao_Paulo');
session_start();

const DB_HOST = 'localhost';
const DB_NOME = 'sabor_massa';
const DB_USER = 'root';
const DB_SENHA = '';


const ID_USUARIO = 1;


const TAXA_ENTREGA = 7.00;

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NOME . ';charset=utf8mb4',
    DB_USER,
    DB_SENHA,
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);


function e($texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}


function reais($valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function nome_tamanho(string $tamanho): string
{
    $nomes = ['media' => 'Média', 'grande' => 'Grande', 'familia' => 'Família', 'unico' => 'Único'];
    return $nomes[$tamanho] ?? $tamanho;
}


function endereco_completo(array $end): string
{
    return $end['rua'] . ', ' . $end['numero'] . ' - ' . $end['bairro'] . ', ' . $end['cidade'];
}
