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

// Pobranie i walidacja danych wejściowych
$imie = $_POST['Imie'] ?? null;

if (!$imie) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku danych
    echo json_encode(['error' => 'Brak wymaganego parametru: Imie']);
    exit;
}

try {
    // Przygotowanie i wykonanie zapytania SQL
    $zapytanie = $baza_danych->prepare("
        SELECT 
            o.imie, 
            o.nazwisko, 
            a.kod_pocztowy, 
            a.miejscowosc, 
            a.ulica, 
            a.numer, 
            o.id_osoby 
        FROM osoba o
        LEFT JOIN osoba_adres oa ON oa.id_osoby = o.id_osoby 
        LEFT JOIN adresy a ON a.ogc_fid = oa.id_adr_zamel 
        WHERE o.imie ILIKE :imie 
        ORDER BY o.imie 
        LIMIT 40
    ");
    $imieParam = '%' . $imie . '%';
    $zapytanie->bindParam(':imie', $imieParam, PDO::PARAM_STR);
    $zapytanie->execute();

    $przewidywane_dane_osoba = $zapytanie->fetchAll();

    // Zwrócenie wyniku w formacie JSON
    echo json_encode($przewidywane_dane_osoba ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd zapytania do bazy danych: ' . $e->getMessage()]);
}
?>