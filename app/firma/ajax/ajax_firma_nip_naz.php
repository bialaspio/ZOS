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

// Pobranie danych wejściowych
$nazwa_nip = $_POST["nazwa_nip"] ?? null;

if (!$nazwa_nip) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku danych wejściowych
    echo json_encode(['error' => 'Brak wymaganego parametru: nazwa_nip']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    $sql = "
        SELECT f.nazwa, f.nip, ai.ulica, ai.numer, ai.kod_pocztowy, ai.miejscowosc 
        FROM firmy f
        INNER JOIN adresy ai ON f.id_adres = ai.ogc_fid  
        WHERE f.nazwa || ' ' || f.nip LIKE :nazwa_nip
    ";
    $stmt = $db->prepare($sql);

    // Bindowanie parametrów
    $nazwa_nip = '%' . $nazwa_nip . '%';
    $stmt->bindParam(':nazwa_nip', $nazwa_nip, PDO::PARAM_STR);

    // Wykonanie zapytania
    $stmt->execute();
    $dane_z_zap = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($dane_z_zap ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>