<?php
function resetBoard() {
    $initial_board = [
        ["", "", ""],
        ["", "", ""],
        ["", "", ""]
    ];

    file_put_contents('board.json', json_encode($initial_board));
}

function checkWinner($board, $player) {
    // Vérifier les lignes
    for ($i = 0; $i < 3; $i++) {
        if ($board[$i][0] == $player && $board[$i][1] == $player && $board[$i][2] == $player) {
            return true;
        }
    }

    // Vérifier les colonnes
    for ($j = 0; $j < 3; $j++) {
        if ($board[0][$j] == $player && $board[1][$j] == $player && $board[2][$j] == $player) {
            return true;
        }
    }

    // Vérifier les diagonales
    if ($board[0][0] == $player && $board[1][1] == $player && $board[2][2] == $player) {
        return true;
    }
    if ($board[0][2] == $player && $board[1][1] == $player && $board[2][0] == $player) {
        return true;
    }

    return false;
}

function checkDraw($board) {
    foreach ($board as $row) {
        foreach ($row as $cell) {
            if ($cell == "") {
                return false;
            }
        }
    }
    return true;
}

function makeMove($board, $player, $difficulty) {
    switch ($difficulty) {
        case "easy":
            return makeRandomMove($board);
        case "medium":
            // Medium difficulty can make random moves
            // or choose the winning move if available
            $winningMove = findWinningMove($board, $player);
            if ($winningMove !== false) {
                return $winningMove;
            } else {
                return makeRandomMove($board);
            }
        case "hard":
            // Hard difficulty will always choose the winning move if available
            // or block the player from winning
            $winningMove = findWinningMove($board, $player);
            if ($winningMove !== false) {
                return $winningMove;
            }
            $blockingMove = findBlockingMove($board, $player);
            if ($blockingMove !== false) {
                return $blockingMove;
            }
            // If no winning or blocking move, choose a random move
            return makeRandomMove($board);
    }
}

function makeRandomMove($board) {
    do {
        $row = rand(0, 2);
        $col = rand(0, 2);
    } while ($board[$row][$col] != "");

    return [$row, $col];
}

function findWinningMove($board, $player) {
    // Check rows
    for ($i = 0; $i < 3; $i++) {
        if ($board[$i][0] == $player && $board[$i][1] == $player && $board[$i][2] == "") {
            return [$i, 2];
        }
        if ($board[$i][0] == $player && $board[$i][2] == $player && $board[$i][1] == "") {
            return [$i, 1];
        }
        if ($board[$i][1] == $player && $board[$i][2] == $player && $board[$i][0] == "") {
            return [$i, 0];
        }
    }
    // Check columns
    for ($j = 0; $j < 3; $j++) {
        if ($board[0][$j] == $player && $board[1][$j] == $player && $board[2][$j] == "") {
            return [2, $j];
        }
        if ($board[0][$j] == $player && $board[2][$j] == $player && $board[1][$j] == "") {
            return [1, $j];
        }
        if ($board[1][$j] == $player && $board[2][$j] == $player && $board[0][$j] == "") {
            return [0, $j];
        }
    }
    // Check diagonals
    if ($board[0][0] == $player && $board[1][1] == $player && $board[2][2] == "") {
        return [2, 2];
    }
    if ($board[0][0] == $player && $board[2][2] == $player && $board[1][1] == "") {
        return [1, 1];
    }
    if ($board[1][1] == $player && $board[2][2] == $player && $board[0][0] == "") {
        return [0, 0];
    }
    if ($board[0][2] == $player && $board[1][1] == $player && $board[2][0] == "") {
        return [2, 0];
    }
    if ($board[0][2] == $player && $board[2][0] == $player && $board[1][1] == "") {
        return [1, 1];
    }
    if ($board[1][1] == $player && $board[2][0] == $player && $board[0][2] == "") {
        return [0, 2];
    }
    return false;
}

function findBlockingMove($board, $player) {
    $opponent = ($player == 'X') ? 'O' : 'X';
    return findWinningMove($board, $opponent);
}

if(isset($_GET['move'])) {
    $move = explode(',', $_GET['move']);
    $row = $move[0];
    $col = $move[1];
    $player = $move[2];

    $file = 'board.json';
    $board = json_decode(file_get_contents($file), true);

    $board[$row][$col] = $player;

    // Vérifier si le joueur a gagné ou s'il y a un match nul
    if (checkWinner($board, $player)) {
        echo json_encode(['winner' => $player]);
    } elseif (checkDraw($board)) {
        echo json_encode(['draw' => true]);
    } else {
        // Faire un mouvement pour le bot
        $difficulty = isset($_GET['difficulty']) ? $_GET['difficulty'] : 'medium';
        list($botRow, $botCol) = makeMove($board, ($player == 'X' ? 'O' : 'X'), $difficulty);
        $board[$botRow][$botCol] = ($player == 'X' ? 'O' : 'X');
        
        // Vérifier si le bot a gagné ou s'il y a un match nul après son mouvement
        if (checkWinner($board, ($player == 'X' ? 'O' : 'X'))) {
            echo json_encode(['winner' => ($player == 'X' ? 'O' : 'X')]);
        } elseif (checkDraw($board)) {
            echo json_encode(['draw' => true]);
        } else {
            file_put_contents($file, json_encode($board));
            // Rediriger vers l'index après chaque coup
            header('Location: index.php');
            exit;
        }
    }
}

// Si le paramètre reset est présent dans l'URL, réinitialiser le tableau de jeu
if(isset($_GET['reset'])) {
    resetBoard();
    // Rediriger vers l'index après la réinitialisation
    header('Location: index.php');
    exit;
}
?>

