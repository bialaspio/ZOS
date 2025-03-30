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
$ulica = $_POST["ulica"] ?? null;
$numer = $_POST["numer"] ?? null;
$miejscowosc = $_POST["miejscowosc"] ?? null;
$kod_pocztowy = $_POST["kod_pocztowy"] ?? null;

if (!$ulica || !$numer || !$miejscowosc || !$kod_pocztowy) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganych danych wejściowych
    echo json_encode(['error' => 'Brak wymaganych parametrów: ulica, numer, miejscowosc, kod_pocztowy']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    $sql = "
        SELECT STRING_AGG(DISTINCT o.imie || ' ' || o.nazwisko, ', ') AS imie_nazwisko, 
               a.ulica, a.numer, a.miejscowosc, a.kod_pocztowy, 
               s.pojemnosc_m3, s.id_szamba
        FROM osoba o
        INNER JOIN osoba_adres oa ON o.id_osoby = oa.id_osoby 
        INNER JOIN adresy a ON a.ogc_fid = oa.id_adr_zamiesz 
        INNER JOIN adres_szamba asz ON asz.id_adres = oa.id_adr_zamiesz 
        INNER JOIN szamba s ON s.id_szamba = asz.id_szambo
        WHERE a.ulica = :ulica 
          AND a.numer = :numer 
          AND a.miejscowosc = :miejscowosc 
          AND a.kod_pocztowy = :kod_pocztowy 
          AND o.wlasciciel = true
        GROUP BY a.ulica, a.numer, a.miejscowosc, a.kod_pocztowy, s.pojemnosc_m3, s.id_szamba
    ";
    $zapytanie = $baza_danych->prepare($sql);

    // Bindowanie parametrów
    $zapytanie->bindParam(':ulica', $ulica, PDO::PARAM_STR);
    $zapytanie->bindParam(':numer', $numer, PDO::PARAM_STR);
    $zapytanie->bindParam(':miejscowosc', $miejscowosc, PDO::PARAM_STR);
    $zapytanie->bindParam(':kod_pocztowy', $kod_pocztowy, PDO::PARAM_STR);

    // Wykonanie zapytania
    $zapytanie->execute();
    $przewidywane_imiona = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($przewidywane_imiona ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>