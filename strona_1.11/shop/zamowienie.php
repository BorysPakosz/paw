<?php
session_start();
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Sprawdzenie, czy koszyk jest pusty
if (empty($_SESSION['koszyk'])) {
    echo "<p>Koszyk jest pusty. Nie można złożyć zamówienia.</p>";
    echo "<a href='index.php'>Wróć do sklepu</a>";
    exit;
}

// Funkcja wysyłki e-maila z potwierdzeniem zamówienia
function wyslijPotwierdzenie($email, $zamowienie) {
    $mail = new PHPMailer(true);

    try {
        // Konfiguracja serwera SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'pakoszborys9@gmail.com';
        $mail->Password = 'cwyciwpftztenlzh'; // Wprowadź wygenerowane hasło aplikacji
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // lub ENCRYPTION_SMTPS
        $mail->Port = 587; 
 

        // Odbiorca i nadawca
        $mail->setFrom('pakoszborys9@gmail.com', 'Sklep Internetowy');
        $mail->addAddress($email);

        // Treść e-maila
        $mail->isHTML(true);
        $mail->Subject = 'Potwierdzenie zamówienia';
        $mail->Body = "
            <h2>Potwierdzenie zamówienia</h2>
            <p>Dziękujemy za złożenie zamówienia w naszym sklepie!</p>
            <p>Twoje zamówienie:</p>
            {$zamowienie}
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Nie udało się wysłać e-maila. Błąd: {$mail->ErrorInfo}";
        return false;
    }
}

// Składanie zamówienia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo "<p>Podano nieprawidłowy adres e-mail.</p>";
        echo "<a href='zamowienie.php'>Wróć do formularza zamówienia</a>";
        exit;
    }

    // Generowanie treści zamówienia
    $zamowienieHTML = "<table border='1'>
        <tr>
            <th>Nazwa</th>
            <th>Cena brutto</th>
            <th>Ilość</th>
            <th>Razem</th>
        </tr>";
    $suma = 0;

    foreach ($_SESSION['koszyk'] as $produkt) {
        $razem = $produkt['cena_brutto'] * $produkt['ilosc'];
        $suma += $razem;
        $zamowienieHTML .= "<tr>
            <td>{$produkt['nazwa']}</td>
            <td>{$produkt['cena_brutto']} zł</td>
            <td>{$produkt['ilosc']}</td>
            <td>{$razem} zł</td>
        </tr>";
    }

    $zamowienieHTML .= "</table>";
    $zamowienieHTML .= "<p>Łączna wartość zamówienia: {$suma} zł</p>";

    // Wysłanie potwierdzenia zamówienia
    if (wyslijPotwierdzenie($email, $zamowienieHTML)) {
        echo "<p>Zamówienie zostało złożone. Potwierdzenie wysłano na adres: {$email}</p>";
        echo "<a href='index.php'>Wróć do sklepu</a>";

        // Czyszczenie koszyka
        unset($_SESSION['koszyk']);
    } else {
        echo "<p>Nie udało się wysłać potwierdzenia zamówienia. Skontaktuj się z administratorem.</p>";
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Składanie zamówienia</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../js/toggleTheme.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../js/navbarEffects.js"></script>
</head>
<body>
    <h1>Składanie zamówienia</h1>
    <form method="POST" action="zamowienie.php">
        <label>Adres e-mail:<br>
            <input type="email" name="email" required>
        </label><br><br>
        <button type="submit">Złóż zamówienie</button>
    </form>
</body>
</html>
