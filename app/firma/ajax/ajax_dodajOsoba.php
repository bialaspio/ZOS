<?php
// Funkcja do wczytywania parametrów połączenia z pliku konfiguracyjnego
function loadDatabaseConfig($configFile) {
    if (!file_exists($configFile)) {
        throw new Exception("Plik konfiguracyjny nie istnieje: $configFile");
    }

    $config = parse_ini_file($configFile);
    if (!$config) {
        throw new Exception("Nie można wczytać pliku konfiguracyjnego: $configFile");
    }

    return $config;
}

try {
    // Wczytanie parametrów połączenia z pliku connect_par.conf
    $config = loadDatabaseConfig('connect_par.conf');

    // Połączenie z bazą danych
    $db = new PDO(
        "pgsql:host={$config['host']};dbname={$config['dbname']}",
        $config['user'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Włącz tryb wyjątków dla błędów
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Ustaw domyślny tryb pobierania
        ]
    );
} catch (Exception $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z połączeniem
    echo json_encode(['error' => 'Błąd połączenia z bazą danych: ' . $e->getMessage()]);
    exit;
}

session_start();
// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401); // Zwróć kod błędu 401 dla niezalogowanego użytkownika
    echo json_encode(['error' => 'Sesja nie jest aktywna. Zaloguj się ponownie.']);
    exit();
}

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobranie i walidacja danych wejściowych
    $Imie = $_POST['Imie'] ?? null;
    $Nazwisko = $_POST['Nazwisko'] ?? null;
    $KodAdr = $_POST['KodAdrSzamba'] ?? null;
    $MiejscowoscAdr = $_POST['MiejscowoscAdrSzamba'] ?? null;
    $UlicaAdr = $_POST['UlicaAdrSzamba'] ?? null;
    $NrAdr = $_POST['NrAdrSzamba'] ?? null;
    $WlascicielSzamba = $_POST['WlascicielSzamba'] ?? null;
    $KodAdrZam = $_POST['KodAdrZamSzamba'] ?? null;
    $MiejscowoscAdrZam = $_POST['MiejscowoscAdrZamSzamba'] ?? null;
    $UlicaAdrZam = $_POST['UlicaAdrZamSzamba'] ?? null;
    $NrAdrZam = $_POST['NrAdrZamSzamba'] ?? null;

    // Walidacja wymaganych pól
    if (!$Imie || !$Nazwisko || !$KodAdr || !$MiejscowoscAdr || !$UlicaAdr || !$NrAdr || !$KodAdrZam || !$MiejscowoscAdrZam || !$UlicaAdrZam || !$NrAdrZam) {
        http_response_code(400); // Zwróć kod błędu 400 w przypadku braku danych
        echo json_encode(['error' => 'Brak wymaganych danych wejściowych']);
        exit;
    }

    try {
        // Przygotowanie zapytania SQL
        $sql = "
            SELECT dodaj_osobe_i_adres(
                :Imie, :Nazwisko, :KodAdr, :MiejscowoscAdr, :UlicaAdr, :NrAdr,
                :KodAdrZam, :MiejscowoscAdrZam, :UlicaAdrZam, :NrAdrZam, :WlascicielSzamba
            );
        ";
        $stmt = $db->prepare($sql);

        // Bindowanie parametrów
        $stmt->bindParam(':Imie', $Imie, PDO::PARAM_STR);
        $stmt->bindParam(':Nazwisko', $Nazwisko, PDO::PARAM_STR);
        $stmt->bindParam(':KodAdr', $KodAdr, PDO::PARAM_STR);
        $stmt->bindParam(':MiejscowoscAdr', $MiejscowoscAdr, PDO::PARAM_STR);
        $stmt->bindParam(':UlicaAdr', $UlicaAdr, PDO::PARAM_STR);
        $stmt->bindParam(':NrAdr', $NrAdr, PDO::PARAM_STR);
        $stmt->bindParam(':KodAdrZam', $KodAdrZam, PDO::PARAM_STR);
        $stmt->bindParam(':MiejscowoscAdrZam', $MiejscowoscAdrZam, PDO::PARAM_STR);
        $stmt->bindParam(':UlicaAdrZam', $UlicaAdrZam, PDO::PARAM_STR);
        $stmt->bindParam(':NrAdrZam', $NrAdrZam, PDO::PARAM_STR);
        $stmt->bindParam(':WlascicielSzamba', $WlascicielSzamba, PDO::PARAM_STR);

        // Wykonanie zapytania
        $stmt->execute();
        $rezultat = $stmt->fetch(PDO::FETCH_ASSOC);

        // Zwrócenie wyniku
        if ($rezultat) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } catch (PDOException $e) {
        http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
        echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405); // Zwróć kod błędu 405 dla metod innych niż POST
    echo json_encode(['error' => 'Metoda niedozwolona']);
}
?>