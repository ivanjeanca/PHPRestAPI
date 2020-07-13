<?php
include "config.php";
include "utils.php";

$dbConn =  connect($db);

// GET ALL
if ($_SERVER['REQUEST_METHOD'] == 'GET' && count($_GET) == 0) {
    $sql = $dbConn->prepare("SELECT * FROM posts");

    $sql->execute();
    $sql->setFetchMode(PDO::FETCH_ASSOC);
    $data = $sql->fetchAll();

    if (!empty($data)) {
        header("HTTP/1.1 200");
        echo json_encode(
            array(
                "valid" => 1,
                "data" => $data
            )
        );
    } else {
        header("HTTP/1.1 400");
        echo json_encode(
            array(
                "valid" => 0,
                "error" => "no se encontraron resultados"
            )
        );
    }

    exit();
}

// GET ONE
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $sql = $dbConn->prepare("SELECT * FROM posts where id = :id");

    $sql->bindValue(':id', $_GET['id']);
    $sql->execute();
    $data = $sql->fetch(PDO::FETCH_ASSOC);

    if (!empty($data)) {
        header("HTTP/1.1 200");
        echo json_encode(
            array(
                "valid" => 1,
                "data" => $data
            )
        );
    } else {
        header("HTTP/1.1 400");
        echo json_encode(
            array(
                "valid" => 0,
                "error" => "no se encontraron resultados"
            )
        );
    }

    exit();
}

// INSERT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = (array) json_decode(file_get_contents('php://input'));

    $fields = getParams($input, "POST");
    $sql = "INSERT INTO posts $fields";
    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);

    if ($statement->execute()) {
        $postId = $dbConn->lastInsertId();
        if ($postId) {
            $input['id'] = $postId;
            header("HTTP/1.1 200");
            echo json_encode(
                array(
                    "valid" => 1,
                    "data" => $input
                )
            );
        }
    } else {
        header("HTTP/1.1 400");
        echo json_encode(
            array(
                "valid" => 0,
                "error" => "solicitud incorrecta"
            )
        );
    }

    exit();
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $input = (array) json_decode(file_get_contents('php://input'));

    $fields = getParams($input, "PUT");
    $sql = "UPDATE posts SET $fields WHERE id='$_GET[id]'";

    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);

    if ($statement->execute()) {
        header("HTTP/1.1 200");
        echo json_encode(
            array(
                "valid" => 1,
                "data" => $input
            )
        );
    } else {
        header("HTTP/1.1 400");
        echo json_encode(
            array(
                "valid" => 0,
                "error" => "solicitud incorrecta"
            )
        );
    }

    exit();
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $statement = $dbConn->prepare("DELETE FROM posts where id=:id");
    $statement->bindValue(':id', $_GET['id']);

    if ($statement->execute()) {
        header("HTTP/1.1 200");
        echo json_encode(
            array(
                "valid" => 1,
                "data" => $_GET
            )
        );
    } else {
        header("HTTP/1.1 400");
        echo json_encode(
            array(
                "valid" => 0,
                "error" => "solicitud incorrecta"
            )
        );
    }

    exit();
}

// ERROR
header("HTTP/1.1 405");
echo json_encode(
    array(
        "valid" => 0,
        "error" => "metodo [" . $_SERVER['REQUEST_METHOD'] . "] no permitido"
    )
);
