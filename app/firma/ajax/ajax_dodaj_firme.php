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
    $db = new PDO(
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
    // Pobranie i walidacja danych wejściowych
    $naz_firmy = $_POST['naz_firmy'] ?? null;
    $nip = $_POST['nip'] ?? null;
    $ulica = $_POST['ulica'] ?? null;
    $nr = $_POST['nr'] ?? null;
    $kod = $_POST['kod'] ?? null;
    $miasto = $_POST['miasto'] ?? null;
    $chb_inny_adr_kor = isset($_POST['chb_inny_adr_kor']) ? 'true' : 'false';
    $inny_adr_kor = $_POST['inny_adr_kor'] ?? null;
    $dod_info = $_POST['dod_info'] ?? null;
    $email = $_POST['email'] ?? null;
    $nr_tel = $_POST['nr_tel'] ?? null;

    // Walidacja wymaganych pól
    if (!$naz_firmy || !$nip || !$ulica || !$nr || !$kod || !$miasto) {
        http_response_code(400); // Zwróć kod błędu 400 w przypadku braku danych
        echo json_encode(['error' => 'Brak wymaganych danych wejściowych']);
        exit;
    }

    try {
        // Przygotowanie zapytania SQL
        $sql = "
            SELECT dodaj_firme2(
                :naz_firmy, 
                :nip, 
                :ulica, 
                :nr, 
                :kod, 
                :miasto, 
                :chb_inny_adr_kor, 
                :inny_adr_kor, 
                :dod_info, 
                :email, 
                :nr_tel
            );
        ";
        $stmt = $db->prepare($sql);

        // Bindowanie parametrów
        $stmt->bindParam(':naz_firmy', $naz_firmy, PDO::PARAM_STR);
        $stmt->bindParam(':nip', $nip, PDO::PARAM_STR);
        $stmt->bindParam(':ulica', $ulica, PDO::PARAM_STR);
        $stmt->bindParam(':nr', $nr, PDO::PARAM_STR);
        $stmt->bindParam(':kod', $kod, PDO::PARAM_STR);
        $stmt->bindParam(':miasto', $miasto, PDO::PARAM_STR);
        $stmt->bindParam(':chb_inny_adr_kor', $chb_inny_adr_kor, PDO::PARAM_BOOL);
        $stmt->bindParam(':inny_adr_kor', $inny_adr_kor, PDO::PARAM_STR);
        $stmt->bindParam(':dod_info', $dod_info, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':nr_tel', $nr_tel, PDO::PARAM_STR);

        // Wykonanie zapytania
        $stmt->execute();
        $rezultat = $stmt->fetch();

        // Zwrócenie wyniku
        echo json_encode(['success' => $rezultat ? true : false]);
    } catch (PDOException $e) {
        http_response_code(500); // Zwróć kod błędu 500 w przypadku problemów z zapytaniem
        echo json_encode(['error' => 'Błąd wykonywania zapytania: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405); // Zwróć kod błędu 405 dla metod innych niż POST
    echo json_encode(['error' => 'Metoda niedozwolona']);
}
?>