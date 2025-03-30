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
$rodzajKontroli = $_POST["rodzajKontroli"] ?? null;
$id_wlascicel_szamba = $_POST["id_wlascicel_szamba"] ?? null;
$id_adr_ob_kon = $_POST["id_adr_ob_kon"] ?? null;

$whereConditions = [];
if (!empty($rodzajKontroli)) {
    $whereConditions[] = 'k.id_rodz_kon::varchar LIKE :rodzajKontroli';
}
if (!empty($id_wlascicel_szamba)) {
    $whereConditions[] = 'k.wlasciciel = :id_wlascicel_szamba';
}
if (!empty($id_adr_ob_kon)) {
    $whereConditions[] = 'k.adres_nier_id = :id_adr_ob_kon';
}

$whereClause = '';
if (!empty($whereConditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
}

try {
    // Przygotowanie zapytania SQL
    $sql = "
        WITH osoby_kontrolujace_agg AS (
            SELECT okk.id_kon, STRING_AGG(ok.imie || ' ' || ok.nazwisko, ',') AS osoby_kont
            FROM osoby_kontrolujace ok
            INNER JOIN osoby_kontr_kontrole okk ON ok.id_oso_kont = okk.id_oso_kont
            GROUP BY okk.id_kon
        ),
        okazane_dokumenty_agg AS (
            SELECT od.id_kon, STRING_AGG(od.naz_dok, ',') AS dokumenty
            FROM okazane_dokumenty od
            GROUP BY od.id_kon
        )
        SELECT k.id_kon, rk.nazwa_kontroli, k.data_kon, k.data_kon || '/' || k.nr_kon AS numer_kontroli,
               o.imie || ' ' || o.nazwisko AS wlasciciel,
               CASE WHEN a.ulica IS NULL OR TRIM(a.ulica) = ''
                    THEN a.kod_pocztowy || ' ' || COALESCE(a.miejscowosc, '') || ' ' || a.numer
                    ELSE a.kod_pocztowy || ' ' || COALESCE(a.miejscowosc, '') || ' Ul.' || a.ulica || ' ' || a.numer
               END AS adres_wlasciciel,
               CASE WHEN a2.ulica IS NULL OR TRIM(a2.ulica) = ''
                    THEN a2.kod_pocztowy || ' ' || COALESCE(a2.miejscowosc, '') || ' ' || a2.numer
                    ELSE a2.kod_pocztowy || ' ' || COALESCE(a2.miejscowosc, '') || ' Ul.' || a2.ulica || ' ' || a2.numer
               END AS adres_obiektu,
               oka.osoby_kont,
               oda.dokumenty,
               k.zalecenia_pokontrolne, k.uwagi
        FROM kontrole k
        INNER JOIN rodzaj_kontroli rk ON k.id_rodz_kon = rk.id
        INNER JOIN osoba o ON o.id_osoby = k.wlasciciel
        INNER JOIN osoba_adres oa ON oa.id_osoby = o.id_osoby
        INNER JOIN adresy a ON a.ogc_fid = oa.id_adr_zamel
        INNER JOIN adresy a2 ON a2.ogc_fid = k.adres_nier_id
        LEFT JOIN osoby_kontrolujace_agg oka ON oka.id_kon = k.id_kon
        LEFT JOIN okazane_dokumenty_agg oda ON oda.id_kon = k.id_kon
        $whereClause
    ";
    $zapytanie = $baza_danych->prepare($sql);

    // Bindowanie parametrów
    if (!empty($rodzajKontroli)) {
        $rodzajKontroli = '%' . $rodzajKontroli . '%';
        $zapytanie->bindParam(':rodzajKontroli', $rodzajKontroli, PDO::PARAM_STR);
    }
    if (!empty($id_wlascicel_szamba)) {
        $zapytanie->bindParam(':id_wlascicel_szamba', $id_wlascicel_szamba, PDO::PARAM_INT);
    }
    if (!empty($id_adr_ob_kon)) {
        $zapytanie->bindParam(':id_adr_ob_kon', $id_adr_ob_kon, PDO::PARAM_INT);
    }

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