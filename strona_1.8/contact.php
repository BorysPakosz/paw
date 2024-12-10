<?php
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Contact {
    // Metoda wyświetlająca formularz kontaktowy
    public function PokazKontakt() {
        echo '<form action="contact.php" method="POST">
                <label for="email">Twój Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="message">Wiadomość:</label>
                <textarea id="message" name="message" required></textarea>
                
                <button type="submit" name="action" value="sendMail">Wyślij</button>
              </form>';
    }

    // Metoda wysyłająca mail kontaktowy za pomocą PHPMailer
    public function WyslijMailKontakt($email, $message) {
        $mail = new PHPMailer(true);
        try {
            // Konfiguracja serwera SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Serwer SMTP Gmaila
            $mail->SMTPAuth = true;
            $mail->Username = 'pakoszborys9@gmail.com'; // Twój email Gmail
            $mail->Password = 'cwyciwpftztenlzh'; // Hasło aplikacji Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Ustawienia nadawcy i odbiorcy
            $mail->setFrom($email, 'Formularz Kontaktowy');
            $mail->addAddress('pakoszborys9@gmail.com');

            // Treść wiadomości
            $mail->isHTML(true);
            $mail->Subject = 'Nowa wiadomość kontaktowa';
            $mail->Body = $message;

            $mail->send();
            echo "Wiadomość została wysłana!";
        } catch (Exception $e) {
            echo "Wystąpił błąd przy wysyłaniu wiadomości: {$mail->ErrorInfo}";
        }
    }

    // Metoda przypominająca hasło za pomocą PHPMailer
    public function PrzypomnijHaslo($email) {
        $mail = new PHPMailer(true);
        try {
            // Konfiguracja serwera SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'pakoszborys9@gmail.com';
            $mail->Password = 'wyciwpftztenlzh';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Ustawienia nadawcy i odbiorcy
            $mail->setFrom('pakoszborys9@gmail.com', 'Przypomnienie Hasła');
            $mail->addAddress($email);

            // Treść wiadomości
            $mail->isHTML(true);
            $mail->Subject = 'Przypomnienie hasła';
            $mail->Body = 'Twoje hasło do panelu admina to: admin';

            $mail->send();
            echo "Hasło zostało wysłane!";
        } catch (Exception $e) {
            echo "Wystąpił błąd przy wysyłaniu hasła: {$mail->ErrorInfo}";
        }
    }
}

// Obsługa żądań
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = new Contact();

    switch ($_POST['action']) {
        case 'sendMail':
            $contact->WyslijMailKontakt($_POST['email'], $_POST['message']);
            break;
        case 'remindPassword':
            $contact->PrzypomnijHaslo($_POST['email']);
            break;
    }
}
?>
