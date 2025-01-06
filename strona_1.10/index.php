<?php
include'cfg.php';
?>
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
        // Wyświetlanie błędów PHP (opcjonalne, pomocne podczas debugowania)
        error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

        // Dołączenie pliku showpage.php, gdzie znajduje się funkcja PokazPodstrone
        include'showpage.php'; 

        // Pobranie parametru 'idp' z adresu URL, z domyślną wartością 'glowna' dla strony głównej
        $idp = isset($_GET['idp']) ? $_GET['idp'] : 'glowna';

        // Wywołanie funkcji, aby pobrać treść z bazy danych na podstawie id strony
        $content = PokazPodstrone($idp);

        // Wyświetlenie zawartości strony (HTML z bazy danych)
        echo $content;
        ?>
    </div>

    <footer>
        <p>Czas spędzony na stronie: <span id="time-spent">0</span> sekund</p>
        <p>169347 Borys Pakosz</p>
    </footer>
</body>
</html>
