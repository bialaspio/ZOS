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
    $baza_danych = new PDO(
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
$IdSzamba = $_POST["IdSzamba"] ?? null;

if (!$IdSzamba) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku danych wejściowych
    echo json_encode(['error' => 'Brak wymaganego parametru: IdSzamba']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    $sql = "SELECT (t).* FROM (SELECT szamba_info(:IdSzamba) AS t) AS s;";
    $zapytanie = $baza_danych->prepare($sql);

    // Bindowanie parametrów
    $zapytanie->bindParam(':IdSzamba', $IdSzamba, PDO::PARAM_INT);

    // Wykonanie zapytania
    $zapytanie->execute();
    $przewidywane_dane_firmy = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($przewidywane_dane_firmy ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>