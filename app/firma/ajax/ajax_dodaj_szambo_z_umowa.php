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
    // Pobieranie danych wejściowych
    $Imie = $_POST['Imie'] ?? null;
    $Nazwisko = $_POST['Nazwisko'] ?? null;

    $KodAdrOsoba = $_POST['KodAdrOsoba'] ?? null;
    $MiejscowoscAdrOsoba = $_POST['MiejscowoscAdrOsoba'] ?? null;
    $UlicaAdrOsoba = $_POST['UlicaAdrOsoba'] ?? null;
    $NrAdrOsoba = $_POST['NrAdrOsoba'] ?? null;

    $KodAdrSzamba = $_POST['KodAdrSzamba'] ?? null;
    $MiejscowoscAdrSzamba = $_POST['MiejscowoscAdrSzamba'] ?? null;
    $UlicaAdrSzamba = $_POST['UlicaAdrSzamba'] ?? null;
    $NrAdrSzamba = $_POST['NrAdrSzamba'] ?? null;

    $Pojemnosc = $_POST['Pojemnosc'] ?? null;
    $Rodzaj = $_POST['Rodzaj'] ?? null;

    $x = $_POST['x'] ?? null;
    $y = $_POST['y'] ?? null;

    // Walidacja danych wejściowych
    if (!$Imie || !$Nazwisko || !$KodAdrOsoba || !$MiejscowoscAdrOsoba || !$UlicaAdrOsoba || !$NrAdrOsoba ||
        !$KodAdrSzamba || !$MiejscowoscAdrSzamba || !$UlicaAdrSzamba || !$NrAdrSzamba || !$Pojemnosc || !$Rodzaj || !$x || !$y) {
        http_response_code(400); // Zwróć kod błędu 400 w przypadku braku wymaganych danych
        echo json_encode(['error' => 'Brak wymaganych danych wejściowych']);
        exit;
    }

    try {
        // Przygotowanie zapytania SQL
        $sql = "
            SELECT dodaj_informacje_o_szambie_v04(
                :Imie, :Nazwisko, :MiejscowoscAdrOsoba, :UlicaAdrOsoba, :NrAdrOsoba, :KodAdrOsoba,
                :MiejscowoscAdrSzamba, :UlicaAdrSzamba, :NrAdrSzamba, :KodAdrSzamba,
                :Pojemnosc, :Rodzaj, :x, :y
            );
        ";
        $stmt = $db->prepare($sql);

        // Bindowanie parametrów
        $stmt->bindParam(':Imie', $Imie, PDO::PARAM_STR);
        $stmt->bindParam(':Nazwisko', $Nazwisko, PDO::PARAM_STR);
        $stmt->bindParam(':KodAdrOsoba', $KodAdrOsoba, PDO::PARAM_STR);
        $stmt->bindParam(':MiejscowoscAdrOsoba', $MiejscowoscAdrOsoba, PDO::PARAM_STR);
        $stmt->bindParam(':UlicaAdrOsoba', $UlicaAdrOsoba, PDO::PARAM_STR);
        $stmt->bindParam(':NrAdrOsoba', $NrAdrOsoba, PDO::PARAM_STR);
        $stmt->bindParam(':KodAdrSzamba', $KodAdrSzamba, PDO::PARAM_STR);
        $stmt->bindParam(':MiejscowoscAdrSzamba', $MiejscowoscAdrSzamba, PDO::PARAM_STR);
        $stmt->bindParam(':UlicaAdrSzamba', $UlicaAdrSzamba, PDO::PARAM_STR);
        $stmt->bindParam(':NrAdrSzamba', $NrAdrSzamba, PDO::PARAM_STR);
        $stmt->bindParam(':Pojemnosc', $Pojemnosc, PDO::PARAM_INT);
        $stmt->bindParam(':Rodzaj', $Rodzaj, PDO::PARAM_STR);
        $stmt->bindParam(':x', $x, PDO::PARAM_STR);
        $stmt->bindParam(':y', $y, PDO::PARAM_STR);

        // Wykonanie zapytania
        $stmt->execute();
        $rezultat = $stmt->fetch(PDO::FETCH_ASSOC);

        // Zwrócenie wyniku
        if ($rezultat) {
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