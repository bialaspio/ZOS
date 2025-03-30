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
    $conn = pg_connect("host={$config['host']} port=5432 dbname={$config['dbname']} user={$config['user']} password={$config['password']}");
    if (!$conn) {
        throw new Exception("Nie udało się połączyć z bazą danych.");
    }
} catch (Exception $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z połączeniem
    echo json_encode(['error' => 'Błąd połączenia z bazą danych: ' . $e->getMessage()]);
    exit;
}

// Pobranie danych wejściowych
$idSzamba = $_POST['idSzamba'] ?? null;

if (!$idSzamba) {
    http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganego parametru
    echo json_encode(['error' => 'Brak wymaganego parametru: idSzamba']);
    exit;
}

try {
    // Pobierz dane o ostatnim wywozie
    $query = "
        SELECT 
            (NOW()::timestamp::date - MAX(pw.data_wywozu)) AS dni_od_opr,
            MAX(pw.data_wywozu) AS data_ost_wyw
        FROM protokol_wywozu pw
        WHERE pw.id_szamba = $1
    ";
    $result = pg_query_params($conn, $query, [$idSzamba]);
    if (!$result) {
        throw new Exception("Błąd wykonania zapytania: " . pg_last_error($conn));
    }
    $row = pg_fetch_assoc($result);
    $dni_od_wyw = $row['dni_od_opr'];
    $data_ost_wyw = $row['data_ost_wyw'];
    pg_free_result($result);

    // Pobierz średnią liczbę dni między wywozami
    $query = "
        SELECT 
            SUM(il_dni) / COUNT(il_dni) AS ilos_dni
        FROM (
            SELECT 
                (data_wywozu - LAG(data_wywozu) OVER (ORDER BY data_wywozu))::int AS il_dni
            FROM protokol_wywozu pw
            WHERE pw.id_szamba = $1
        ) AS foo
    ";
    $result = pg_query_params($conn, $query, [$idSzamba]);
    if (!$result) {
        throw new Exception("Błąd wykonania zapytania: " . pg_last_error($conn));
    }
    $row = pg_fetch_assoc($result);
    $ilos_dni = $row['ilos_dni'];
    pg_free_result($result);

    // Zamknij połączenie z bazą danych
    pg_close($conn);

    // Wyślij dane JSON
    echo json_encode([
        'dni_od_wyw' => $dni_od_wyw,
        'data_ost_wyw' => $data_ost_wyw,
        'ilos_dni' => $ilos_dni
    ]);
} catch (Exception $e) {
    http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
    echo json_encode(['error' => 'Błąd przetwarzania danych: ' . $e->getMessage()]);
    pg_close($conn);
    exit;
}
?>