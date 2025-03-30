<?php
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
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Dane opisowe -Test</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link href="https://unpkg.com/tabulator-tables/dist/css/tabulator.min.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
	<link rel="stylesheet" href="mermaid.css">
	<link rel="stylesheet" href="../css/main.css">
	<link rel="stylesheet" href="lista_firm.css">
	<link rel="stylesheet" href="dodaj_firme.css">
	<link rel="stylesheet" href="dodaj_umowe.css">
	<link rel="stylesheet" href="dodaj_osobe.css">
	<link rel="stylesheet" href="rap_wywozu.css">
	<link rel="stylesheet" href="lista_osob_do_szambo.css">
	<link rel="stylesheet" href="ProtWyw_v02.css">
	<link rel="stylesheet" href="dodaj_szambo_z_umowa.css">
	<link rel="stylesheet" href="lista_osob_do_szambo.css">
	<link rel="stylesheet" href="charakt_zbiornika.css">
	<link rel="stylesheet" href="charakt_nieruchomosci.css">
	<link rel="stylesheet" href="rap_wyw_miej.css">
	<link rel="stylesheet" href="kontrole.css">
	<link rel="stylesheet" href="lista_kontrole.css">
	<link rel="stylesheet" href="kontrole_view.css">
	<link rel="stylesheet" href="dodaj_szambo_z_dziala.css">
	
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
				<h3  style = "font-size: 26px;	color:#F0F8FF;  text-align: center; padding-top: 8px; padding-bottom: 0px; border-top-width: 1px;border-top-style: solid;margin-right: 2px;border-right-width: 1px;border-right-style: solid;border-left-width: 1px;border-left-style: solid;border-bottom-width: 1px;border-bottom-style: solid;margin-bottom: 2px;margin-left: 2px;"></h3>
			</div>
			
			<div class="divRaporty">
							<ol>
								<li><a style="color:white" href="#">
									<button type="button" class="button">RAPORT</button>
								</a>
								<ul>
									<li><a style="color:white" href="rap_wywozu.php"><button type="button" class="button">Raport wywozu</button></a></li>
									<li><a style="color:white" href="rap_wyw_miej.php"><button type="button" class="button">Raport miejs</button></a></li>
									<li><a style="color:white" href="#"><button type="button" class="button">Kontrole</button></a>
										<ul class="submenu-right button" style="padding-left: 0px;">
											
											<li style = "display:block"><a style="color:black" href="kontrole.php"><button type="button" class="button2">Dodaj</button></a></li>
											<li style = "display:block"><a style="color:black" href="lista_kontrole.php"><button type="button" class="button2">Lista</button></a></li>

										</ul>
									</li>
								</ul>
								</li>
							</ol>
						</div>


			<div class="divDodawanie" >
				<ol>
					<li>
						<a style ="color:white" href="#">
							<button type="button" class="button">DODAJ</button>
						</a>
						<ul>
							<li><a style ="color:white;background-color: gray;" href="dodaj_adres.php"><button type="button" class="button" disabled>Adres</button></a></li>
							<li><a style ="color:white" href="dodaj_osobe.php?source=link"><button type="button" class="button">Osobę</button></a></li>
							<li><a style ="color:white" href="dodaj_firme.php"><button type="button" class="button">Firmę</button></a></li>
							<li><a style ="color:white" href="dodaj_szambo_czyste.php"><button type="button" class="button">Szambo</button></a></li>
							<li><a style ="color:white" href="dodaj_umowe.php"><button type="button" class="button">Umowę</button></a></li>
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
							<li><a style ="color:white" href="lista_firm.php"><button type="button" class="button">Firm</button></a></li>
							<li><a style ="color:white" href="lista_szamb.php"><button type="button" class="button">WlSzAdrFiUm</button></a></li>
							<li><a style ="color:white" href="lista_osob_do_szambo.php"><button type="button" class="button">Osob do szamba</button></a></li>
							<li><a style ="color:white" href="lista_osob_adres.php"><button type="button" class="button">Osoby pod adres</button></a></li>
							<li><a style ="color:white" href="charakt_zbiornika_po_adres.php"><button type="button" class="button">Zbiornik char</button></a></li>
						</ul>
					</li>
				</ol>
			</div>
			
<!--			<div class="divRaporty" >
				<ol>
					<li><a style ="color:white" href="#">
							<button type="button" class="button">RAPORT</button>
						</a>
						<ul>
							<li><a style ="color:white" href="rap_wywozu.php"><button type="button" class="button">Raport wywozu</button></a></li>
							<li><a style ="color:white" href="rap_wyw_miej.php"><button type="button" class="button">Raport miejs</button></a></li>
							<li><a style ="color:white" href="kontrole.php"><button type="button" class="button">Kontrole</button></a></li>
						</ul>
					</li>
				</ol>
			</div>
-->
			<!--
				<div class="mod_opis" >
				<a href="../grid_a/opisowka.php">
					<button type="button" class="button">MODUŁ OPISÓWKA</button>
				</a>
			</div>
			-->
			
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
	
