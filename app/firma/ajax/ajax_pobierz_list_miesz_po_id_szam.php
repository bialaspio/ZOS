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
$id_szamba = $_POST["id_szamba"] ?? null;

if (!$id_szamba) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku danych wejściowych
    echo json_encode(['error' => 'Brak wymaganego parametru: id_szamba']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    $sql = "
        SELECT o.imie, o.nazwisko, a.ulica, a.numer, a.kod_pocztowy, a.miejscowosc, o.wlasciciel
        FROM osoba o
        INNER JOIN osoba_adres oa ON o.id_osoby = oa.id_osoby
        INNER JOIN adresy a ON a.ogc_fid = oa.id_adr_zamiesz
        INNER JOIN adres_szamba as2 ON as2.id_adres = oa.id_adr_zamiesz
        INNER JOIN szamba s ON s.id_szamba = as2.id_szambo
        WHERE s.id_szamba = :id_szamba
    ";
    $stmt = $baza_danych->prepare($sql);

    // Bindowanie parametrów
    $stmt->bindParam(':id_szamba', $id_szamba, PDO::PARAM_INT);

    // Wykonanie zapytania
    $stmt->execute();
    $przewidywane_dane_szamba = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($przewidywane_dane_szamba ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>