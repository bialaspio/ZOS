<?php
// index_mapa.php - chroniona strona, do której dostęp mają tylko zalogowani użytkownicy
// Rozpocznij sesję
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Jeśli nie jest zalogowany, przekieruj do strony logowania
	//header('Location: http://192.168.0.94/geoserwer');
	header('Location: /GSPG');
    exit();
}
?>

<!doctype html>
<html lang="pl">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="stylesheet" href="ProtWyw.css">

	
</head>
	<body>
	
			<!--<script src="ProtWyw.js"></script> -->
			<?php
					$appName = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					$conn_string = "host=__host__ port=5432 dbname=__dbname__ user=__user__ password=__passwd__";

				//	$connStr = "host=__host__ port=5432 dbname=__dbname__ user=__user__ options='--application_name=$appName'";

					//simple check
					$conn = pg_connect($conn_string);
					$result = pg_query($conn, "select o.imie,o.nazwisko , ai.ulica, ai.numer, ai.kod_pocztowy, ai.miejscowosc
from osoba_adres oa
INNER join adresy_id ai
on oa.id_adr_zamel = ai.ogc_fid  
inner join osoba o 
on o.id_osoby = oa.id_osoby and o.id_osoby in (select id_osoba from osoba_szambo os where id_szambo in (".$_COOKIE['id_szamba'].")) 
");
					//print (var_dump(pg_fetch_all($result)));

					while ($row = pg_fetch_row($result)) {
						$imie = $row[0];
						$nazwisko = $row[1];
						$ulica = $row[2];
						$numer = $row[3];
						$kod = $row[4];
						$miasto= $row[5];
					}

					$imie = str_replace(" ", "&nbsp;", $imie);
					$nazwisko = str_replace(" ", "&nbsp;", $nazwisko);
					$ulica = str_replace(" ", "&nbsp;", $ulica);
					$numer = str_replace(" ", "&nbsp;", $numer);
					$kod = str_replace(" ", "&nbsp;", $kod);
					$miasto = str_replace(" ", "&nbsp;", $miasto);
					
					
					$result = pg_query($conn, "select f.nazwa, f.nip, 'Ul. '||ai.ulica ||' '|| ai.numer||' , '|| ai.miejscowosc||' '|| ai.kod_pocztowy::varchar as Adres 
from firmy f
INNER join adresy_id ai
on f.id_adres = ai.ogc_fid  
INNER join umowy u
on u.id_firma = f.id and u.id_szambo = ".$_COOKIE['id_szamba'].";");

					while ($row = pg_fetch_row($result)) {
						$nazwa_f = ($row[0]);
						$nip_f = ($row[1]);
						$adres_f = ($row[2]);
					}	

					$nazwa_f = str_replace(" ", "&nbsp;", $nazwa_f);
					$adres_f = str_replace(" ", "&nbsp;", $adres_f);
					$nip_f = str_replace(" ", "&nbsp;", $nip_f);
					 
					pg_free_result($result);
					
					$result = pg_query($conn, "select s.pojemnosc_m3 from szamba s where s.id_szamba = ".$_COOKIE['id_szamba'].";");

					while ($row = pg_fetch_row($result)) {
						$poj_sz = ($row[0]);
					}	
					
					pg_free_result($result);
					
					$oczyszczalnie = [];
					
					$result = pg_query($conn, "select nazwa_oczysz from oczyszczalnia");
					
					while ($row = pg_fetch_row($result)) {
						$oczyszczalnie[] = $row[0];
					}
					print_r(count($_POST));
			?>

		<div class ="protWyw">
		
			<form id="form" method="post"  action="ProtWywZapisz.php">
				<div class = "DataMiej">
					
					<div class="dataProtWyw">
						<label>Kraków:</label>
						<?php 
							$date = date('Y.m.d', time());
							echo "<label id=\"data_cal_l\" name=\"data_cal_l\">".$date."</label>";
							echo "<input type=\"text\" style =\"visibility: hidden;\" id=\"data_cal\" name=\"data_cal\" value =\"".$date."\">";
						?>
					</div>
					
					<div class="miejProtWyw">
						<?php
							$appName = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
							$conn_string = "host=__host__ port=5432 dbname=__dbname__ user=__user__ password=__passwd__";

							$conn = pg_connect($conn_string);

							//  id do nr 
							$result = pg_query($conn, "select max(pw.nr_prot_nr) from protokol_wywozu pw where pw.nr_prot_data = NOW()::timestamp::date;");

							while ($row = pg_fetch_row($result)) {
								$max_id_in_day = ($row[0]);
							}
								
							if(is_null($max_id_in_day)) {
								$max_id_in_day = 1;
							}
							else {
								 $max_id_in_day += 1;
							}

							pg_free_result($result);
							pg_close ();
							
							echo "<label>Zlecenie nr:</label>";
							echo "<input type=\"text\" id=\"zlecen_nr\" name=\"zlecen_nr\" value =\"".$date."/".$max_id_in_day."\">";
							
						?>
					</div>
				</div>

			<!--	<div class = "" style = "align-items: center;  display: flex;"> -->
				<div class = "div_h1" > 
					<h1 class="TytulProtWyw">Protokół wywozu nieczystości płynnych</h1>
				</div>
				
				<div class = "daneProtWyw">
					<!-- Firma -->
					<div class = "divFirma">
						<div class = "divFirmaLab">
							<h3 class ="labFirmH" >Firma</h3>
						</div>
						<div class ="divFirmaDane">
							<div class ="divFirmaDaneLab">
								<label for="lnazFirma">Nazwa Firmy</label>  
								<label for="ladrFirma">Adres</label>
								<label for="lnipFirma">NIP</label>
							</div>
							<div class ="divFirmaDaneDane">
								<input type="text" id="nazFirma" name="nazFirma" value=<?php echo ($nazwa_f); ?>>
								<input type="text" id="adrFirma" name="adrFirma" value=<?php echo ($adres_f); ?>>
								<input type="text" id="nipFirma" name="nipFirma" value=<?php echo ($nip_f); ?>>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Ooczyszczalnia  -->
				<div class = "divOczyPlat">
					<div class = "divOczyszczalnia">
						<div class = "divOczyszczalniaLab">
							<h3 class ="labOczyszczalnia" >Nazwa oczyszczalni</h3>
						</div>
						<div class = "divOczyszczalniaDaneDane">
 							<!--<input type="text" id="nazOczyszczalni" name="nazOczyszczalni" value="Nazwa oczyszczalni ścieków">-->
							<input type="text" list="lista_oczyszczalni" name="oczyszczalnia" placeholder="Wybierz oczyszczalnie">
								<datalist id="lista_oczyszczalni">
								<?php					
								foreach ($oczyszczalnie as $oczyszczalnia) {
									echo "<option value=\"".$oczyszczalnia."\">".$oczyszczalnia."</option>";
								}
								?>
								</datalist>
						</div>
						
						<div class = "divGotowka" onclick="prz_gor()">
							<h4 class ="labGotowka" id = "labGotowka" name= "labGotowka" style= "text-decoration: none;">Gotówka</h4>
							<input type="radio" class = "rb" id="rb" name="rb" value="Gotowka">
							</div>
						<div class = "divPrzelew" onclick="prz_prz()">
							<h4 class ="labPrzelew" id ="labPrzelew" name ="labPrzelew" style= "text-decoration: none;">Przelew</h4>
							<input type="radio" class = "rb" id="rb" name="rb" value="Przelew">
							
						</div>
					</div>
				</div>
	
				<script>
				function prz_gor(){
					//console.log (document.getElementById("labGotowka").style.textDecoration);
					document.getElementById("labGotowka").style.textDecoration = "none";
					document.getElementById("labPrzelew").style.textDecoration = "line-through";
				}
				
				function prz_prz(){
					//console.log (document.getElementById("labGotowka").style.textDecoration);
					document.getElementById("labGotowka").style.textDecoration = "line-through";
					document.getElementById("labPrzelew").style.textDecoration = "none";
				}
				
				//line-through
				</script>
			
				<!-- Zleceniodawca | Data wywozu-->
				<div class = "divZleDatWywMain">
					<div class = "divZleDatWyw">
						<div class = "divZleceniodawaLab">
							<h3 class ="labZleceniodawaH">Zleceniodawca</h3>
						</div>
						
						<div class = "divZleceniodawaDane">
 							<div class ="divZleceniodawaDaneLab">
								<label for="Imie">Imię</label>  
								<label for="Nazwisko">Nazwisko</label>
							</div>
							<div class ="divZleceniodawaDaneDane">
								<input type="text" id="Imie" name="Imie" value=<?php echo $imie; ?>>
								<input type="text" id="Nazwisko" name="Nazwisko" value=<?php echo $nazwisko; ?>>
							</div>
						</div>	
						
						<div class = "divDataWyw">
							<div class = "divDataWywLab">
								<h3 class ="labDataWywH">Data wywozu</h3>
							</div>

							<div class ="divDataWywDane">
								<input style = "margin-left:70px;"type="date" id="data_wyw" name="data_wyw">
							</div>
						</div>
					</div>	
				</div>
				
				
				<!-- Adres Ilość-->
				<div class = "divZleDatWywMain">
					<div class = "divZleDatWyw">
						<div class = "divZleceniodawaLab">
							<h3 class ="labZleceniodawaH">Adres posesji</h3>
						</div>
						
						<div class = "divZleceniodawaDane">
 							<div class ="divZleceniodawaDaneLab">
								<label for="lulica_numer">Ulica i nr:</label>  
								<label for="lMiejscowosc">Miejscowość</label>
							</div>
							<div class ="divZleceniodawaDaneDane">
								<input type="text" id="ulica_numer" name="ulica_numer" value=<?php echo "Ul.".$ulica."&nbsp;".$numer ?>>
								<input type="text" id="Miejscowosc" name="Miejscowosc" value=<?php echo $kod."&nbsp;".$miasto ?>>
							</div>
						</div>	
						
						<div class = "divDataWyw">
							<div class = "divDataWywLab">
								<h3 class ="labDataWywH">Ilość mertów sześciennych</h3>
							</div>

							<div class ="divZbiornikDane">
								<div  class ="divZbiornikDaneLab"> 
									<label for="poj_zbior">Pojemność:</label>  
									<label for="Miejscowosc">Ilość wywieziona:</label>
								</div>
								<div class ="divZbiornikDaneDane"> 
									<input type="text" id="pojm3" name="pojcm3" class = "ilm3" value=<?php echo $poj_sz ?>> 
									<input type="text" id="Iloscm3" name="Iloscm3" class = "ilm3" value="0.00">
								</div>
							
							</div>
						</div>
					</div>	
				</div>
			<input type="submit" value="Zapisz">
			</form>
		</div>

		
		<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>      
		<script src="https://unpkg.com/jquery@3.3.1/dist/jquery.js"></script>
		<script src="../js/leaflet.js"></script>
		<script src="../js/leaflet.measure.js"></script>
		<script src="../js/proj4-compressed.js"></script>
		<script src="../js/proj4leaflet.js"></script>
		<script src="../js/preloader.js"></script>
		<script src="../js/L.Control.ZoomBar.js"></script>
		<script src="../js/L.TileLayer.BetterWMS.js"></script>
		<script src="../js/easy-button.js"></script>
		<script src="../js/leaflet.groupedlayercontrol.js"></script>
		<script src="../js/leaflet-search.js"></script>
		<script src="../js/leaflet.browser.print.js"></script>
		
	</body>
</html>