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

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $osoba_kontrolujaca = $_POST['osoba_kontrolujaca'] ?? null;

    if (!$osoba_kontrolujaca) {
        http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganych danych wejściowych
        echo json_encode(['error' => 'Brak wymaganego parametru: osoba_kontrolujaca']);
        exit;
    }

    try {
        // Przygotowanie zapytania SQL
        $sql = "SELECT * FROM osoby_kontrolujace ok WHERE ok.imie || ' ' || ok.nazwisko LIKE :osoba_kontrolujaca";
        $stmt = $db->prepare($sql);

        // Bindowanie parametrów
        $osoba_kontrolujaca = $osoba_kontrolujaca . '%';
        $stmt->bindParam(':osoba_kontrolujaca', $osoba_kontrolujaca, PDO::PARAM_STR);

        // Wykonanie zapytania
        $stmt->execute();
        $rezultat = $stmt->fetch(PDO::FETCH_ASSOC);

        // Zwrócenie wyniku w formacie JSON
        echo json_encode((bool) $rezultat);
    } catch (PDOException $e) {
        http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
        echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
    }
}
?>