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

        #result {
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
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
                echo '<td class="cell" id="' . $i . '-' . $j . '" onclick="play(' . $i . ', ' . $j . ')">' . ($board[$i][$j] !== "" ? $board[$i][$j] : "&nbsp;") . '</td>';
            }
            echo '</tr>';
        }
        ?>
    </table>

    <form id="form" method="post">
        <label for="difficulty">Choisir la difficulté :</label>
        <select name="difficulty" id="difficulty">
            <option value="easy">Facile</option>
            <option value="medium">Moyen</option>
            <option value="hard">Difficile</option>
            <option value="local">Joueur Local</option>
        </select>
        <label for="symbol">Choisir votre symbole :</label>
        <select name="symbol" id="symbol">
            <option value="X">X</option>
            <option value="O">O</option>
        </select>
        <button type="button" onclick="restartGame()">Recommencer</button>
    </form>

    <div id="result"></div>

    <script>
        function play(row, col) {
            var player = document.getElementById('symbol').value;
            var difficulty = document.getElementById('difficulty').value;

            if (player !== 'X' && player !== 'O') {
                alert("Entrez 'X' ou 'O' seulement.");
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    console.log("Response received:", xhr.responseText); // Débogage
                    var response = JSON.parse(xhr.responseText);
                    console.log("Parsed response:", response); // Débogage
                    if (response.winner) {
                        document.getElementById('result').innerText = 'Le gagnant est : ' + response.winner;
                    } else if (response.draw) {
                        document.getElementById('result').innerText = 'Match nul !';
                    }
                    updateBoard(response.board);
                }
            };
            var url = 'morpion.php?move=' + row + ',' + col + ',' + player + '&difficulty=' + difficulty;
            console.log("Sending request to:", url); // Débogage
            xhr.open('GET', url, true);
            xhr.send();
        }


        function updateBoard(board) {
            for (var i = 0; i < 3; i++) {
                for (var j = 0; j < 3; j++) {
                    var cell = document.getElementById(i + '-' + j);
                    cell.innerHTML = board[i][j] !== "" ? board[i][j] : "&nbsp;";
                }
            }
        }

        function restartGame() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    updateBoard(JSON.parse(xhr.responseText));
                    document.getElementById('result').innerText = '';
                }
            };
            xhr.open('GET', 'morpion.php?reset=true', true);
            xhr.send();
        }
    </script>
</body>

</html>