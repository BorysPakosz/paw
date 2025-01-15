<?php
session_start();
$db = new mysqli("localhost", "root", "", "moja_strona");

if ($db->connect_error) {
    die("Błąd połączenia z bazą danych: " . $db->connect_error);
}

$id = (int)$_GET['id']; // Pobranie ID produktu
$query = "SELECT * FROM produkty WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$produkt = $result->fetch_assoc();

if (!$produkt) {
    die("Produkt nie istnieje.");
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $produkt['tytul']; ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <form method="POST" action="koszyk.php?action=dodaj">
        <h1><?php echo $produkt['tytul']; ?></h1>
        <p>Opis: <?php echo $produkt['opis']; ?></p>
        <p>Cena netto: <?php echo $produkt['cena_netto']; ?> zł</p>
        <p>VAT: <?php echo $produkt['podatek_vat']; ?>%</p>
        <p>Łączna cena: <?php echo $produkt['cena_netto'] * (1 + $produkt['podatek_vat'] / 100); ?> zł</p>
        <input type="hidden" name="id" value="<?php echo $produkt['id']; ?>">
        <input type="hidden" name="nazwa" value="<?php echo $produkt['tytul']; ?>">
        <input type="hidden" name="cena_brutto" value="<?php echo $produkt['cena_netto'] * (1 + $produkt['podatek_vat'] / 100); ?>">
        <label>Ilość: <input type="number" name="ilosc" value="1" min="1"></label>
        <button type="submit">Dodaj do koszyka</button>
    </form>
    <a href="index.php">Powrót do sklepu</a>
</body>
</html>
