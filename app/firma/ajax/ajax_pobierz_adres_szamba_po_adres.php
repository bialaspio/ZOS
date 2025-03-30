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
$CzescAdres = $_POST["CzescAdres"] ?? null;

if (!$CzescAdres) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku danych wejściowych
    echo json_encode(['error' => 'Brak wymaganego parametru: CzescAdres']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    $sql = "
        SELECT DISTINCT 
            a.ogc_fid AS id_obiektu, 
            a.kod_pocztowy, 
            a.miejscowosc, 
            a.ulica, 
            a.numer 
        FROM adresy a
        INNER JOIN kontrole k ON k.adres_nier_id = a.ogc_fid
        WHERE 
            (a.kod_pocztowy || ' ' || a.miejscowosc || ' ' || a.ulica || ' ' || a.numer LIKE :CzescAdres)
            OR 
            (a.kod_pocztowy || ' ' || a.miejscowosc || ' ' || a.numer LIKE :CzescAdres)
    ";
    $zapytanie = $baza_danych->prepare($sql);

    // Bindowanie parametrów
    $CzescAdres = '%' . $CzescAdres . '%';
    $zapytanie->bindParam(':CzescAdres', $CzescAdres, PDO::PARAM_STR);

    // Wykonanie zapytania
    $zapytanie->execute();
    $przewidywane_dane_adresowe = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($przewidywane_dane_adresowe ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>