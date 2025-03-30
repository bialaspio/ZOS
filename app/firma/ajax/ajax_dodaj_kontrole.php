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

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobieranie danych wejściowych
    $id_rodz_kont = $_POST['id_rodz_kont'] ?? null;
    $data_kon_d = $_POST['data_kon_d'] ?? null;
    $nr_kon = $_POST['nr_kon'] ?? null;
    $id_osoby_wlasciciel = $_POST['id_osoby_wlasciciel'] ?? null;
    $zalecenia_pokontr = $_POST['zalecenia_pokontr'] ?? null;
    $uwagi = $_POST['uwagi'] ?? null;
    $id_adresu_nier = $_POST['id_adresu_nier'] ?? null;
    $osoby_kontrolujace = $_POST['osoby_kontrolujace'] ?? [];
    $osoby_b_u_w_k = $_POST['osoby_b_u_w_k'] ?? [];
    $data_json = $_POST['json'] ?? null;

    // Walidacja danych wejściowych
    if (!$id_rodz_kont || !$data_kon_d || !$nr_kon || !$id_osoby_wlasciciel || !$id_adresu_nier || !$data_json) {
        http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganych danych
        echo json_encode(['error' => 'Brak wymaganych danych wejściowych']);
        exit;
    }

    // Konwersja danych JSON na tablicę
    $data_json_array = json_decode($data_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400); // Zwróć kod błędu 400 w przypadku nieprawidłowego JSON-a
        echo json_encode(['error' => 'Nieprawidłowy format danych JSON']);
        exit;
    }

    // Konwersja tablic na format PostgreSQL ARRAY
    $osoby_kontrolujace_pg = '{' . implode(',', array_map(fn($item) => '"' . addslashes($item) . '"', $osoby_kontrolujace)) . '}';
    $osoby_b_u_w_k_pg = '{' . implode(',', array_map(fn($item) => '"' . addslashes($item) . '"', $osoby_b_u_w_k)) . '}';
    $data_json_pg = '{' . implode(',', array_map(fn($item) => '"' . addslashes(json_encode($item)) . '"', $data_json_array)) . '}';

    try {
        // Przygotowanie zapytania SQL
        $sql_query = "
            SELECT public.dodaj_kontrole_v02(
                :id_rodz_kont,
                :data_kon_d,
                :nr_kon,
                :id_osoby_wlasciciel,
                :id_adresu_nier,
                :zalecenia_pokontr,
                :uwagi,
                :osoby_kontrolujace,
                :osoby_b_u_w_k,
                :data_json_pg::json[]
            )
        ";
        $zapytanie = $baza_danych->prepare($sql_query);

        // Bindowanie parametrów
        $zapytanie->bindParam(':id_rodz_kont', $id_rodz_kont, PDO::PARAM_INT);
        $zapytanie->bindParam(':data_kon_d', $data_kon_d, PDO::PARAM_STR);
        $zapytanie->bindParam(':nr_kon', $nr_kon, PDO::PARAM_STR);
        $zapytanie->bindParam(':id_osoby_wlasciciel', $id_osoby_wlasciciel, PDO::PARAM_INT);
        $zapytanie->bindParam(':id_adresu_nier', $id_adresu_nier, PDO::PARAM_INT);
        $zapytanie->bindParam(':zalecenia_pokontr', $zalecenia_pokontr, PDO::PARAM_STR);
        $zapytanie->bindParam(':uwagi', $uwagi, PDO::PARAM_STR);
        $zapytanie->bindParam(':osoby_kontrolujace', $osoby_kontrolujace_pg, PDO::PARAM_STR);
        $zapytanie->bindParam(':osoby_b_u_w_k', $osoby_b_u_w_k_pg, PDO::PARAM_STR);
        $zapytanie->bindParam(':data_json_pg', $data_json_pg, PDO::PARAM_STR);

        // Wykonanie zapytania
        $zapytanie->execute();

        // Sprawdzenie wyniku
        $result = $zapytanie->fetchColumn();
        echo json_encode(['success' => $result ? true : false]);
    } catch (PDOException $e) {
        http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
        echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
    } finally {
        // Zwolnienie zasobów
        $zapytanie = null;
        $baza_danych = null;
    }
} else {
    http_response_code(405); // Zwróć kod błędu 405 dla metod innych niż POST
    echo json_encode(['error' => 'Metoda niedozwolona']);
}
?>