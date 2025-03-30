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
$kod = $_POST["KodPocz"] ?? null;
$miejscowosc = $_POST["Miejscowosc"] ?? null;

if (!$kod) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku danych wejściowych
    echo json_encode(['error' => 'Brak wymaganego parametru: KodPocz']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    if ($miejscowosc) {
        $sql = "
            SELECT DISTINCT kod_pocztowy 
            FROM adresy 
            WHERE kod_pocztowy LIKE :kod AND miejscowosc = :miejscowosc 
            ORDER BY kod_pocztowy
        ";
        $zapytanie = $baza_danych->prepare($sql);
        $kod = $kod . '%';
        $zapytanie->bindParam(':kod', $kod, PDO::PARAM_STR);
        $zapytanie->bindParam(':miejscowosc', $miejscowosc, PDO::PARAM_STR);
    } else {
        $sql = "
            SELECT DISTINCT kod_pocztowy 
            FROM adresy 
            WHERE kod_pocztowy LIKE :kod 
            ORDER BY kod_pocztowy
        ";
        $zapytanie = $baza_danych->prepare($sql);
        $kod = $kod . '%';
        $zapytanie->bindParam(':kod', $kod, PDO::PARAM_STR);
    }

    // Wykonanie zapytania
    $zapytanie->execute();
    $przewidywane_kody = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($przewidywane_kody ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>