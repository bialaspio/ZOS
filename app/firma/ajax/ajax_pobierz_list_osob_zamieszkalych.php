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
$id_budynku = $_POST["id_budynku"] ?? null;

if (!$id_budynku) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku danych wejściowych
    echo json_encode(['error' => 'Brak wymaganego parametru: id_budynku']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    $sql = "
        SELECT 
            o.imie, o.nazwisko,
            a.ulica, a.numer, a.kod_pocztowy, a.miejscowosc
        FROM budynki_egib be 
        LEFT JOIN adr_bud ab ON be.ogc_fid = ab.adr_bud 
        LEFT JOIN adresy a ON a.ogc_fid = ab.id_adr 
        LEFT JOIN osoba_adres oa ON oa.id_adr_zamiesz = ab.id_adr 
        LEFT JOIN osoba o ON o.id_osoby = oa.id_osoby
        WHERE be.id_budynku = :id_budynku
    ";
    $stmt = $baza_danych->prepare($sql);

    // Bindowanie parametrów
    $stmt->bindParam(':id_budynku', $id_budynku, PDO::PARAM_INT);

    // Wykonanie zapytania
    $stmt->execute();
    $przewidywane_dane = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($przewidywane_dane ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>