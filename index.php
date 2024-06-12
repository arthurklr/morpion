<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Morpion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            margin: 20px auto;
        }

        .cell {
            width: 50px;
            height: 50px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            font-size: 24px;
            cursor: pointer;
        }

        button {
            display: block;
            margin: 10px auto;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        select {
            font-size: 16px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Jeu du Morpion</h1>
    <table>
        <?php
            // Charger le tableau de jeu depuis le fichier JSON
            $file = 'board.json';
            $board = json_decode(file_get_contents($file), true);

            // Afficher le tableau de jeu
            for ($i = 0; $i < 3; $i++) {
                echo '<tr>';
                for ($j = 0; $j < 3; $j++) {
                    echo '<td class="cell" id="' . $i . '-' . $j . '" onclick="play(' . $i . ', ' . $j . ')">' . $board[$i][$j] . '</td>';
                }
                echo '</tr>';
            }
        ?>
    </table>
    
    <form method="post">
        <label for="difficulty">Choisir la difficulté :</label>
        <select name="difficulty" id="difficulty">
            <option value="easy">Facile</option>
            <option value="medium">Moyen</option>
            <option value="hard">Difficile</option>
        </select>
        <button type="submit" name="restart">Recommencer</button>
    </form>

    <script>
        function play(row, col) {
            var player = prompt("Entrez 'X' ou 'O'");
            if (player !== 'X' && player !== 'O') {
                alert("Entrez 'X' ou 'O' seulement.");
                return;
            }

            window.location.href = 'morpion.php?move=' + row + ',' + col + ',' + player;
        }
    </script>
</body>
</html>

<?php
// Si le bouton "Recommencer" est cliqué, réinitialiser le jeu
if(isset($_POST['restart'])) {
    $initial_board = [
        ["", "", ""],
        ["", "", ""],
        ["", "", ""]
    ];

    file_put_contents('board.json', json_encode($initial_board));
}
?>
