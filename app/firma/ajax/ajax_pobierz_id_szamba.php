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
$KodAdrWlSzamba = $_POST["KodAdrWlSzamba"] ?? null;
$MiejscowoscAdrWlSzamba = $_POST["MiejscowoscAdrWlSzamba"] ?? null;
$UlicaAdrWlSzamba = $_POST["UlicaAdrWlSzamba"] ?? null;
$NrAdrWlSzamba = $_POST["NrAdrWlSzamba"] ?? null;

if (!$KodAdrWlSzamba || !$MiejscowoscAdrWlSzamba || !$NrAdrWlSzamba) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganych danych wejściowych
    echo json_encode(['error' => 'Brak wymaganych parametrów: KodAdrWlSzamba, MiejscowoscAdrWlSzamba, NrAdrWlSzamba']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    if (empty($UlicaAdrWlSzamba) || $UlicaAdrWlSzamba === $MiejscowoscAdrWlSzamba) {
        $sql = "
            SELECT DISTINCT sz.id_szamba 
            FROM szamba sz 
            INNER JOIN adres_szamba asz ON asz.id_szambo = sz.id_szamba 
            INNER JOIN adresy a ON a.ogc_fid = asz.id_adres 
            WHERE a.numer LIKE :nr_bud 
              AND a.kod_pocztowy LIKE :kodpocz 
              AND a.miejscowosc LIKE :miejs
        ";
        $zapytanie = $baza_danych->prepare($sql);
    } else {
        $sql = "
            SELECT DISTINCT sz.id_szamba 
            FROM szamba sz 
            INNER JOIN adres_szamba asz ON asz.id_szambo = sz.id_szamba 
            INNER JOIN adresy a ON a.ogc_fid = asz.id_adres 
            WHERE a.ulica LIKE :ulica 
              AND a.numer LIKE :nr_bud 
              AND a.kod_pocztowy LIKE :kodpocz 
              AND a.miejscowosc LIKE :miejs
        ";
        $zapytanie = $baza_danych->prepare($sql);
        $zapytanie->bindParam(':ulica', $UlicaAdrWlSzamba, PDO::PARAM_STR);
    }

    // Bindowanie parametrów
    $zapytanie->bindParam(':nr_bud', $NrAdrWlSzamba, PDO::PARAM_STR);
    $zapytanie->bindParam(':kodpocz', $KodAdrWlSzamba, PDO::PARAM_STR);
    $zapytanie->bindParam(':miejs', $MiejscowoscAdrWlSzamba, PDO::PARAM_STR);

    // Wykonanie zapytania
    $zapytanie->execute();
    $przewidywane_dane_szamba = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    // Zwrócenie wyników w formacie JSON
    echo json_encode($przewidywane_dane_szamba ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>