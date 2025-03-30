<?php
// Rozpocznij sesję
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Jeśli nie jest zalogowany, przekieruj do strony logowania
   // header('Location: http://192.168.0.94/geoserwer');
	header('Location: /GSPG');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Dane opisowe -Test</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link href="https://unpkg.com/tabulator-tables/dist/css/tabulator.min.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
	<link rel="stylesheet" href="mermaid.css">
	<link rel="stylesheet" href="../css/main.css">
	<link rel="stylesheet" href="HistWyw.css">
	<link rel = "icon" href ="TMCE_logo_min.png">
	<script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.20/jspdf.plugin.autotable.min.js"></script>
</head>
<body>

<div id="map" style = "position: relative;">
	<nav class = "naglowek" id="header">
		<a href="https://www.tmce.pl">
			<img src="../css/images/logoTMCE.png" style="height: 46px;margin-top: 3px;">
		</a>
		
        <div id="topbar" style = "margin-top:8px;align-items: center;  justify-content: center; ">
			<div class = "dgcz">
				<div class = "data_godz"> Data: </div>
				<div class = "czas">
				<p id="tabela"></p>
				</div>
			</div>
			
			<div class = "baneropis" >
				<h3  style = "font-size: 26px;	color:#F0F8FF;  text-align: center; padding-top: 8px; padding-bottom: 0px; border-top-width: 1px;border-top-style: solid;margin-right: 2px;border-right-width: 1px;border-right-style: solid;border-left-width: 1px;border-left-style: solid;border-bottom-width: 1px;border-bottom-style: solid;margin-bottom: 2px;margin-left: 2px;">Historia Wywozu </h3>
			</div>
			
			<div class="mod_opis" >
				<a href="../index_mapa.php">
					<button type="button" class="button">STRONA GŁÓWNA</button>
				</a>
			</div>
						
			<div class="logoff" style ="position: relative; text-align: center; font-size: 14px;  width:100px; color:white; padding-top:3px">
				<?php
					echo "Witaj: <BR>" . $_SESSION['username'];
				?>
			</div>
			<div class="logoff" >
				<a href="../logout.php">
					<img src="../css/images/logoff.png" >
				</a>
			</div>
        </div>
    </nav>
	<div style = "margin : 20px 10px 10px 10px; width:99%;"> 
		<nav style = "border: 1px solid rgba(0,0,0,0.2); height:40px; align-items: center; justify-content: center;">
		<?php
		
					//print_r($_COOKIE['id_szamba']);
					$appName = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					$conn_string = "host=__host__ port=5432 dbname=__dbname__ user=__user__ password=__passwd__";

				//	$connStr = "host=__host__ port=5432 dbname=__dbname__ user=__user__ options='--application_name=$appName'";

					//simple check
					$conn = pg_connect($conn_string);
					
					/*
					$query = "SELECT o.imie, o.nazwisko, sza.ulica, sza.numer, sza.miejscowosc, sza.kod_pocztowy, sza.pojemnosc_m3
							from osoba o, szamba_adresy sza
							where sza.id_szamba = ".$_COOKIE['id_szamba']." and o.id_osoby in (select oa.id_oso_adr from osoba_adres oa where oa.id_adr_zamiesz in (
							select asz2.id_adres from adres_szamba asz2 where asz2.id_szambo = ".$_COOKIE['id_szamba'].")) and o.wlasciciel = true;";
					
										
					$query = "SELECT STRING_AGG(DISTINCT o.imie || ' ' || o.nazwisko, ', ') AS imie_nazwisko, a.ulica, a.numer, a.miejscowosc, a.kod_pocztowy, s.pojemnosc_m3, s.id_szamba 
					FROM osoba o
					INNER JOIN osoba_adres oa ON o.id_osoby = oa.id_osoby 
					INNER JOIN adresy a ON a.ogc_fid = oa.id_adr_zamiesz 
					INNER JOIN adres_szamba asz ON asz.id_adres = oa.id_adr_zamiesz 
					INNER JOIN szamba s ON s.id_szamba = asz.id_szambo 
					where s.id_szamba = ".$_COOKIE['id_szamba']." 
					AND o.wlasciciel = true
					GROUP BY a.ulica, a.numer, a.miejscowosc, a.kod_pocztowy, s.pojemnosc_m3, s.id_szamba;";
					*/

					$query = "SELECT STRING_AGG(DISTINCT o.imie || ' ' || o.nazwisko, ', ') AS imie_nazwisko, 
							a.ulica, a.numer, a.miejscowosc, a.kod_pocztowy, s.pojemnosc_m3, s.id_szamba 
							FROM szamba s 
							inner join adres_szamba as2 on as2.id_szambo =s.id_szamba 
							inner join adresy a on a.ogc_fid = as2.id_adres 
							inner join osoba_szambo os on os.id_szambo = s.id_szamba 
							inner join osoba o on o.id_osoby = os.id_osoba 
							where s.id_szamba = ".$_COOKIE['id_szamba']." 
							AND o.wlasciciel = true
							GROUP BY a.ulica, a.numer, a.miejscowosc, a.kod_pocztowy, s.pojemnosc_m3, s.id_szamba;";


					$result = pg_query($conn,$query);
					
					while ($row = pg_fetch_row($result)) {
						$imie_nazwisko = $row[0];
						$ulica = $row[1];
						$numer = $row[2];
						$miejscowosc = $row[3];
						$kod_pocztowy = $row[4];
						$pojemnosc_m3 = $row[5];
					}
					$dane_szambo =  $imie_nazwisko." - ".$ulica."  ".$numer."  ".$miejscowosc."  ".$kod_pocztowy."  poj. m3: ".$pojemnosc_m3."<BR>";
					
					pg_free_result($result);
					//pg_close ();
		?>
		<h4  style = "font-size: 22px;	color:#154c79;  text-align: center; "><?php echo $dane_szambo;?></h4>
		</nav>
	</div>
	
</div>  
<div>
	<nav style = "border: 1px solid rgba(0,0,0,0.2); height:30px; width:600px;  align-items: center;margin-left:auto; margin-right:auto">
				<div style = "background-color: dodgerblue;height:100%">
					<button type="button" class="button" id="download-csv" style="width:118px">Download CSV</button>
				</div>
				<div style = "background-color: dodgerblue;height:100%">
					<button type="button" class="button" id="download-json" style="width:118px">Download JSON</button>
				</div>
				<div style = "background-color: dodgerblue;height:100%">
					<button type="button" class="button"id="download-xlsx" style="width:118px">Download XLSX</button>
				</div>
				<div style = "background-color: dodgerblue;height:100%">
					<button type="button" class="button" id="download-pdf" style="width:118px">Download PDF</button>
				</div>
				<div style = "background-color: dodgerblue;height:100%">
					<button type="button" class="button" id="download-html" style="width:118px">Download HTML</button>
				</div>
	</nav>
</div>
<div id="example-table" style ="margin-top:10px"></div>
<?php
			$zmienna = $_COOKIE['id_szamba'];
			//echo '<BR>----------------------<BR>';
			json_encode($zmienna);
			//echo '<BR>----------------------<BR>';
			
			$query = "select (NOW()::timestamp::date - max(pw.data_wywozu)) as dni_od_opr,max(pw.data_wywozu)  from protokol_wywozu pw where pw.id_szamba =".$_COOKIE['id_szamba'].";";
			$result = pg_query($conn,$query);
			while ($row = pg_fetch_row($result)) {
				$dni_od_wyw = $row[0];
				$data_ost_wyw = $row[1];
			}
			
			pg_free_result($result);
			
			$query = "select sum(il_dni)/count(il_dni) as ilos_dni  from (SELECT (data_wywozu - lag(data_wywozu) OVER (ORDER BY data_wywozu))::int as il_dni FROM protokol_wywozu pw where pw.id_szamba = ".$_COOKIE['id_szamba'].") as foo;";
			$result = pg_query($conn,$query);
			while ($row = pg_fetch_row($result)) {
				$sr_ilos_dni = $row[0];
			}
			
			pg_free_result($result);
?>
<div style = "height:20px"></div>
<div style = "width:99%; justify-content: space-around;"> 
	<nav style = "margin:auto; border: 1px solid rgba(0,0,0,0.2); height:40px; width:80%; align-items: center; display: flex;;">
		<label style = "font-size: 22px; color:#154c79;"> Data ostatniego wywozu: <?php echo $data_ost_wyw;?></label>
		<?php
			if ($dni_od_wyw >$sr_ilos_dni){
				echo '<label style = "font-size: 22px; color:#FF0000;"> Ilość dni od wywozu: '.$dni_od_wyw.'</label>';
			}
			else{
				echo '<label style = "font-size: 22px; color:#154c79;"> Ilość dni od wywozu: '.$dni_od_wyw.'</label>';
			} 
		?>
		
		
		<label style = "font-size: 22px; color:#154c79;"> Średnia opróżnień co: <?php echo $sr_ilos_dni;?> dni</label>
	</nav>
</div>


<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>

<script type="text/javascript" src="https://vectorjs.org/interactive.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.20/jspdf.plugin.autotable.min.js"></script>
<script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>
<script type="text/javascript" src="HistWyw.js"></script> 
 <!-- <script type="text/javascript" src="tabele_back_20231031.js"></script> -->
</body>
</html>

<!--
C:\xampp\htdocs\geoserwer\app\gridjs\gridjs\plugins\selection\dist\selection.module.js
gridjs/plugins/selection
-->