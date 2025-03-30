<?php
// index_mapa.php - chroniona strona, do której dostęp mają tylko zalogowani użytkownicy

// Rozpocznij sesję
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Jeśli nie jest zalogowany, przekieruj do strony logowania
//	header('Location: http://192.168.0.94/geoserwer');
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
        <link rel="stylesheet" href="css/preloader.css">
        <link rel="stylesheet" href="css/leaflet.css">
        <link rel="stylesheet" href="css/L.Control.ZoomBar.css">
    	<link rel="stylesheet" href="css/easy-button.css">
        <link rel="stylesheet" href="css/leaflet.groupedlayercontrol.css">
        <link rel="stylesheet" href="css/leaflet-search.css">
        <link rel="stylesheet" href="css/L.Control.SwitchScaleControl.css">
		<link rel="stylesheet" href="css/leaflet.measure.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="css/L.Control.SwitchScaleControl.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link rel="stylesheet" href="https://unpkg.com/purecss@2.0.6/build/pure-min.css"  integrity="sha384-Uu6IeWbM+gzNVXJcM9XV3SohHtmWE+3VGi496jvgX1jyvDTXfdK+rfZc8C1Aehk5" crossorigin="anonymous">
		<link rel="stylesheet" href="https://unpkg.com/tabulator-tables/dist/css/tabulator.min.css" >
		<link rel="stylesheet" href="css/L.TileLayer.TmceBetterWMS.css">
		<link rel="stylesheet" href="css/main.css">
        <title>Portal mapowy - Test</title>
        <link rel = "icon" href ="legenda/TMCE_logo_min.png">
    </head>
    
	<body>
		<div class="preloader">
            <img src="css/images/load_TMCE.gif" alt="preloader">
            <div>Ładowanie mapy...</div>
        </div>
        </div>
            <div id="map">
				<nav class = "naglowek" id="header">
					
						<a href="https://www.tmce.pl">
								<img src="css/images/logoTMCE.png" style="height: 50px">
						</a>
					
                    <div id="topbar">
						<div class = "dgcz">
							<div class = "data_godz"> Data: </div>
							<div class = "czas">cos</div>
						</div>
						
						<div class = "brn">
							<h3><span>Breaking news:</span><br> Mamy listowanie atrybutów obiektów występujących w danym zakresie przestrzennym w formie tabeli atrybutów. Komunikacja przeglądarka<=>GeoServer</h3>
							
						</div>
						<div class="logoff" style = " background-color:white">
								<a href="https://tukajmapping.sharepoint.com/sites/Geoserwer" title="Share Point - Geoswerwer">
									<img src="css/images/SP.png" >
								</a>
						</div>
						<div class="divRaporty">
							<ol>
								<li><a style="color:white" href="#">
									<button type="button" class="button">RAPORT</button>
								</a>
								<ul>
									<li><a style="color:white" href="firma/rap_wywozu.php"><button type="button" class="button">Raport wywozu</button></a></li>
									<li><a style="color:white" href="firma/rap_wyw_miej.php"><button type="button" class="button">Raport miejs</button></a></li>
									<li><a style="color:white" href="#"><button type="button" class="button">Kontrole</button></a>
										<ul class="submenu-right button">
											
											<li style = "display:block"><a style="color:black" href="firma/kontrole.php"><button type="button" class="button2">Dodaj</button></a></li>
											<li style = "display:block"><a style="color:black" href="firma/lista_kontrole.php"><button type="button" class="button2">Lista</button></a></li>

										</ul>
									</li>
								</ul>
								</li>
							</ol>
						</div>

						<div class="divDodawanie" >
							<ol>
								<li><a style ="color:white" href="#">
										<button type="button" class="button">DODAJ</button>
									</a>
									<ul>
										<li><a style ="color:white;background-color: gray;" href="firma/dodaj_adres.php"><button type="button" class="button" disabled>Adres</button></a></li>
										<li><a style ="color:white" href="firma/dodaj_osobe.php?source=link"><button type="button" class="button">Osobę</button></a></li>
										<li><a style ="color:white" href="firma/dodaj_firme.php"><button type="button" class="button">Firmę</button></a></li>
										<li><a style ="color:white" href="firma/dodaj_szambo_czyste.php"><button type="button" class="button">Szambo</button></a></li>
										<li><a style ="color:white" href="firma/dodaj_umowe.php"><button type="button" class="button">Dodaj umowę</button></a></li>
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
										<li><a style ="color:white" href="firma/lista_firm.php"><button type="button" class="button">Firm</button></a></li>
										<li><a style ="color:white" href="firma/lista_szamb.php"><button type="button" class="button">WlSzAdrFiUm</button></a></li>
										<li><a style ="color:white" href="firma/lista_osob_do_szambo.php"><button type="button" class="button">Osob do szamba</button></a></li>
										<li><a style ="color:white" href="firma/charakt_zbiornika_po_adres.php"><button type="button" class="button">Zbiornik char</button></a></li>
										
									</ul>
								</li>
							</ol>
						</div>
						
						<!--<div class="divRaporty" >
							<ol>
								<li><a style ="color:white" href="#">
										<button type="button" class="button">RAPORT</button>
									</a>
									<ul>
										<li><a style ="color:white" href="firma/rap_wywozu.php"><button type="button" class="button">Raport wywozu</button></a></li>
										<li><a style ="color:white" href="firma/rap_wyw_miej.php"><button type="button" class="button">Raport miejs</button></a></li>
										<li><a style ="color:white" href="firma/kontrole.php"><button type="button" class="button">Kontrole</button></a></li>
									</ul>
								</li>
							</ol>
						</div> -->



<!--

						<div class="mod_opis" >
							<a href="grid_a/opisowka.php">
								<button type="button" class="button">MODUŁ OPISÓWKA</button>
							</a>
						</div>
-->
						<div class="logoff" style = "text-align: center; font-size: 14px; position: relative; width:100px; color:white">
								<?php
									echo "Witaj: <BR>" . $_SESSION['username'];
								?>
						</div>
						<div class="logoff" >
								<a href="logout.php">
									<img src="css/images/logoff.png" >
								</a>
						</div>
				
                    </div>
					
                    <button class="pokaz">
						<i class="fa-solid fa-layer-group fa-xl" style="color:white "></i>
                    </button>
                    <span id="strzalki-btn">>></span>
                </nav>


				<div class="leftpanelSzukaj" id="leftpanelSzukaj"> 
					<button class="szukaj"><span style="writing-Mode: vertical-rl">SZUKAJ</span></button>
					<div class="leftpanel ukryj" id="leftpanel">
						<button class="off" id="close-PRG" title="PRG/działki">PRG/działki</button>
						<button class="off" id="close-ADR" title="Adresy">Adresy</button>
						<button class="off"  id="close-SZAMBA" title="Szamba">Szamba</button>
						<button class="off" id="close-WSP" title="Współrzędne">Współrzędne</button>
						
						<div class="wyszukajPRG" id="szukaj-PRG">
							<h4>Wyszukaj PRG/działkę:</h4>
							<div class="gmina" >
								<label for="gminy">Gmina:</label>
								<select name="gminy" id="gmina" onchange="changeGmina();">
									<option value="121203_2">121203_2 Bolesław</option>
									<option value="121204_2">121204_2 Klucze</option>
									<option value="121205_5">121205_5 Olkusz</option>
									<option value="121206_2">121206_2 Trzyciąż</option>
								</select>              
								<button class="akceptuj">Zaznacz</button>
								<button class="wyczysc">Wyczyść</button>
							</div>
							<div class="obreb">
								<label for="obreby">Obręb:</label>
								<select name="obreby" id="obreby" onchange="changeObreb()">	</select>              
								<button class="akceptuj">Zaznacz</button>
								<button class="wyczysc">Wyczyść</button>
							</div>
						</div>
						
						<div class="wyszukajAdres ukryj" id="szukaj-adres" >
							<h4>Wyszukaj adres:</h4>
							<div class="gmina">
								<label for="gmina">Gmina:     </label>
								<select onchange="getCities(this.value)" name="gmina">
									<label for="gminy">Gmina:</label>
									<option value="">Wybierz gminę</option>
									<option value="121203_2">121203_2 Bolesław</option>
									<option value="121204_2">121204_2 Klucze</option>
									<option value="121205_5">121205_5 Olkusz</option>
									<option value="121206_2">121206_2 Trzyciąż</option>
								</select>
							</div>
							
							<div class="miejscowosc">
								<label for="miejscowosc">Miejscowość:</label>
								<select name="miejscowosci" id="miejscowosci"  onchange="adresyLista()">
									<option value="">Wybierz miejscowość</option>
								</select>
							</div>
						</div>
						
						
						<div class="wyszukajSzambo ukryj" id="szukaj-szambo" >
							<h4>Wyszukaj szamba:</h4>
							<div class="gmina_sz">
								<label for="gmina_sz">Gmina:     </label>
								<select onchange="getCities_sz(this.value)" name="gmina_sz" >
									<label for="gminy_sz">Gmina:</label>
									<option value="">Wybierz gminę</option>
									<option value="121204_2">121204_2 Klucze</option>
								</select>
							</div>
							<div class="miejscowosc_sz">
								<label for="miejscowosc_sz">Miejscowość:</label>
								<select name="miejscowosci_sz" id="miejscowosci_sz"  onchange="adresyLista_sz()">
									<option value="">Wybierz miejscowość</option>
								</select>
							</div>							
						</div>
						
						<div class="wyszukajWsp ukryj" id="szukaj-wsp" >
							<h4>Wyszukaj współrzędne:                 </h4>
							<div class="szerokosc" style ="display:flex">
								<label for="szerokosc">Szerokość: </label>
								<input type="number" name="szerokosc" class="szerokosc" placeholder="Wprowadź X" id="X">
							</div>
							<div class="dugosc" style ="display:flex">
								<label for="szerokosc">Długość:</label>
								<input type="number" name="dlugosc" class="dlugosc" placeholder="Wprowadź Y" id="Y">
							</div>
							<button class="akceptuj" id="btnXY">Idź do</button>       
						</div>
					</div>
				</div>
				
                <div class="leftpanelAtrybuty" id="leftpanelAtrybuty">
                    <button class="atrybutyButton" id="close-Atrybuty" title="Atrybuty"><span style="writing-Mode: vertical-rl">ATRYBUTY</span></button>
                    <div class="ukryj" id="atrybuty">
                        <h4>Atrybuty warstwy w tabeli:</h4>
                        <div class="atrybutyButtons">
                            <button class="atrybutyWyswietl akceptuj inactive" >Zaznacz zakresem</button>
                            <button class="atrybutyZabij wyczysc inactive">Wyczyść tabelę</button>
                        </div>
                        <label for="warstwa">Warstwa:     </label>
                        <select name="warstwa" id="wybierz-warstwe" onchange="checkLayers()">
                            <option value="" class="inactive">Wybierz warstwę</option>
                            <option value="PG_L_adresy" class="inactive">Adresy</option>
                            <option value="PG_L_slup_i_maszt_punkt_olkuski" class="inactive">Słupy energetyczne - Geodezja</option>
							<option value="PG_L_slup_nn"  class="inactive">Słupy energetyczne - Tauron</option>
                        </select>
                    </div>
                </div>
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
		<script src="js/leaflet.js"></script>
		<script src="js/leaflet.measure.js"></script>
		<script src="js/proj4-compressed.js"></script>
        <script src="js/proj4leaflet.js"></script>
        <script src="js/preloader.js"></script>
        <script src="js/L.Control.ZoomBar.js"></script>
		<script src="js/L.TileLayer.BetterWMS.js"></script>
		<script src="js/L.TileLayer.TmceBetterWMS.js"></script>
		<script src="js/L.TileLayer.TmceBetterWMSBud.js"></script>
		<script src="js/L.TileLayer.TmceBetterWMSSzmba.js"></script>
        <script src="js/easy-button.js"></script>
        <script src="js/leaflet.groupedlayercontrol.js"></script>
        <script src="js/leaflet-search.js"></script>
		<script src="js/leaflet.browser.print.js"></script>
		<script src="js/main.js"></script>
		<script src="js/openProtWyw.js"></script>
    </body>
</html>