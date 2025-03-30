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
    <link rel="stylesheet" href="prot_wydruk_po_wyp.css">
	
	<style>
		@media print {
			.no-print {
				display: none;
			}
		}
	</style>
	<link rel="stylesheet" href="dodaj_firme.css">
</head>

<body>


<div class ="protWyw">
	<div class = "DataMiej">
		
		<div class="dataProtWyw">
			<label id ="labKrakData" style = "padding-left:10px" for="miejdata"></label>
		</div>
		
		<div class="miejProtWyw">
			
			<label id ="nr_zlecenia" style = "padding-right:10px" for="miejdata"></label>
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
					<label id = "labNazwaFirmy" style = "padding-left:10px" for="lnazFirma"> NazFirma</label>
					<label id = "labAdresFirmy" style = "padding-left:10px" for="ladrFirma"> Adres Firma</label>
					<label id = "labNipFirmy" style = "padding-left:10px" for="lnipFirma"> Nip firma </label>
				</div>
                
			</div>
            <div class ="divRealizacjaGmina">
                <div class = "divPoleNaGorDol" style = "border-bottom: 1px solid gray;" >
                        <h4 class ="labGotowka" style= "text-decoration: none;">Realizowane przez gmnie</h4>
                </div>
                
                <div class = "divPoleNaGorDol" >
                <div class = "divGotowka" >
                        <h4 class ="labRealGminTAK" id = "labRealGminTAK" name= "labRealGminTAK" style= "text-decoration: none;">TAK</h4>
                    </div>
                    <div class = "divPrzelew" style = "border-left: 1px solid gray;">
                        <h4 class ="labRealGminNIE" id ="labRealGminNIE" name ="labRealGminNIE" style= "text-decoration: none;">NIE</h4>
                    </div>    
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
				<label id = "labOczysz" class = "labOczysz"><label>
			</div>
			
            <div style = "display:inline-block; width: 40%;" >
               
                <div class = "divPoleNaGorDol" style = "border-bottom: 1px solid gray;" >
                    <div class = "divGotowka" >
                        <h4 class ="labGotowka" id = "labGotowka" name= "labGotowka" style= "text-decoration: none;">Gotówka</h4>
                    </div>
                    <div class = "divPrzelew" style = "border-left: 1px solid gray;">
                        <h4 class ="labPrzelew" id ="labPrzelew" name ="labPrzelew" style= "text-decoration: none;">Przelew</h4>
                    </div>
                </div>
                
                <div class = "divPoleNaGorDol" >
                    <div class = "divPrzemyslowe" >
                        <h4 class ="labPrzemyslowe" id = "labPrzemyslowe" name= "labPrzemyslowe" style= "text-decoration: none;">Rodzaj nieczystości:</h4>
                    </div>
                    <div class = "divBytowe" style = "border-left: 1px solid gray;">
                        <h4 class ="labRodzajNieczystosci" id ="labRodzajNieczystosci" name ="labRodzajNieczystosci" style= "text-decoration: none;">Bytowe</h4>
                    </div>
                </div>

            </div>
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
					<label id = "labImie" style = "padding-left:10px" for="Imie">Imie1 </label>
					<label id = "labNazwisko" style = "padding-left:10px" for="Nazwisko">Nazwisko2 </label>
				</div>
			</div>	
			
			<div class = "divDataWyw">
				<div class = "divDataWywLab">
					<h3 class ="labDataWywH">Data wywozu</h3>
				</div>

				<div class ="divDataWywDane">
					<label id = "datawywozu" style = "margin:auto; padding-left:95px" for="dataWyt"></label>
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
					<label id="labUlicaNr" style = "padding-left:10px" for="ulicanr"></label>
					<label id="labKodMiejscowosc" style = "padding-left:10px" for="miejscowosc"></label>
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
						
						<label id="pojzbior" style = "padding-left:45px; padding-top:5px;" for="pojzbior"></label>
						<label> <label>
					</div>
				
				</div>
			</div>
		</div>	
	</div>

	<div style = "width:100%; height: 40px; display:flex"> 
	</div > 

	<div style = "width:100%; height: 20px; display:flex"> 
		<div style = "width:50%; text-align: left;"> 
			<input type="submit" value="Drukuj" id = "bt_Drukuj" class ="bt_Drukuj butZapisz no-print" style = "width:140px; font-size: 1.0rem;">
		</div>
		<div style = "width:50%; text-align: right	;"> 
			<input type="submit" value="Zamknij" id = "bt_Zamknij" class ="bt_Zamknij butZapisz no-print" style = "width:140px; font-size: 1.0rem;;">
		</div>
	</div>



</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="prot_wydruk_po_wyp.js"></script> 
<script type="text/javascript" src="pop_up.js"></script> 
		

</body>

