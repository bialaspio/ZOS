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

if (!$ulica) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganego parametru
    echo json_encode(['error' => 'Brak wymaganego parametru: Ulica']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    if ($kod && $miejscowosc) {
        $sql = "
            SELECT DISTINCT ulica 
            FROM adresy 
            WHERE ulica LIKE :part AND kod_pocztowy = :kod AND miejscowosc = :miej 
            ORDER BY ulica
        ";
        $zapytanie = $baza_danych->prepare($sql);
        $ulica = $ulica . '%';
        $zapytanie->bindParam(':part', $ulica, PDO::PARAM_STR);
        $zapytanie->bindParam(':miej', $miejscowosc, PDO::PARAM_STR);
        $zapytanie->bindParam(':kod', $kod, PDO::PARAM_STR);
    } else {
        $sql = "
            SELECT DISTINCT ulica 
            FROM adresy 
            WHERE ulica LIKE :part 
            ORDER BY ulica
        ";
        $zapytanie = $baza_danych->prepare($sql);
        $ulica = $ulica . '%';
        $zapytanie->bindParam(':part', $ulica, PDO::PARAM_STR);
    }

    // Wykonanie zapytania
    $zapytanie->execute();
    $przewidywane_ulice = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($przewidywane_ulice ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>