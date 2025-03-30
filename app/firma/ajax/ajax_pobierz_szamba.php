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

try {
    // Przygotowanie zapytania SQL
    $sql = "
        SELECT 
            STRING_AGG(o.imie || ' ' || o.nazwisko, ',') AS im_naz,
            a.kod_pocztowy, a.miejscowosc, a.ulica, a.numer,
            s.pojemnosc_m3, s.rodzaj_nieczystosci,
            STRING_AGG(f.nazwa || ' - NIP: ' || f.nip, ',') AS fir_nip
        FROM szamba s
        INNER JOIN adres_szamba as2 ON s.id_szamba = as2.id_szambo
        INNER JOIN adresy a ON a.ogc_fid = as2.id_adres
        INNER JOIN osoba_adres oa ON oa.id_adr_zamel = as2.id_adres
        INNER JOIN osoba o ON o.id_osoby = oa.id_osoby
        INNER JOIN umowy u ON u.id_szambo = s.id_szamba
        INNER JOIN firmy f ON f.id = u.id_firma
        WHERE o.wlasciciel IS TRUE
        GROUP BY s.id_szamba, s.pojemnosc_m3, s.rodzaj_nieczystosci, a.kod_pocztowy, a.miejscowosc, a.ulica, a.numer
        ORDER BY s.id_szamba
    ";
    $zapytanie = $baza_danych->prepare($sql);

    // Wykonanie zapytania
    $zapytanie->execute();
    $przewidywane_dane_firmy = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($przewidywane_dane_firmy ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>