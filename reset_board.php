<?php
// Réinitialiser le tableau de jeu
$initial_board = [
    ["", "", ""],
    ["", "", ""],
    ["", "", ""]
];

file_put_contents('board.json', json_encode($initial_board));
?>
