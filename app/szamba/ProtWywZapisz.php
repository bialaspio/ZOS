<?php
// index_mapa.php - chroniona strona, do której dostęp mają tylko zalogowani użytkownicy

// Rozpocznij sesję
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Jeśli nie jest zalogowany, przekieruj do strony logowania
	//header('Location: http://192.168.0.94/geoserwer');
	header('Location:/GSPG');
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
	
	<style>
		@media print {
			.no-print {
				display: none;
			}
		}
	</style>
	
</head>

<body>
<?php
//------------------------------------------------------------------------------------------------------------------------
//Pobieramy dane z formularza
//------------------------------------------------------------------------------------------------------------------------

print_r(count($_POST));
echo '<BR>';
print_r($_POST);
echo '<BR>';
/*
print_r (is_null($_POST['data_wyw']));
echo '<BR>';
echo $_POST['data_wyw'];
echo '<BR>';
print_r (is_null($_POST['oczyszczalnia']));
echo '<BR>';
echo $_POST['oczyszczalnia'];
echo '<BR>';
echo '---------------------------------------------------------------<BR>';
*/
if (count($_POST) < 14 )
{
	echo '<script type="text/javascript"> window.open("ProtWyw.php");</script>';
}
else if (empty($_POST['data_wyw']))
{
	echo '<script type="text/javascript"> window.open("ProtWyw.php");</script>';
} 
else if (empty($_POST['oczyszczalnia']))
{
	echo '<script type="text/javascript"> window.open("ProtWyw.php");</script>';
}
else 
{
	$data_cal = $_POST['data_cal'];
	$zlecen_nr = $_POST['zlecen_nr'];
	$oczyszczalnia = $_POST['oczyszczalnia'];
	$f_nipFirma = $_POST['nipFirma'];
	$o_ulica_numer = $_POST['ulica_numer'];
	$o_miejkod = $_POST['Miejscowosc'];
	$o_nazw = $_POST['Nazwisko'];
	$o_imie = $_POST['Imie'];

	// Rozbice adresu 
	$parts =explode(chr(0xC2).chr(0xA0), $o_miejkod);
	$o_kod_pocz = $parts[0];
	$o_miejscowosc = $parts[1];

	$parts =explode(chr(0xC2).chr(0xA0), $o_ulica_numer);
	$o_ulica= str_replace("Ul.","",$parts[0]); 
	$o_nr_bud= $parts[1];

	$oczyszczalnia=$_POST['oczyszczalnia'];
	$rb=$_POST['rb'];

	$data_wyw=$_POST['data_wyw'];
	$Iloscm3=$_POST['Iloscm3'];
	//-----------

	//------------------------------------------------------------------------------------------------------------------------
	// operace na bazie 
	//------------------------------------------------------------------------------------------------------------------------
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

	// firma id 
	$result = pg_query($conn, "select f.id from firmy f where f.nip =".$f_nipFirma);
	while ($row = pg_fetch_row($result)) {
		$id_firmy = ($row[0]);
	}
	pg_free_result($result);
	//id_szamba
	$result = pg_query($conn, "select asz.id_szambo  from adres_szamba asz  where asz.id_adres in (
	select ai.ogc_fid  from adresy_id ai where ai.miejscowosc like '".$o_miejscowosc."' and  ai.ulica like '".$o_ulica."' 
	and  ai.numer like '".$o_nr_bud."' and kod_pocztowy like '".$o_kod_pocz."')");

	while ($row = pg_fetch_row($result)) {
		$id_szamba = ($row[0]);
	}
	pg_free_result($result);

	$query = "select o.id_oczysz from oczyszczalnia o where o.nazwa_oczysz like '".$oczyszczalnia."'";

	$result = pg_query($conn, $query);

	while ($row = pg_fetch_row($result)) {
		$id_oczyszcz = ($row[0]);
		
	}
	pg_free_result($result);

	$zlecen_nr = str_replace($data_cal."/",'',$zlecen_nr);

	$insert_query = "INSERT INTO public.protokol_wywozu (nr_prot_data, nr_prot_nr, firma_id, id_szamba, platnosc, ilosc_sciekow, data_wywozu, id_oczyszcz) 
	VALUES('".$data_cal."','".$zlecen_nr."',".$id_firmy.",".$id_szamba.",'".$rb."',".$Iloscm3.",'".$data_wyw."',".$id_oczyszcz.");";
	
	$result = pg_query($conn, $insert_query);
	
	pg_free_result($result);
	pg_close ();
}
?>

		<div class ="protWyw">
				<div class = "DataMiej">
					
					<div class="dataProtWyw">
						<label style = "padding-left:10px" for="miejdata">Kraków, <?php echo $_POST['data_cal']; ?></label>
					</div>
					
					<div class="miejProtWyw">
						
						<label style = "padding-right:10px" for="miejdata">Zlecenie nr: <?php echo $_POST['zlecen_nr']; ?></label>
					</div>
				</div>

				<div class = "div_h1""> 
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
								<label style = "padding-left:10px" for="lnazFirma"><?php echo $_POST['nazFirma']; ?> </label>
								<label style = "padding-left:10px" for="ladrFirma"><?php echo $_POST['adrFirma']; ?> </label>
								<label style = "padding-left:10px" for="lnipFirma"><?php echo $_POST['nipFirma']; ?> </label>
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
							<label class = "labOczysz"><?php echo $_POST['oczyszczalnia']; ?> <label>
						</div>
						
						<?php
						
						if ($_POST['rb'] == 'Gotowka' ){
							echo '<div class = "divGotowka" onclick="prz_gor()">';
							echo '<h4 class ="labGotowka" id = "labGotowka" name= "labGotowka" style= "text-decoration: none;">Gotówka</h4>';
							echo '</div>';
							echo '<div class = "divPrzelew" onclick="prz_prz()">';
							echo '<h4 class ="labPrzelew" id ="labPrzelew" name ="labPrzelew" style= "text-decoration: line-through;">Przelew</h4>';
							echo '</div>';	
						}
						
						
						
						else if ($_POST['rb'] == 'Przelew' )	{
							echo '<div class = "divGotowka" onclick="prz_gor()">';
							echo '<h4 class ="labGotowka" id = "labGotowka" name= "labGotowka" style= "text-decoration: line-through;">Gotówka</h4>';
							echo '</div>';
							echo '<div class = "divPrzelew" onclick="prz_prz()">';
							echo '<h4 class ="labPrzelew" id ="labPrzelew" name ="labPrzelew" style= "text-decoration: none;">Przelew</h4>';
							echo '</div>';	
						}
						
						?>
						<!--<div class = "divGotowka" onclick="prz_gor()">
							<h4 class ="labGotowka" id = "labGotowka" name= "labGotowka" style= "text-decoration: none;">Gotówka</h4>
						</div>
						<div class = "divPrzelew" onclick="prz_prz()">
							<h4 class ="labPrzelew" id ="labPrzelew" name ="labPrzelew" style= "text-decoration: none;">Przelew</h4>
						</div>
						-->
					</div>
				</div>
	
							<!-- Zleceniodawca | Data wywozu-->
				<div class = "divZleDatWywMain">
					<div class = "divZleDatWyw">
						<div class = "divZleceniodawaLab">
							<h3 class ="labZleceniodawaH">Zleceniodawca</h3>
						</div>
						
						<div class = "divZleceniodawaDane">
 							<div class ="divZleceniodawaDaneLab">
								<label for="Imiel">Imię</label>  
								<label for="Nazwiskol">Nazwisko</label>
							</div>
							<div class ="divZleceniodawaDaneDane">
								<label style = "padding-left:10px" for="Imie"><?php echo $_POST['Imie']; ?> </label>
								<label style = "padding-left:10px" for="Nazwisko"><?php echo $_POST['Nazwisko']; ?> </label>
							</div>
						</div>	
						
						<div class = "divDataWyw">
							<div class = "divDataWywLab">
								<h3 class ="labDataWywH">Data wywozu</h3>
							</div>

							<div class ="divDataWywDane">
								<label style = "margin:auto; padding-left:95px" for="dataWyt"><?php echo $_POST['data_wyw']; ?> </label>
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
								<label style = "padding-left:10px" for="ulicanr"><?php echo $_POST['ulica_numer']; ?> </label>
								<label style = "padding-left:10px" for="miejscowosc"><?php echo $_POST['Miejscowosc']; ?> </label>
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
									
									<label style = "padding-left:45px; padding-top:5px;" for="pojzbior"><?php echo $_POST['pojcm3']; ?> </label>
									<label> <label>
								</div>
							
							</div>
						</div>
					</div>	
				</div>
		</div>
		
		<button class="no-print" onclick="window.close()">Zamknij</button>
		<button class="no-print" onclick="window.print()">Drukuj</button>
		
		<script>
		window.onafterprint = function() {
			window.close();
		};
		</script>

		
</body>
<!--
	id_prot - auto 
	nr_prot_data 
	nr_prot_nr 
	firma_id  - select id from 
	id_szamba
	platnosc

	ilosc_sciekow
	data_wywozu
	id_oczyszcz
*/
-->

