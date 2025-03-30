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
$Imie = $_POST["Imie"] ?? null;
$Nazwisko = $_POST["Nazwisko"] ?? null;
$KodAdrOsoba = $_POST["KodAdrOsoba"] ?? null;
$MiejscowoscAdrOsoba = $_POST["MiejscowoscAdrOsoba"] ?? null;
$UlicaAdrOsoba = $_POST["UlicaAdrOsoba"] ?? null;
$NrAdrOsoba = $_POST["NrAdrOsoba"] ?? null;

if (!$Imie || !$Nazwisko || !$KodAdrOsoba || !$MiejscowoscAdrOsoba || !$NrAdrOsoba) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganych danych wejściowych
    echo json_encode(['error' => 'Brak wymaganych parametrów: Imie, Nazwisko, KodAdrOsoba, MiejscowoscAdrOsoba, NrAdrOsoba']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    if ($UlicaAdrOsoba === $MiejscowoscAdrOsoba) {
        $sql = "
            SELECT o.id_osoby, wlasciciel
            FROM osoba_adres oa
            INNER JOIN osoba o ON oa.id_osoby = o.id_osoby
            INNER JOIN adresy a ON a.ogc_fid = oa.id_adr_zamel OR a.ogc_fid = oa.id_adr_zamiesz
            WHERE o.imie LIKE :imie
              AND o.nazwisko LIKE :nazwisko
              AND a.numer LIKE :nr_bud
              AND a.kod_pocztowy LIKE :kodpocz
              AND a.miejscowosc LIKE :miejs
        ";
        $zapytanie = $baza_danych->prepare($sql);
    } else {
        $sql = "
            SELECT o.id_osoby, wlasciciel
            FROM osoba_adres oa
            INNER JOIN osoba o ON oa.id_osoby = o.id_osoby
            INNER JOIN adresy a ON a.ogc_fid = oa.id_adr_zamel OR a.ogc_fid = oa.id_adr_zamiesz
            WHERE o.imie LIKE :imie
              AND o.nazwisko LIKE :nazwisko
              AND a.ulica LIKE :ulica
              AND a.numer LIKE :nr_bud
              AND a.kod_pocztowy LIKE :kodpocz
              AND a.miejscowosc LIKE :miejs
        ";
        $zapytanie = $baza_danych->prepare($sql);
        $zapytanie->bindParam(':ulica', $UlicaAdrOsoba, PDO::PARAM_STR);
    }

    // Bindowanie parametrów
    $zapytanie->bindParam(':imie', $Imie, PDO::PARAM_STR);
    $zapytanie->bindParam(':nazwisko', $Nazwisko, PDO::PARAM_STR);
    $zapytanie->bindParam(':nr_bud', $NrAdrOsoba, PDO::PARAM_STR);
    $zapytanie->bindParam(':kodpocz', $KodAdrOsoba, PDO::PARAM_STR);
    $zapytanie->bindParam(':miejs', $MiejscowoscAdrOsoba, PDO::PARAM_STR);

    // Wykonanie zapytania
    $zapytanie->execute();
    $przewidywane_dane_osoba = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($przewidywane_dane_osoba ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>