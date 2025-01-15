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
<?php
session_start();
require 'koszyk_logika.php'; // Import funkcji koszyka

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'dodaj':
            addToCart($_POST['id'], $_POST['nazwa'], $_POST['cena_brutto'], $_POST['ilosc']);
            header("Location: koszyk.php");
            break;

        case 'usun':
            removeFromCart($_GET['id']);
            header("Location: koszyk.php");
            break;

        case 'edytuj':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                updateCartQuantity($_POST['id'], $_POST['ilosc']);
                header("Location: koszyk.php");
            } else {
                // Formularz zmiany ilości
                echo "<form method='POST'>
                    <label>Ilość: <input type='number' name='ilosc' min='1' value='1'></label>
                    <input type='hidden' name='id' value='{$_GET['id']}'>
                    <button type='submit'>Zmień</button>
                </form>";
            }
            break;

        default:
            showCart(); // Wyświetlenie koszyka
            break;
    }
} else {
    showCart(); // Wyświetlenie koszyka, gdy brak akcji
}
?>
