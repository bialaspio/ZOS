<?php
require '../menu_str.php';

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
    $config = loadDatabaseConfig('../connect_par.conf');

    // Połączenie z bazą danych
    $conn = pg_connect("host={$config['host']} port=5432 dbname={$config['dbname']} user={$config['user']} password={$config['password']}");
    if (!$conn) {
        throw new Exception("Nie udało się połączyć z bazą danych.");
    }
} catch (Exception $e) {
    echo "<h4 style='color: red;'>Błąd połączenia z bazą danych: " . htmlspecialchars($e->getMessage()) . "</h4>";
    exit;
}

// Pobranie danych budynku
$id_budynku = $_COOKIE['id_budynku'] ?? null;
if (!$id_budynku) {
    echo "<h4 style='color: red;'>Brak identyfikatora budynku w ciasteczkach.</h4>";
    exit;
}

$query = "
    SELECT 
        be.id_budynku, 
        be.rodzaj_opis,
        CASE 
            WHEN a.ulica IS NULL OR a.ulica = '' THEN a.miejscowosc 
            ELSE a.ulica 
        END AS ulica, 
        a.numer, 
        a.kod_pocztowy, 
        a.miejscowosc
    FROM budynki_egib be 
    LEFT JOIN adr_bud ab ON be.ogc_fid = ab.adr_bud 
    LEFT JOIN adresy a ON a.ogc_fid = ab.id_adr 
    WHERE be.id_budynku = $1;
";

$result = pg_query_params($conn, $query, [$id_budynku]);
if (!$result) {
    echo "<h4 style='color: red;'>Błąd zapytania do bazy danych.</h4>";
    exit;
}

$row = pg_fetch_assoc($result);
if ($row) {
    $id_bud = $row['id_budynku'];
    $rodz_bud = $row['rodzaj_opis'];
    $ulica = $row['ulica'];
    $numer = $row['numer'];
    $miejscowosc = $row['miejscowosc'];
    $kod_pocztowy = $row['kod_pocztowy'];

    $dane_adres = trim("$ulica $numer $miejscowosc $kod_pocztowy");
    if (empty($dane_adres)) {
        $dane_adres = "Budynek nie posiada punktu adresowego.";
    }
} else {
    $dane_adres = "Budynek nie posiada punktu adresowego.";
    $rodz_bud = "Nieznany";
}

pg_free_result($result);
pg_close($conn);
?>

<div style="height:20px"></div>

<div style="margin: 20px 70px 10px 70px;"> 
    <nav style="border: 1px solid rgba(0,0,0,0.2); height:90px; align-items: center; justify-content: center; display:inline-block;">
        <h4 style="font-size: 22px; color:#154c79; text-align: center;">Adres nieruchomości:</h4>
        <div style="height:2px"></div>
        <h4 id="h4DaneAdres" class="h4DaneAdres"><?php echo htmlspecialchars($dane_adres); ?></h4>
        <h4 id="h4RodzajBudynku" class="h4RodzajBudynku">Rodzaj budynku: <?php echo htmlspecialchars($rodz_bud); ?></h4>
    </nav>
</div>

<div id="dane_char_nier">
    <div style="margin: 20px 70px 10px 70px;"> 	
        <nav style="border: 1px solid rgba(0,0,0,0.2); height:40px; align-items: center; justify-content: center;">
            <h4 style="font-size: 22px; color:#154c79; text-align: center;">Lista osób zameldowanych:</h4>
        </nav>
    </div>

    <div style="height:20px"></div>
    <div>
        <nav style="border: 1px solid rgba(0,0,0,0.2); height:30px; width:600px; align-items: center; margin-left:auto; margin-right:auto">
            <div style="background-color: dodgerblue; height:100%">
                <button type="button" class="button" id="download-csv-lista_osob_zameldowanych" style="width:118px">Download CSV</button>
            </div>
            <div style="background-color: dodgerblue; height:100%">
                <button type="button" class="button" id="download-json-lista_osob_zameldowanych" style="width:118px">Download JSON</button>
            </div>
            <div style="background-color: dodgerblue; height:100%">
                <button type="button" class="button" id="download-xlsx-lista_osob_zameldowanych" style="width:118px">Download XLSX</button>
            </div>
            <div style="background-color: dodgerblue; height:100%">
                <button type="button" class="button" id="download-pdf-lista_osob_zameldowanych" style="width:118px">Download PDF</button>
            </div>
            <div style="background-color: dodgerblue; height:100%">
                <button type="button" class="button" id="download-html-lista_osob_zameldowanych" style="width:118px">Download HTML</button>
            </div>
        </nav>
    </div>

    <div id="lista_osob_zameldowanych-table" style="margin: 20px 70px 10px 70px;"></div>
</div>

<script>
    const h4DaneAdresDane = document.getElementById("h4DaneAdres").innerText;
    const daneCharNier = document.getElementById("dane_char_nier");
    if (h4DaneAdresDane === 'Budynek nie posiada punktu adresowego.') {
        daneCharNier.style.display = "none";
    } else {
        daneCharNier.style.display = "block";
    }
</script>

<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>
<script type="text/javascript" src="charakt_nieruchomosci.js"></script>
</body>
</html>