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
$idKon = $_POST["idKon"] ?? null;
$nazwaDok = $_POST["nazwaDok"] ?? null;
$nazwaZalacznik = $_POST["nazwaZalacznik"] ?? null;

if (!$idKon || !$nazwaDok || !$nazwaZalacznik) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganych danych wejściowych
    echo json_encode(['error' => 'Brak wymaganych parametrów: idKon, nazwaDok, nazwaZalacznik']);
    exit;
}

try {
    // Przygotowanie zapytania SQL
    $sql = "
        SELECT id, id_kon, naz_dok, naz_zalacznik, zalacznik
        FROM okazane_dokumenty
        WHERE id_kon = :idKon
          AND naz_dok LIKE :nazwaDok
          AND naz_zalacznik LIKE :nazwaZalacznik
    ";
    $zapytanie = $baza_danych->prepare($sql);

    // Bindowanie parametrów
    $zapytanie->bindParam(':idKon', $idKon, PDO::PARAM_INT);
    $zapytanie->bindParam(':nazwaDok', $nazwaDok, PDO::PARAM_STR);
    $zapytanie->bindParam(':nazwaZalacznik', $nazwaZalacznik, PDO::PARAM_STR);

    // Wykonanie zapytania
    $zapytanie->execute();

    $data = [];
    while ($row = $zapytanie->fetch(PDO::FETCH_ASSOC)) {
        // Pobierz rzeczywiste dane z zasobu bytea
        $zalacznikData = stream_get_contents($row['zalacznik']);

        // Zakoduj dane do formatu Base64
        $zalacznikBase64 = base64_encode($zalacznikData);

        $data[] = [
            "id" => $row["id"],
            "id_kon" => $row["id_kon"],
            "naz_dok" => $row["naz_dok"],
            "naz_zalacznik" => $row["naz_zalacznik"],
            "zalacznik" => $zalacznikBase64
        ];
    }

    // Zwrócenie wyników w formacie JSON
    echo json_encode($data ?: []);
} catch (PDOException $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
}
?>