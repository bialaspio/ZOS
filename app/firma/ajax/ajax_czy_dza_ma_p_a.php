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
$id_dzialki = $_POST['id_dzialki'] ?? null;

if (!$id_dzialki) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku danych
    echo json_encode(['error' => 'Brak wymaganego parametru: id_dzialki']);
    exit;
}

try {
    // Przygotowanie i wykonanie zapytania
    $zapytanie = $baza_danych->prepare("
        SELECT DISTINCT true AS czy_p_a 
        FROM dzialki d
        INNER JOIN adresy a ON ST_Within(a.wkb_geometry, d.wkb_geometry)
        WHERE d.id_dzialki LIKE :id_dzialki
    ");
    $zapytanie->bindParam(':id_dzialki', $id_dzialki, PDO::PARAM_STR);
    $zapytanie->execute();

    $czy_jest_p_a = $zapytanie->fetchAll();

    // Zwrócenie wyniku w formacie JSON
    echo json_encode($czy_jest_p_a ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd zapytania do bazy danych: ' . $e->getMessage()]);
}
?>