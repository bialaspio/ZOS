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
$id = $_GET['id'] ?? null;
$start = $_GET['start'] ?? null;
$koniec = $_GET['koniec'] ?? null;

if (!$start || !$koniec) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganych parametrów
    echo json_encode(['error' => 'Brak wymaganych parametrów: start, koniec']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    $id_condition = ($id && $id !== '*') ? " AND fa.id = :id" : "";
    $sql = "
        SELECT
            replace(pw.nr_prot_data::varchar, '-', '/') || '/' || pw.nr_prot_nr AS nr_zlecenia,
            fa.nazwa,
            fa.nip,
            'Ul. ' || sza.ulica || ' ' || sza.numer || ', ' || sza.miejscowosc || ' ' || sza.kod_pocztowy::varchar AS adres,
            pw.data_wywozu,
            pw.ilosc_sciekow,
            o.nazwa_oczysz
        FROM protokol_wywozu pw
        INNER JOIN firmy_adresy fa ON pw.firma_id = fa.id
        INNER JOIN szamba_adresy sza ON pw.id_szamba = sza.id_szamba
        INNER JOIN oczyszczalnia o ON o.id_oczysz = pw.id_oczyszcz
        WHERE pw.data_wywozu BETWEEN :start AND :koniec
        $id_condition
    ";

    $stmt = $db->prepare($sql);

    // Bindowanie parametrów
    $stmt->bindParam(':start', $start, PDO::PARAM_STR);
    $stmt->bindParam(':koniec', $koniec, PDO::PARAM_STR);

    if ($id && $id !== '*') {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    }

    // Wykonanie zapytania
    $stmt->execute();

    // Przekształcenie wyników do JSON
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników
    echo json_encode($results ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>