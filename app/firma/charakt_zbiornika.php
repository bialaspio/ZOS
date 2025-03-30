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

// Pobranie danych właściciela szamba
$id_szamba = $_COOKIE['id_szamba'] ?? null;
if (!$id_szamba) {
    echo "<h4 style='color: red;'>Brak identyfikatora szamba w ciasteczkach.</h4>";
    exit;
}

$query_wlasciciel = "
    SELECT o.imie, o.nazwisko, a.ulica, a.numer, a.kod_pocztowy, a.miejscowosc
    FROM szamba s
    INNER JOIN osoba_szambo os ON os.id_szambo = s.id_szamba
    INNER JOIN osoba o ON o.id_osoby = os.id_osoba
    INNER JOIN osoba_adres oa ON oa.id_osoby = o.id_osoby
    INNER JOIN adresy a ON a.ogc_fid = oa.id_adr_zamel
    WHERE s.id_szamba = $1 AND o.wlasciciel = true;
";
$result = pg_query_params($conn, $query_wlasciciel, [$id_szamba]);
$dane_wlasciciela = pg_fetch_all($result) ?: [];
pg_free_result($result);

$query_szambo = "
    SELECT a.ulica, a.numer, a.miejscowosc, a.kod_pocztowy, s.pojemnosc_m3, s.rodzaj_nieczystosci
    FROM szamba s
    INNER JOIN adres_szamba as2 ON as2.id_szambo = s.id_szamba
    INNER JOIN adresy a ON a.ogc_fid = as2.id_adres
    WHERE s.id_szamba = $1;
";
$result = pg_query_params($conn, $query_szambo, [$id_szamba]);
$dane_szambo = pg_fetch_assoc($result) ?: [];
pg_free_result($result);

$query_historia = "
    SELECT (NOW()::timestamp::date - MAX(pw.data_wywozu)) AS dni_od_opr, MAX(pw.data_wywozu) AS data_ost_wyw
    FROM protokol_wywozu pw
    WHERE pw.id_szamba = $1;
";
$result = pg_query_params($conn, $query_historia, [$id_szamba]);
$dane_historia = pg_fetch_assoc($result) ?: [];
pg_free_result($result);

$query_srednia = "
    SELECT SUM(il_dni) / COUNT(il_dni) AS ilos_dni
    FROM (
        SELECT (data_wywozu - LAG(data_wywozu) OVER (ORDER BY data_wywozu))::int AS il_dni
        FROM protokol_wywozu pw
        WHERE pw.id_szamba = $1
    ) AS foo;
";
$result = pg_query_params($conn, $query_srednia, [$id_szamba]);
$dane_srednia = pg_fetch_assoc($result) ?: [];
pg_free_result($result);

pg_close($conn);

// Przygotowanie danych do wyświetlenia
$dane_wlasciciela_html = "";
foreach ($dane_wlasciciela as $wlasciciel) {
    $dane_wlasciciela_html .= "{$wlasciciel['imie']} {$wlasciciel['nazwisko']}; {$wlasciciel['ulica']} {$wlasciciel['numer']} {$wlasciciel['kod_pocztowy']} {$wlasciciel['miejscowosc']}<br>";
}

$dane_szambo_html = "{$dane_szambo['kod_pocztowy']} {$dane_szambo['miejscowosc']} {$dane_szambo['ulica']} {$dane_szambo['numer']} - Rodzaj nieczystości: {$dane_szambo['rodzaj_nieczystosci']} Pojemność: {$dane_szambo['pojemnosc_m3']} m3";

$dni_od_wyw = $dane_historia['dni_od_opr'] ?? "Brak danych";
$data_ost_wyw = $dane_historia['data_ost_wyw'] ?? "Brak danych";
$sr_ilos_dni = $dane_srednia['ilos_dni'] ?? "Brak danych";
?>

<div style="margin: 20px 70px 10px 70px;">
    <nav style="border: 1px solid rgba(0,0,0,0.2); height:auto; align-items: center; justify-content: center; display:block;">
        <h4 style="font-size: 22px; color:#154c79; text-align: center;"><?php echo $dane_wlasciciela_html; ?></h4>
        <h4 style="font-size: 22px; color:#154c79; text-align: center;"><?php echo $dane_szambo_html; ?></h4>
    </nav>
</div>

<div>
    <nav style="border: 1px solid rgba(0,0,0,0.2); height:30px; width:600px; align-items: center; margin-left:auto; margin-right:auto">
        <?php
        $formats = ['csv', 'json', 'xlsx', 'pdf', 'html'];
        foreach ($formats as $format) {
            echo "<div style='background-color: dodgerblue; height:100%;'>
                    <button type='button' class='button' id='download-$format' style='width:118px'>Download " . strtoupper($format) . "</button>
                  </div>";
        }
        ?>
    </nav>
</div>

<div id="hist_wyw-table" style="margin-top:10px"></div>

<div style="height:20px"></div>
<div style="width:99%; justify-content: space-around;">
    <nav style="margin:auto; border: 1px solid rgba(0,0,0,0.2); height:40px; width:80%; align-items: center; display: flex;">
        <label style="font-size: 22px; color:#154c79;">Data ostatniego wywozu: <?php echo $data_ost_wyw; ?></label>
        <label style="font-size: 22px; color:<?php echo ($dni_od_wyw > $sr_ilos_dni) ? '#FF0000' : '#154c79'; ?>;">Ilość dni od wywozu: <?php echo $dni_od_wyw; ?></label>
        <label style="font-size: 22px; color:#154c79;">Średnia opróżnień co: <?php echo $sr_ilos_dni; ?> dni</label>
    </nav>
</div>

<div style="margin: 20px 70px 10px 70px;">
    <nav style="border: 1px solid rgba(0,0,0,0.2); height:40px; align-items: center; justify-content: center;">
        <h4 style="font-size: 22px; color:#154c79; text-align: center;">Lista zamieszkałych osób</h4>
    </nav>
</div>

<div>
    <nav style="border: 1px solid rgba(0,0,0,0.2); height:30px; width:600px; align-items: center; margin-left:auto; margin-right:auto">
        <?php
        foreach ($formats as $format) {
            echo "<div style='background-color: dodgerblue; height:100%;'>
                    <button type='button' class='button' id='download-$format-list-osob' style='width:118px'>Download " . strtoupper($format) . "</button>
                  </div>";
        }
        ?>
    </nav>
</div>

<div id="lista_osob-table" style="margin-top:10px"></div>

<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="charakt_zbiornika.js"></script>
</body>
</html>