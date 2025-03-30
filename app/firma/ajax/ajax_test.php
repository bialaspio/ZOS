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
    $dbh = new PDO(
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

// Pobranie danych wejściowych
$kod = $_POST["kod"] ?? null;

if (!$kod) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganego parametru
    echo json_encode(['error' => 'Brak wymaganego parametru: kod']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    $stmt = $dbh->prepare("SELECT DISTINCT miejscowosc FROM adresy WHERE kod_pocztowy = :kod");
    $stmt->bindParam(':kod', $kod, PDO::PARAM_STR);

    // Wykonanie zapytania
    $stmt->execute();

    // Przetwarzanie wyników
    $odpowiedz = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($odpowiedz) {
        // Tworzy listę miejscowości
        $listaMiejscowosci = array_column($odpowiedz, "miejscowosc");

        // Zwraca wynik w formacie JSON
        echo json_encode([
            "miejscowosci" => $listaMiejscowosci
        ]);
    } else {
        echo json_encode(['miejscowosci' => []]); // Zwraca pustą listę, jeśli brak wyników
    }
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>