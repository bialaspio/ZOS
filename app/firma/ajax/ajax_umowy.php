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
    $id_condition = ($id && $id !== '*') ? " AND f.id = :id" : "";
    $sql = "
        SELECT 
            f.nazwa, 
            f.nip, 
            u.umowa_od, 
            u.umowa_do,
            CASE 
                WHEN sza.ulica IS NULL OR sza.ulica = '' 
                THEN sza.miejscowosc || ' ' || sza.numer || ', ' || sza.miejscowosc || ' ' || sza.kod_pocztowy 
                ELSE 'Ul. ' || sza.ulica || ' ' || sza.numer || ', ' || sza.miejscowosc || ' ' || sza.kod_pocztowy 
            END AS adres, 
            sza.pojemnosc_m3
        FROM firmy f
        LEFT JOIN umowy u ON f.id = u.id_firma 
            AND u.umowa_od <= :koniec 
            AND u.umowa_do >= :start
        INNER JOIN szamba_adresy sza ON u.id_szambo = sza.id_szamba
        WHERE 1=1 $id_condition
        ORDER BY f.nazwa
    ";

    $zapytanie = $baza_danych->prepare($sql);

    // Bindowanie parametrów
    $zapytanie->bindParam(':start', $start, PDO::PARAM_STR);
    $zapytanie->bindParam(':koniec', $koniec, PDO::PARAM_STR);
    if ($id && $id !== '*') {
        $zapytanie->bindParam(':id', $id, PDO::PARAM_INT);
    }

    // Wykonanie zapytania
    $zapytanie->execute();
    $wyniki = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($wyniki ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>