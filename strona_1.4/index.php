<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Strona poświęcona filmom oskarowym">
    <title>Filmy Oskarowe</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/timer.js"></script>
    <script src="js/toggleTheme.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/navbarEffects.js"></script>
</head>
<body>
    <header>
        <button class="theme-toggle" onclick="toggleTheme()">Przełącz Tryb Jasny/Ciemny</button>
        <h1>Filmy Oskarowe</h1>
        <nav class="navbar">
            <ul>
                <li><a href="index.php?idp=glowna">Strona Główna</a></li>
                <li><a href="index.php?idp=filmy">Najlepsze Filmy</a></li>
                <li><a href="index.php?idp=aktorzy">Najlepsi Aktorzy</a></li>
                <li><a href="index.php?idp=rezyserzy">Najlepsi Reżyserzy</a></li>
                <li><a href="index.php?idp=kontakt">Kontakt</a></li>
                <li><a href="index.php?idp=filmy_yt">Filmy YouTube</a></li>
            </ul>
        </nav>
    </header>

    <div class="content">
        <?php
        error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

        // Dynamiczne ładowanie stron
        if ($_GET['idp'] == '') {
            $strona = 'html/glowna.html';
        } elseif ($_GET['idp'] == 'filmy') {
            $strona = 'html/filmy.html';
        } elseif ($_GET['idp'] == 'aktorzy') {
            $strona = 'html/aktorzy.html';
        } elseif ($_GET['idp'] == 'rezyserzy') {
            $strona = 'html/rezyserzy.html';
        } elseif ($_GET['idp'] == 'kontakt') {
            $strona = 'html/kontakt.html';
        }elseif($_GET['idp'] == 'filmy_yt') {
            $strona = 'html/filmy_yt.html';
        }else {
            $strona = 'html/glowna.html';
        }

        // Sprawdź, czy plik istnieje
        if (file_exists($strona)) {
            include($strona);
        } else {
            echo "Strona nie istnieje.";
        }
        ?>
    </div>

    <footer>
        <p>Czas spędzony na stronie: <span id="time-spent">0</span> sekund</p>
        <p>169347 Borys Pakosz</p>
    </footer>
</body>
</html>
