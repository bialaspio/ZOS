<?php
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
		<link rel="stylesheet" href="style.css">
		<link rel = "icon" href ="TMCE_logo_min.png">  
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
					<h3  style = "font-size: 26px;	color:#F0F8FF;  text-align: center; padding-top: 8px; padding-bottom: 0px; border-top-width: 1px;border-top-style: solid;margin-right: 2px;border-right-width: 1px;border-right-style: solid;border-left-width: 1px;border-left-style: solid;border-bottom-width: 1px;border-bottom-style: solid;margin-bottom: 2px;margin-left: 2px;">DANE OPISOWE - GeoServer </h3>
				</div>
				
				<!--<div class="divDodawanie" >
					<ol>
						<li><a style ="color:white" href="#">
								<button type="button" class="button">DODAJ</button>
							</a>
							<ul>
								<li><a style ="color:white;background-color: gray;" href="firma/dodaj_adres.php"><button type="button" class="button" disabled>Adres</button></a></li>
								<li><a style ="color:white" href="../firma/dodaj_osobe.php"><button type="button" class="button">Osobę</button></a></li>
								<li><a style ="color:white" href="../firma/dodaj_firme.php"><button type="button" class="button">Firmę</button></a></li>
								<li><a style ="color:white" href="../firma/dodaj_szambo_czyste.php"><button type="button" class="button">Szambo</button></a></li>
							</ul>
						</li>
					</ol>
				</div>
				
				
				<div class="divFirma"  >
					<ol>
						<li><a style ="color:white" href="#">
								<button type="button" class="button">FIRMA</button>
							</a>
							<ul>
								<li><a style ="color:white" href="../firma/lista_firm.php"><button type="button" class="button">Lista firm</button></a></li>
								<li><a style ="color:white" href="../firma/dodaj_firme.php"><button type="button" class="button">Dodaj firmę</button></a></li>
								<li><a style ="color:white" href="../firma/rap_wywozu.php"><button type="button" class="button">Raport wywozu</button></a></li>
								<li><a style ="color:white" href="../firma/dodaj_umowe.php"><button type="button" class="button">Dodaj umowę</button></a></li>
							
							</ul>
						</li>
					</ol>
				</div>-->
				
				<div class="divDodawanie" >
					<ol>
						<li><a style ="color:white" href="#">
								<button type="button" class="button">DODAJ</button>
							</a>
							<ul>
								<li><a style ="color:white;background-color: gray;" href="firma/dodaj_adres.php"><button type="button" class="button" disabled>Adres</button></a></li>
								<li><a style ="color:white" href="../firma/dodaj_osobe.php"><button type="button" class="button">Osobę</button></a></li>
								<li><a style ="color:white" href="../firma/dodaj_firme.php"><button type="button" class="button">Firmę</button></a></li>
								<li><a style ="color:white" href="../firma/dodaj_szambo_czyste.php"><button type="button" class="button">Szambo</button></a></li>
								<li><a style ="color:white" href="../firma/dodaj_umowe.php"><button type="button" class="button">Dodaj umowę</button></a></li>
							</ul>
						</li>
					</ol>
				</div>

				<div class="divListy" >
					<ol>
						<li><a style ="color:white" href="#">
								<button type="button" class="button">LISTA</button>
							</a>
							<ul>
								<li><a style ="color:white" href="../firma/lista_firm.php"><button type="button" class="button">Lista firm</button></a></li>
								<li><a style ="color:white" href="../firma/lista_szamb.php"><button type="button" class="button">Lista szamb</button></a></li>
							</ul>
						</li>
					</ol>
				</div>
				
				<div class="divRaporty" >
					<ol>
						<li><a style ="color:white" href="#">
								<button type="button" class="button">RAPORT</button>
							</a>
							<ul>
								<li><a style ="color:white" href="../firma/rap_wywozu.php"><button type="button" class="button">Raport wywozu</button></a></li>
							</ul>
						</li>
					</ol>
				</div>


				<div class="mod_opis" >
					<a href="../grid_a/opisowka.php">
						<button type="button" class="button">MODUŁ OPISÓWKA</button>
					</a>
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
			<nav style = "border: 1px solid rgba(0,0,0,0.2); height:40px; align-items: center; justify-content: center; z-index: auto;">
				<div class="select">
				  <label for="warstwa">Warstwa:     </label>
				  <select name="warstwa" id="wybierz-warstwe">
					<option value="initial">Wybierz warstwę</option>                          
					<option value="PG_obreby_nazwa">Obręby</option>
					<option value="PG_dzialk_pol">Działki</option>
					<option value="PG_budynki_opisowka">Budynki</option>
					<option value="PG_adresy_id">Adresy</option>                         
					<!--<option value="PG_slupy_geo_sz">Słupy energetyczne - Tauron</option>-->
					<option value="PG_slup_nn">Słupy energetyczne - Geodezja</option>                                                 
					<option value="PG_szamba_adresy">Szamba</option>                                                 
				  </select>
				</div> 
				
				<button class="raport">Generuj tabelę</button>
				<button class="getSelectedRows">Wskaż na mapie</button>
				
				<span id="select-stats"></span>
			</nav>
		</div>
		
	</div>  

	<div>
		<nav style = "border: 1px solid rgba(0,0,0,0.2); height:30px; width:600px;  align-items: center;margin-left:auto; margin-right:auto; z-index: auto;">
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

	<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>
	<script type="text/javascript" src="tabele_tabul.js"></script> 
	<script type="text/javascript" src="https://vectorjs.org/interactive.js"></script>
	<script type="text/javascript" src="../firma/tabele_tabul.js"></script> 
	 <!-- <script type="text/javascript" src="tabele_back_20231031.js"></script> -->
	</body>
</html>

<!--
C:\xampp\htdocs\geoserwer\app\gridjs\gridjs\plugins\selection\dist\selection.module.js
gridjs/plugins/selection
-->