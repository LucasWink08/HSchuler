<?php
define('usuario', 'root');
define('senha', 'lucas123');
define('bd', 'atividade');
define('servidor', 'localhost');
define('porta', '3306');
define('dsn', 'mysql:host=' . servidor . ';port=' . porta . ';dbname=' . bd);
define('host', 'localhost');

$conexao =new PDO(dsn, usuario, senha);

?>