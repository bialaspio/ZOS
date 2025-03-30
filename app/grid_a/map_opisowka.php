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

<!doctype html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="initial-scale=1">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <link rel="stylesheet" href="../css/preloader.css">
        <link rel="stylesheet" href="../css/leaflet.css">
        <link rel="stylesheet" href="../css/L.Control.ZoomBar.css">
    	<link rel="stylesheet" href="../css/easy-button.css">
        <link rel="stylesheet" href="../css/leaflet.groupedlayercontrol.css">
        <link rel="stylesheet" href="../css/leaflet-search.css">
        <link rel="stylesheet" href="../css/main.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <title>Portal mapowy - Test</title>
        <link rel = "icon" href ="../legenda/TMCE_logo_min.png">
    </head>
    
	<body>
       
            <div id="map">
				<nav class = "naglowek" id="header">
					<a href="https://www.tmce.pl">
						<img src="../css/images/logoTMCE.png" style="height: 50px">
					</a>
					
                    <div id="topbar">
						<div class = "dgcz">
							<div class = "data_godz"> Data: </div>
							<div class = "czas">
							<p id="tabela"></p>
							</div>
						</div>
						
						<div class = "brn">
							<h3><span>Breaking news:</span><br> 
							Miejsce na istotne informacje !!!
							</h3>
						</div>
						<div class="div_firma"  >
							<ol>
								<li><a style ="color:white" href="#">
										<button type="button" class="button">FIRMA</button>
									</a>
									<ul>
										<li><a style ="color:white" href="../firma/lista_firm.php"><button type="button" class="button">Lista firm</button></a></li>
										<li><a style ="color:white" href="../firma/dodaj_firme.php"><button type="button" class="button">Dodaj firmę</button></a></li>
										<li><a style ="color:white" href="../firma/rap_wywozu.php"><button type="button" class="button">Raport wywozu</button></a></li>
										<li><a style ="color:white" href="firma/dodaj_umowe.php"><button type="button" class="button">Dodaj umowę</button></a></li>
									
									</ul>
								</li>
							</ol>
						</div>
						
						<div class="mod_opis" >
							<a href="opisowka.php">
								<button type="button" class="button" style = "padding-left: 6px;">MODUŁ OPISÓWKA</button>
							</a>
						</div>
						
						<div class="mod_opis">
							<a href="../index_mapa.php">
								<button type="button" class="button">STRONA GŁÓWNA</button>
							</a>
						</div>
						
						<div class="logoff" style = "text-align: center; font-size: 14px; position: relative; width:100px; color:white">
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
					
                    <button class="pokaz">
						<i class="fa-solid fa-layer-group fa-xl" style="color:white "></i>
                    </button>
                    <span id="strzalki-btn">>></span>
                </nav>

				
				<div class = "poz_div_mark">
					<span class = "proj_wsg84" >WGS84 </span><div class = "WSP_WGS_84">50.300000 19.630000</div>
					<span class = "proj_inne">
						<select style="font-weight:700" id = "ComboProj"> 
							<option value="PUWG2000s6" >PUWG2000s6</option> 
							<option value="PUWG1992" selected>PUWG1992</option> 
							<option value="UTM" >UTM</option>
							<option value="WKID">WKID</option>
						</select>
						
					</span><div class = "WSP_inne"> 5448854.94 270505.78</div>
				</div>
		    </div>



		<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>
		
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
		<script src="../js/map_opisowka.js"></script>
        
    </body>
</html>