<?php
session_start();
$db = new mysqli("localhost", "root", "", "moja_strona");

if ($db->connect_error) {
    die("Błąd połączenia z bazą danych: " . $db->connect_error);
}

// Pobieranie listy produktów
$query = "SELECT p.*, c.nazwa AS kategoria FROM produkty p LEFT JOIN categories c ON p.kategoria = c.id WHERE p.status_dostepnosci = 1";
$result = $db->query($query);

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sklep internetowy</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../js/toggleTheme.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../js/navbarEffects.js"></script>
</head>
<body>
    <header>
        <button class="theme-toggle" onclick="toggleTheme()">Przełącz Tryb Jasny/Ciemny</button>
        <h1>Filmy Oskarowe</h1>
        <nav class="navbar">
            <ul>
                <li><a href="../index.php?idp=glowna">Strona Główna</a></li>
                <li><a href="../index.php?idp=filmy">Najlepsze Filmy</a></li>
                <li><a href="../index.php?idp=aktorzy">Najlepsi Aktorzy</a></li>
                <li><a href="../index.php?idp=rezyserzy">Najlepsi Reżyserzy</a></li>
                <li><a href="../index.php?idp=kontakt">Kontakt</a></li>
                <li><a href="../index.php?idp=filmy_yt">Filmy YouTube</a></li>
                <li><a href="../shop/index.php">Sklep</a></li>
            </ul>
        </nav>
    </header>
    <h1>Sklep internetowy</h1>
    <a href="koszyk.php">Przejdź do koszyka</a>
    <div class="produkty">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="produkt" onclick="window.location='produkt.php?id=<?php echo $row['id']; ?>';">
                <img src="<?php echo $row['zdjecie']; ?>" alt="<?php echo $row['tytul']; ?>" class="produkt-zdjecie">
                <div class="produkt-szczegoly">
                    <h2><?php echo $row['tytul']; ?></h2>
                    <p>Kategoria: <?php echo $row['kategoria']; ?></p>
                    <p>Cena: <?php echo $row['cena_netto'] * (1 + $row['podatek_vat'] / 100); ?> zł</p>
                </div>
            </div>

        <?php endwhile; ?>
    </div>
</body>
</html>
