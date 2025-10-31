<?php

$datos = json_decode(file_get_contents("partidas.json"), true);

$input = json_decode(file_get_contents("php://input"));



if ($input['id'] > count($datos))
{
    $datos[$input['id']]['x'] = $input['x'];
    $datos[$input['id']]['y'] = $input['y'];
    $datos[$input['id']]['clase'] = $input['clase'];
}
else
{
    $partidanueva = [];
    $partidanueva['x'] = $input['x'];
    $partidanueva['y'] = $input['y'];
    $partidanueva['clase'] = $input['clase'];
    $partidanueva['id'] = $input['id'];
    $datos[] = $partidanueva;
}

file_put_contents("partidas.json", json_encode($datos, JSON_PRETTY_PRINT));

?>