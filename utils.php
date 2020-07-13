<?php
// CONEXION A LA BD
function connect($db) {
    try {
        $conn = new PDO("mysql:host={$db['host']};dbname={$db['db']}", $db['username'], $db['password']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // MODO DE ERROR EXEPCION
        return $conn;
    } catch (PDOException $exception) {
        exit($exception->getMessage());
    }
}

// OBTENER PARAMETROS INDICADOS EN EL JSON
function getParams($input, $method) {
    $filterParams = [];
    if ($method == "POST" || $method == "post") {
        foreach ($input as $param => $value)
            $filterParams[] = "$param";
        return '(' . implode(", ", $filterParams) . ') VALUES (:' . implode(", :", $filterParams) . ")";
    } elseif ($method == "PUT" || $method == "put") {
        foreach ($input as $param => $value)
            $filterParams[] = "$param=:$param";
        return implode(", ", $filterParams);
    }
}

// ASOCIAR (BIND) PARAMETROS CON VALORES
function bindAllValues($statement, $params) {
    foreach ($params as $param => $value)
        $statement->bindValue(':' . $param, $value);
    return $statement;
}
