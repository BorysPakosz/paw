
<?php
// Funkcja dodawania produktu do koszyka
function addToCart($id, $nazwa, $cena_brutto, $ilosc) {
    if (!isset($_SESSION['koszyk'])) {
        $_SESSION['koszyk'] = [];
    }

    foreach ($_SESSION['koszyk'] as &$produkt) {
        if ($produkt['id'] == $id) {
            $produkt['ilosc'] += $ilosc;
            return;
        }
    }

    $_SESSION['koszyk'][] = ['id' => $id, 'nazwa' => $nazwa, 'cena_brutto' => $cena_brutto, 'ilosc' => $ilosc];
}

// Funkcja usuwania produktu z koszyka
function removeFromCart($id) {
    if (!isset($_SESSION['koszyk'])) {
        return;
    }

    foreach ($_SESSION['koszyk'] as $index => $produkt) {
        if ($produkt['id'] == $id) {
            unset($_SESSION['koszyk'][$index]);
            $_SESSION['koszyk'] = array_values($_SESSION['koszyk']); // Reindeksacja
            return;
        }
    }
}

// Funkcja aktualizacji ilości produktu
function updateCartQuantity($id, $ilosc) {
    if (!isset($_SESSION['koszyk'])) {
        return;
    }

    foreach ($_SESSION['koszyk'] as &$produkt) {
        if ($produkt['id'] == $id) {
            $produkt['ilosc'] = $ilosc; 
            return;
        }
    }
}

// Funkcja wyświetlania zawartości koszyka
function showCart() {
    if (empty($_SESSION['koszyk'])) {
        echo "<p>Koszyk jest pusty.</p>";
        return;
    }

    echo "<table border='1'>
        <tr>
            <th>Nazwa</th>
            <th>Cena brutto</th>
            <th>Ilość</th>
            <th>Razem</th>
            <th>Akcje</th>
        </tr>";

    $suma = 0;

    foreach ($_SESSION['koszyk'] as $produkt) {
        $razem = $produkt['cena_brutto'] * $produkt['ilosc'];
        $suma += $razem;

        echo "<tr>
            <td>{$produkt['nazwa']}</td>
            <td>{$produkt['cena_brutto']} zł</td>
            <td>{$produkt['ilosc']}</td>
            <td>{$razem} zł</td>
            <td>
                <a href='koszyk.php?action=usun&id={$produkt['id']}'>Usuń</a>
                <a href='koszyk.php?action=edytuj&id={$produkt['id']}'>Zmień ilość</a>
            </td>
        </tr>";
    }

    echo "</table>";
    echo "<p>Łączna wartość koszyka: {$suma} zł</p>";
    echo "<a href='zamowienie.php'>Złóż zamówienie</a>";
}
    echo "<a href='index.php'>Powrót do sklepu</a>";
?>

