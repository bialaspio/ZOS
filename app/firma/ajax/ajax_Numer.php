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
$ulica = $_POST["Ulica"] ?? null;
$miejscowosc = $_POST["Miejscowosc"] ?? null;
$kod = $_POST["Kod"] ?? null;
$numer = $_POST["Numer"] ?? null;

if (!$numer) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku danych wejściowych
    echo json_encode(['error' => 'Brak wymaganego parametru: Numer']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    if ($kod && $miejscowosc && $ulica) {
        $sql = "
            SELECT DISTINCT numer 
            FROM adresy 
            WHERE numer LIKE :numer 
              AND ulica LIKE :ulica 
              AND kod_pocztowy = :kod 
              AND miejscowosc = :miejscowosc 
            ORDER BY numer
        ";
        $zapytanie = $baza_danych->prepare($sql);
        $numer = $numer . '%';
        $zapytanie->bindParam(':numer', $numer, PDO::PARAM_STR);
        $zapytanie->bindParam(':ulica', $ulica, PDO::PARAM_STR);
        $zapytanie->bindParam(':kod', $kod, PDO::PARAM_STR);
        $zapytanie->bindParam(':miejscowosc', $miejscowosc, PDO::PARAM_STR);
    } elseif ($miejscowosc) {
        $sql = "
            SELECT DISTINCT numer 
            FROM adresy 
            WHERE numer LIKE :numer 
              AND miejscowosc LIKE :miejscowosc 
            ORDER BY numer
        ";
        $zapytanie = $baza_danych->prepare($sql);
        $numer = $numer . '%';
        $zapytanie->bindParam(':numer', $numer, PDO::PARAM_STR);
        $zapytanie->bindParam(':miejscowosc', $miejscowosc, PDO::PARAM_STR);
    } else {
        $sql = "
            SELECT DISTINCT numer 
            FROM adresy 
            WHERE numer LIKE :numer 
            ORDER BY numer
        ";
        $zapytanie = $baza_danych->prepare($sql);
        $numer = $numer . '%';
        $zapytanie->bindParam(':numer', $numer, PDO::PARAM_STR);
    }

    // Wykonanie zapytania
    $zapytanie->execute();
    $przewidywane_numery = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($przewidywane_numery ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>