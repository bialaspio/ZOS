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
    $data = $_POST['data'] ?? null;
    $nr_prot = $_POST['nr_prot'] ?? null;
    $id_firmy = $_POST['id_firma'] ?? null;
    $id_szamba = $_POST['id_szamba'] ?? null;
    $platnosc = $_POST['platnosc'] ?? null;
    $poj_szamba = $_POST['PojSzamba'] ?? null;
    $data_wyw = $_POST['data_wyw'] ?? null;
    $id_oczyszcz = $_POST['id_ocz'] ?? null;
    $realizacja_gmina = $_POST['realizacja_gmina'] ?? null;

    // Walidacja danych wejściowych
    if (!$data || !$nr_prot || !$id_firmy || !$id_szamba || !$platnosc || !$poj_szamba || !$data_wyw || !$id_oczyszcz || !$realizacja_gmina) {
        http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganych danych
        echo json_encode(['error' => 'Brak wymaganych danych wejściowych']);
        exit;
    }

    try {
        // Przygotowanie zapytania SQL
        $sql = "
            INSERT INTO public.protokol_wywozu(
                nr_prot_data, 
                nr_prot_nr, 
                firma_id, 
                id_szamba, 
                platnosc, 
                ilosc_sciekow, 
                data_wywozu, 
                id_oczyszcz, 
                realizacja_gmina
            ) VALUES (
                :data, 
                :nr_prot, 
                :id_firmy, 
                :id_szamba, 
                :platnosc, 
                :poj_szamba, 
                :data_wyw, 
                :id_oczyszcz, 
                :realizacja_gmina
            );
        ";
        $zapytanie = $baza_danych->prepare($sql);

        // Bindowanie parametrów
        $zapytanie->bindParam(':data', $data, PDO::PARAM_STR);
        $zapytanie->bindParam(':nr_prot', $nr_prot, PDO::PARAM_STR);
        $zapytanie->bindParam(':id_firmy', $id_firmy, PDO::PARAM_INT);
        $zapytanie->bindParam(':id_szamba', $id_szamba, PDO::PARAM_INT);
        $zapytanie->bindParam(':platnosc', $platnosc, PDO::PARAM_STR);
        $zapytanie->bindParam(':poj_szamba', $poj_szamba, PDO::PARAM_STR);
        $zapytanie->bindParam(':data_wyw', $data_wyw, PDO::PARAM_STR);
        $zapytanie->bindParam(':id_oczyszcz', $id_oczyszcz, PDO::PARAM_INT);
        $zapytanie->bindParam(':realizacja_gmina', $realizacja_gmina, PDO::PARAM_BOOL);

        // Wykonanie zapytania
        $zapytanie->execute();

        // Sprawdzenie wyniku
        if ($zapytanie->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } catch (PDOException $e) {
        http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
        echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405); // Zwróć kod błędu 405 dla metod innych niż POST
    echo json_encode(['error' => 'Metoda niedozwolona']);
}
?>