<?php
// Parametry połączenia z bazą danych __user__QL
$host = '__host__';
$dbname = '__dbname__';
$user_db = '__user__';
$password_db = '__passwd__';

// Pobierz dane z formularza logowania
$username = $_POST['user'];
$password = $_POST['passwd'];

// Połącz z bazą danych
$dsn = "pgsql:host=$host;dbname=$dbname";

try {
	$dbh = new PDO($dsn, $user_db, $password_db);

    // Sprawdź, czy użytkownik istnieje
    $stmt = $dbh->prepare("SELECT * FROM users WHERE login = :username");
	$stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($user) {
        // Sprawdź poprawność hasła
        if (password_verify($password, $user['haslo'])) {
            // Użytkownik zalogowany pomyślnie
             // Utwórz sesję dla zalogowanego użytkownika
            session_start();
            $_SESSION['username'] = $user['login'];
            $_SESSION['logged_in'] = true;

            // Przekieruj na stronę główną lub inną chronioną stronę
            header('Location: app/index_mapa.php');
			//header('Location: app/index_mapa_v001.php');
            exit();
			
			//session_start();
			//$_SESSION['logged_in'] = true;
			//header("location: http://192.168.0.94/geoserwer/app/index_mapa.php");
			
        } else {
            // Błędne hasło
			//header("location: http://192.168.0.94/geoserwer/index_el.html");
			header("location: /GSPG/index_el.html");
			//echo "Błędne hasło.";
        }
    } else {
        // Użytkownik nie istnieje
		//    header("location: http://192.168.0.94/geoserwer/index_el.html");
		  header("location: /GSPG/index_el.html");
		//echo "Użytkownik o podanej nazwie nie istnieje.";
    }
} catch (PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
}
?>