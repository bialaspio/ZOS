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
$miejscowosc = $_POST["miejscowosc"] ?? null;
$id_firmy = $_POST["id_firmy"] ?? null;
$start = $_POST["start"] ?? null;
$koniec = $_POST["koniec"] ?? null;
$rodzajNieczyst = $_POST["rodzajNieczyst"] ?? null;
$RelGmin = $_POST["RelGmin"] ?? null;

if (!$start || !$koniec) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganych danych wejściowych
    echo json_encode(['error' => 'Brak wymaganych parametrów: start, koniec']);
    exit;
}

// Zamiana wartości dla RelGmin
$SemRelGmin = -1;
if ($RelGmin === 'TAK') {
    $SemRelGmin = 1;
} elseif ($RelGmin === 'NIE') {
    $SemRelGmin = 0;
}

// Przygotowanie zapytania SQL
$sql = "
    SELECT 
        pw.data_wywozu, pw.ilosc_sciekow, s.rodzaj_nieczystosci, pw.realizacja_gmina, f.nazwa,
        COALESCE(
            'Ul.' || a.ulica || ' ' || a.numer || ' ' || a.kod_pocztowy || ' ' || a.miejscowosc,
            a.miejscowosc || ' ' || a.numer || ' ' || a.kod_pocztowy || ' ' || a.miejscowosc
        ) AS Adres
    FROM protokol_wywozu pw
    LEFT JOIN firmy f ON pw.firma_id = f.id
    LEFT JOIN adres_szamba as2 ON as2.id_szambo = pw.id_szamba
    LEFT JOIN adresy a ON a.ogc_fid = as2.id_adres
    LEFT JOIN szamba s ON s.id_szamba = pw.id_szamba
    WHERE pw.data_wywozu BETWEEN :start AND :koniec
";

// Dodatkowe warunki WHERE
if ($miejscowosc !== 'Wszystkie') {
    $sql .= " AND a.miejscowosc = :miejscowosc";
}
if ($rodzajNieczyst !== 'Wszystkie') {
    $sql .= " AND s.rodzaj_nieczystosci = :rodzajNieczyst";
}
if ($id_firmy !== '-1') {
    $sql .= " AND f.id = :id_firmy";
}
if ($SemRelGmin > -1) {
    $sql .= " AND pw.realizacja_gmina = :RelGmin";
}

try {
    // Przygotowanie zapytania
    $zapytanie = $baza_danych->prepare($sql);

    // Bindowanie parametrów
    $zapytanie->bindParam(':start', $start);
    $zapytanie->bindParam(':koniec', $koniec);
    if ($miejscowosc !== 'Wszystkie') {
        $zapytanie->bindParam(':miejscowosc', $miejscowosc);
    }
    if ($rodzajNieczyst !== 'Wszystkie') {
        $zapytanie->bindParam(':rodzajNieczyst', $rodzajNieczyst);
    }
    if ($id_firmy !== '-1') {
        $zapytanie->bindParam(':id_firmy', $id_firmy);
    }
    if ($SemRelGmin > -1) {
        $RelGminTF = $SemRelGmin === 1;
        $zapytanie->bindParam(':RelGmin', $RelGminTF, PDO::PARAM_BOOL);
    }

    // Wykonanie zapytania
    $zapytanie->execute();

    // Pobranie danych
    $przewidywane_dane_firmy = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($przewidywane_dane_firmy ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>