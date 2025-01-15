<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<?php
session_start();
require '../cfg.php'; // Wczytaj dane logowania

// Funkcja logowania
function FormularzLogowania($error = '') {
    echo "<h2>Logowanie</h2>";
    if ($error) echo "<p style='color: red;'>$error</p>";
    echo "
        <form method='POST'>
            <label>Login: <input type='text' name='login'></label><br>
            <label>Hasło: <input type='password' name='password'></label><br>
            <input type='submit' value='Zaloguj'>
        </form>
    ";
}

// Obsługa logowania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['logged_in'])) {
    if ($_POST['login'] === $GLOBALS['login'] && $_POST['password'] === $GLOBALS['pass']) {
        $_SESSION['logged_in'] = true;
    } else {
        FormularzLogowania("Nieprawidłowe dane logowania.");
        exit;
    }
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    FormularzLogowania();
    exit;
}

// Połączenie z bazą danych
$db = new mysqli("localhost", "root", "", "moja_strona");
if ($db->connect_error) {
    die("Błąd połączenia z bazą danych: " . $db->connect_error);
}

// Funkcja wyświetlania listy podstron
function ListaPodstron($db) {
    $query = "SELECT id, page_title FROM page_list";
    $result = $db->query($query);
    echo "<h2>Lista podstron</h2>";
    echo "<a href='?action=add_page'>Dodaj nową podstronę</a><br><br>";
    echo "<table border='1'><tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['page_title']}</td>
            <td>
                <a href='?action=edit_page&id={$row['id']}'>Edytuj</a> | 
                <a href='?action=delete_page&id={$row['id']}'>Usuń</a>
            </td>
        </tr>";
    }
    echo "</table>";
}

// Funkcja zarządzania kategoriami
function ListaKategorii($db) {
    $query = "SELECT * FROM categories";
    $result = $db->query($query);

    echo "<h2>Lista kategorii</h2>";
    echo "<a href='?action=add_category'>Dodaj nową kategorię</a><br><br>";

    // Wyświetlenie tabeli
    echo "<table border='1'>";
    echo "<tr>
        <th>ID</th>
        <th>Matka</th>
        <th>Nazwa</th>
        <th>Akcje</th>
    </tr>";

    // Zbieranie danych do hierarchii
    $kategorie = [];
    while ($row = $result->fetch_assoc()) {
        $kategorie[] = $row;
    }

    // Funkcja rekurencyjna do budowania hierarchii
    function WyswietlKategorie($kategorie, $parent_id = 0, $level = 0) {
        foreach ($kategorie as $kategoria) {
            if ($kategoria['matka'] == $parent_id) {
                echo "<tr>
                    <td>{$kategoria['id']}</td>
                    <td>{$kategoria['matka']}</td>
                    <td>" . str_repeat('--', $level) . " {$kategoria['nazwa']}</td>
                    <td>
                        <a href='?action=edit_category&id={$kategoria['id']}'>Edytuj</a> | 
                        <a href='?action=delete_category&id={$kategoria['id']}'>Usuń</a>
                    </td>
                </tr>";
                // Wywołanie rekurencyjne dla podkategorii
                WyswietlKategorie($kategorie, $kategoria['id'], $level + 1);
            }
        }
    }

    // Wyświetlenie kategorii zaczynając od głównych
    WyswietlKategorie($kategorie);

    echo "</table>";
}

// Funkcja dodawania nowej kategorii
function DodajKategorie($db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $kategorie = explode("\n", $_POST['categories']); // Pobieramy kategorie z pola tekstowego
        $parent_id = $_POST['matka']; // ID matki kategorii

        // Przetwarzanie każdej kategorii
        foreach ($kategorie as $kategoria) {
            $kategoria = trim($kategoria); // Usuwamy białe znaki
            if (!empty($kategoria)) {
                $stmt = $db->prepare("INSERT INTO categories (nazwa, matka) VALUES (?, ?)");
                $stmt->bind_param("si", $kategoria, $parent_id);
                $stmt->execute();
            }
        }

        echo "<p>Dodano kategorie.</p>";
        echo "<a href='?action=list_categories'>Powrót do listy kategorii</a>";
        return;
    }

    // Formularz do dodawania kategorii
    echo "
    <h2>Dodaj nowe kategorie</h2>
    <form method='POST'>
        <label>Kategorie (każda w nowej linii):<br>
            <textarea name='categories' rows='10' cols='30'></textarea>
        </label><br>
        <label>Matka kategorii (ID): 
            <input type='number' name='matka' value='0'>
        </label><br><br>
        <input type='submit' value='Dodaj kategorie'>
        <a href='?action=list_categories'><button type='button'>Anuluj</button></a>
    </form>";
}


// Funkcja edycji kategorii
function EdytujKategorie($db, $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nazwa = $_POST['nazwa'];
        $matka = $_POST['matka'] ?: 0;

        $stmt = $db->prepare("UPDATE categories SET nazwa = ?, matka = ? WHERE id = ? LIMIT 1");
        $stmt->bind_param("sii", $nazwa, $matka, $id);
        $stmt->execute();

        echo "<p>Kategoria została zaktualizowana.</p>";
        echo "<a href='?action=list_categories'>Powrót do listy kategorii</a>";
    } else {
        $stmt = $db->prepare("SELECT nazwa, matka FROM categories WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        echo "
        <h2>Edytuj kategorię</h2>
        <form method='POST'>
            <label>Nazwa: <input type='text' name='nazwa' value='{$result['nazwa']}'></label><br>
            <label>Rodzic (ID): <input type='text' name='matka' value='{$result['matka']}'></label><br>
            <input type='submit' value='Zapisz zmiany'> 
            <a href='?action=list_categories'><button type='button'>Anuluj</button></a>
        </form>";
    }
}

// Funkcja usuwania kategorii
function UsunKategorie($db, $id) {
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<p>Kategoria została usunięta.</p>";
    echo "<a href='?action=list_categories'>Powrót do listy kategorii</a>";
}

function UsunPodstrone($db, $id) {
    $stmt = $db->prepare("DELETE FROM page_list WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<p>Podstrona została usunięta.</p>";
    echo "<a href='?action=list_pages'>Powrót do listy podstron</a>";
}
function EdytujPodstrone($db, $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['page_title'];
        $content = $_POST['page_content'];
        $status = isset($_POST['status']) ? 1 : 0;

        $stmt = $db->prepare("UPDATE page_list SET page_title = ?, page_content = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssii", $title, $content, $status, $id);
        $stmt->execute();

        echo "<p>Podstrona została zaktualizowana.</p>";
        echo "<a href='?action=list_pages'>Powrót do listy podstron</a>";
    } else {
        $query = "SELECT * FROM page_list WHERE id = $id";
        $result = $db->query($query)->fetch_assoc();

        echo "
        <h2>Edytuj podstronę</h2>
        <form method='POST'>
            <label>Tytuł: <input type='text' name='page_title' value='{$result['page_title']}'></label><br>
            <label>Treść: <textarea name='page_content' style='height: 300px;'>{$result['page_content']}</textarea></label><br>
            <label>Aktywna: <input type='checkbox' name='status' " . ($result['status'] ? 'checked' : '') . "></label><br>
            <input type='submit' value='Zapisz zmiany'>
            <a href='?action=list_pages'><button type='button'>Anuluj</button></a>
        </form>";
    }
}

function DodajNowaPodstrone($db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['page_title'];
        $content = $_POST['page_content'];
        $status = isset($_POST['status']) ? 1 : 0;

        $stmt = $db->prepare("INSERT INTO page_list (page_title, page_content, status) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $content, $status);
        $stmt->execute();

        echo "<p>Nowa podstrona została dodana.</p>";
        echo "<a href='?action=list_pages'>Powrót do listy</a>";
    } else {
        echo "
        <h2>Dodaj nową podstronę</h2>
        <form method='POST'>
            <label>Tytuł: <input type='text' name='page_title'></label><br>
            <label>Treść: <textarea name='page_content' style='height: 300px;'></textarea></label><br>
            <label>Aktywna: <input type='checkbox' name='status'></label><br>
            <input type='submit' value='Dodaj podstronę'>
            <a href='?action=list_pages'><button type='button'>Anuluj</button></a>
        </form>";
    }
}
function PokazProdukty($db) {
    $query = "SELECT p.*, c.nazwa AS kategoria_nazwa FROM produkty p LEFT JOIN categories c ON p.kategoria = c.id";
    $result = $db->query($query);

    echo "<h2>Lista produktów</h2>";
    echo "<a href='?action=dodaj_produkt'>Dodaj nowy produkt</a><br><br>";
    echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Tytuł</th>
            <th>Opis</th>
            <th>Cena netto</th>
            <th>VAT</th>
            <th>Ilość w magazynie</th>
            <th>Status</th>
            <th>Kategoria</th>
            <th>Gabaryt</th>
            <th>Zdjęcie</th>
            <th>Data utworzenia</th>
            <th>Data modyfikacji</th>
            <th>Data wygaśnięcia</th>
            <th>Akcje</th>
        </tr>";
    while ($row = $result->fetch_assoc()) {
        $status = $row['status_dostepnosci'] ? 'Dostępny' : 'Niedostępny';
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['tytul']}</td>
            <td>{$row['opis']}</td>
            <td>{$row['cena_netto']}</td>
            <td>{$row['podatek_vat']}%</td>
            <td>{$row['ilosc_magazyn']}</td>
            <td>{$status}</td>
            <td>{$row['kategoria_nazwa']}</td>
            <td>{$row['gabaryt']}</td>
            <td><img src='{$row['zdjecie']}' alt='Zdjęcie produktu' style='max-width:100px;'></td>
            <td>{$row['data_utworzenia']}</td>
            <td>{$row['data_modyfikacji']}</td>
            <td>{$row['data_wygasniecia']}</td>
            <td>
                <a href='?action=edytuj_produkt&id={$row['id']}'>Edytuj</a> | 
                <a href='?action=usun_produkt&id={$row['id']}'>Usuń</a>
            </td>
        </tr>";
    }
    echo "</table>";
}

function UsunProdukt($db, $id) {
    $stmt = $db->prepare("DELETE FROM produkty WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<p>Produkt został usunięty.</p>";
    echo "<a href='?action=zarzadzaj_produktami'>Powrót do listy</a>";
}

function DodajProdukt($db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tytul = $_POST['tytul'];
        $opis = $_POST['opis'];
        $cena_netto = $_POST['cena_netto'];
        $podatek_vat = $_POST['podatek_vat'];
        $ilosc_magazyn = $_POST['ilosc_magazyn'];
        $status_dostepnosci = isset($_POST['status_dostepnosci']) ? 1 : 0;
        $kategoria = $_POST['kategoria'];
        $gabaryt = $_POST['gabaryt'];
        $zdjecie = $_POST['zdjecie'];
        $data_wygasniecia = $_POST['data_wygasniecia'];

        $stmt = $db->prepare("INSERT INTO produkty (tytul, opis, cena_netto, podatek_vat, ilosc_magazyn, status_dostepnosci, kategoria, gabaryt, zdjecie, data_wygasniecia)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiisisss", $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_magazyn, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie, $data_wygasniecia);
        $stmt->execute();

        echo "<p>Produkt został dodany.</p>";
        echo "<a href='?action=zarzadzaj_produktami'>Powrót do listy</a>";
    } else {
        // Form do dodania produktu
        echo "
        <h2>Dodaj nowy produkt</h2>
        <form method='POST'>
            <label>Tytuł: <input type='text' name='tytul'></label><br>
            <label>Opis: <textarea name='opis'></textarea></label><br>
            <label>Cena netto: <input type='number' step='0.01' name='cena_netto'></label><br>
            <label>Podatek VAT: <input type='number' name='podatek_vat'></label><br>
            <label>Ilość w magazynie: <input type='number' name='ilosc_magazyn'></label><br>
            <label>Status dostępności: <input type='checkbox' name='status_dostepnosci'></label><br>
            <label>Kategoria: <input type='number' name='kategoria'></label><br>
            <label>Gabaryt: <input type='text' name='gabaryt'></label><br>
            <label>Zdjęcie (URL): <input type='text' name='zdjecie'></label><br>
            <label>Data wygaśnięcia: <input type='date' name='data_wygasniecia'></label><br>
            <input type='submit' value='Dodaj produkt'>
        </form>";
    }
}
function EdytujProdukt($db, $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tytul = $_POST['tytul'];
        $opis = $_POST['opis'];
        $cena_netto = (float)$_POST['cena_netto'];
        $podatek_vat = (int)$_POST['podatek_vat'];
        $ilosc_magazyn = (int)$_POST['ilosc_magazyn'];
        $status_dostepnosci = isset($_POST['status_dostepnosci']) ? 1 : 0;
        $kategoria = $_POST['kategoria'];
        $gabaryt = $_POST['gabaryt'];
        $zdjecie = $_POST['zdjecie'];
        $data_wygasniecia = $_POST['data_wygasniecia'];

        $stmt = $db->prepare("UPDATE produkty SET tytul = ?, opis = ?, cena_netto = ?, podatek_vat = ?, ilosc_magazyn = ?, status_dostepnosci = ?, kategoria = ?, gabaryt = ?, zdjecie = ?, data_wygasniecia = ? WHERE id = ?");
        $stmt->bind_param("ssdiisisssi", $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_magazyn, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie, $data_wygasniecia, $id);
        $stmt->execute();

        echo "<p>Produkt został zaktualizowany.</p>";
        echo "<a href='?action=zarzadzaj_produktami'>Powrót do listy produktów</a>";
    } else {
        // Pobierz dane produktu, aby je wyświetlić w formularzu
        $stmt = $db->prepare("SELECT * FROM produkty WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $produkt = $result->fetch_assoc();

        echo "
        <h2>Edytuj produkt</h2>
        <form method='POST'>
            <label>Tytuł: <input type='text' name='tytul' value='{$produkt['tytul']}'></label><br>
            <label>Opis: <textarea name='opis'>{$produkt['opis']}</textarea></label><br>
            <label>Cena netto: <input type='number' step='0.01' name='cena_netto' value='{$produkt['cena_netto']}'></label><br>
            <label>Podatek VAT: <input type='number' name='podatek_vat' value='{$produkt['podatek_vat']}'></label><br>
            <label>Ilość w magazynie: <input type='number' name='ilosc_magazyn' value='{$produkt['ilosc_magazyn']}'></label><br>
            <label>Status dostępności: <input type='checkbox' name='status_dostepnosci' " . ($produkt['status_dostepnosci'] ? 'checked' : '') . "></label><br>
            <label>Kategoria: <input type='text' name='kategoria' value='{$produkt['kategoria']}'></label><br>
            <label>Gabaryt: <input type='text' name='gabaryt' value='{$produkt['gabaryt']}'></label><br>
            <label>Zdjęcie (link): <input type='text' name='zdjecie' value='{$produkt['zdjecie']}'></label><br>
            <label>Data wygaśnięcia: <input type='date' name='data_wygasniecia' value='{$produkt['data_wygasniecia']}'></label><br>
            <input type='submit' value='Zapisz zmiany'>
            <a href='?action=zarzadzaj_produktami'><button type='button'>Anuluj</button></a>
        </form>";
    }
}

// Obsługa działań
echo "<nav>
    <a href='?action=list_pages'>Zarządzaj podstronami</a> |
    <a href='?action=list_categories'>Zarządzaj kategoriami</a>|
    <a href='?action=zarzadzaj_produktami'>Zarządzaj produktami</a>
</nav><br>";

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        // Obsługa kategorii
        case 'list_categories':
            ListaKategorii($db);
            break;
        case 'add_category':
            DodajKategorie($db);
            break;
        case 'edit_category':
            EdytujKategorie($db, $_GET['id']);
            break;
        case 'delete_category':
            UsunKategorie($db, $_GET['id']);
            break;

        // Obsługa podstron
        case 'list_pages': 
            ListaPodstron($db);
            break;
        case 'edit_page': 
            EdytujPodstrone($db, $_GET['id']);
            break;
        case 'delete_page': 
            UsunPodstrone($db, $_GET['id']);
            break;
        case 'add_page': 
            DodajNowaPodstrone($db);
            break;

        case 'zarzadzaj_produktami':
            PokazProdukty($db);
            break;
        case 'dodaj_produkt':
            DodajProdukt($db);
            break;
        case 'usun_produkt':
            UsunProdukt($db, $_GET['id']);
            break;
        case 'edytuj_produkt':
            EdytujProdukt($db, $_GET['id']);
            break;
            
            
        default:
            echo "<p>Nieznane działanie.</p>";
            break;
    }
} else {
    // Wyświetlanie głównego menu
    echo "<h2>Panel administracyjny</h2>";
    echo "<ul>
        <li><a href='?action=list_categories'>Zarządzaj kategoriami</a></li>
        <li><a href='?action=list_pages'>Zarządzaj podstronami</a></li>
    </ul>";
}

?>

</body>
</html>
