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

		<link rel="stylesheet" href="../css/main.css">
		<link rel="stylesheet" href="rap_wywozu.css">
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
						<h3  style = "font-size: 26px;	color:#F0F8FF;  text-align: center; padding-top: 8px; padding-bottom: 0px; border-top-width: 1px;border-top-style: solid;margin-right: 2px;border-right-width: 1px;border-right-style: solid;border-left-width: 1px;border-left-style: solid;border-bottom-width: 1px;border-bottom-style: solid;margin-bottom: 2px;margin-left: 2px;">Raporty wywozu.</h3>
					</div>
					
					
					<div class="div_firma" >
						<ol >
							<li><a style ="color:white" href="#">
									<button type="button" class="button">FIRMA</button>
								</a>
								<ul >
									<li><a style ="color:white" href="lista_firm.php"><button type="button" class="button">Lista firm</button></a></li>
									<li><a style ="color:white" href="dodaj_firme.php"><button type="button" class="button">Dodaj firmę</button></a></li>
									<li><a style ="color:white" href="rap_wywozu.php"><button type="button" class="button">Raport wywozu</button></a></li>
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
			<div style = "height:20px"></div>
	
	
	
	<?php
		
				//	print_r($_COOKIE);
					$appName = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					$conn_string = "host=__host__ port=5432 dbname=__dbname__ user=__user__ password=__passwd__";

				//	$connStr = "host=__host__ port=5432 dbname=__dbname__ user=__user__ options='--application_name=$appName'";

					//simple check
					$conn = pg_connect($conn_string);
					
					$query = "SELECT id, nazwa, nip, ulica, numer, miejscowosc, kod_pocztowy, chb_innyadrkor, innyadrkor, dodinfo, email, nrtel FROM public.firmy_adresy_edit where nip =".$_COOKIE['nip'].";";
					//echo $query;
					
					
					
					
					$result = pg_query($conn,$query);
					
					while ($row = pg_fetch_row($result)) {
						$nazwa = $row[1];
						$nip = $row[2];
						$ulica = $row[3];
						$numer = $row[4];
						$miejscowosc = $row[5];
						$kod_pocztowy = $row[6];
						$chb_innyadrkor = $row[7];
						$innyadrkor = $row[8];
						$dodinfo = $row[9];
						$email = $row[10];
						$nrtel = $row[11];
					}
				
					pg_free_result($result);
					pg_close ();
		?>
	
	
	<div class = "divdodfirma">
		<label class = "labDaneFirmy"> Edycja danych firmy. </label >
		<form id="form_dod_firma" method="post" action=".php" style = "display: flex;">
			<div class = "divP1InForm">
				<div class = "divNazFir">
					<label class = "labDodFirma">Nazwa firmy:</label>
					<input type="text" id="nazFirmy" name="nazFirmy" class = "textDodFirma" value="<?php echo $nazwa;?>">
				</div>
				
				<div class = "divNazFir">
					<label class = "labDodFirma">NIP:</label>
					<input type="text" id="nazNIP" name="nazNIP" class = "textDodFirma" value="<?php echo $nip;?>" readonly>
				</div>

				<div class = "divFirmaUlicaNr">
					<div class = "divUlicaFir">
						<label class = "divUlicaFir">Ulica:</label>
						<input type="text" id="nazUlic" name="nazUlic" class = "textDodFirma" value="<?php echo $ulica;?>">
					</div>
					<div class ="divUlicOdstepNr"> </div>
					<div class = "divNrFir">
						<label class = "labDodFirma">Nr:</label>
						<input type="text" id="nazNr" name="nazNr" class = "textDodFirma" value="<?php echo $numer;?>">
					</div>
				</div>
				
				<div class = "divFirmaKodMiej">
					<div class = "divKodFir">
						<label class = "labDodFirma">Kod:</label>
						<input type="text" id="nazKod" name="nazKod" class = "textDodFirma" value="<?php echo $kod_pocztowy;?>">
					</div>
					<div class ="divKodOdstepMiej"> </div>
					<div class = "divMiejFir">
						<label class = "labDodFirma">Miejscowość:</label>
						<input type="text" id="nazMiejs" name="nazMiejs" class = "textDodFirma" value="<?php echo $miejscowosc;?>">
					</div>
				</div>
			</div>
			
			<div class = "divP1InForm">
			
				<div class = "divchbInnyAdrKorFir">
					<?php
						if ($chb_innyadrkor == 't'){
							echo '<input type="checkbox" name="chkInnyAdrKor" id="chkInnyAdrKor" value="chkInnyAdrKor" checked >';
						}
						else {
							echo '<input type="checkbox" name="chkInnyAdrKor" id="chkInnyAdrKor" value="chkInnyAdrKor" >';
						}
					?>
					<div class ="divchbOdstepInnyAdrKor"> </div>
					<label class = "labInnyAdrKor">Inny adres korespondencyjny</label>
				</div>	
				
				<div class = "divInnyAdrKor">
					<!--<input type="textarea" id="InnyAdrKor" class = "textnazInnyAdrKor" style="display: none; " value ="<?php echo $innyadrkor?>">-->
					
					<textarea name="form[InnyAdrKor]" id="InnyAdrKor" class="textnazInnyAdrKor" style="overflow: hidden; overflow-wrap: break-word;  display: none;"><?php echo $innyadrkor?></textarea>
					
					<script>
						const checkbox = document.getElementById("chkInnyAdrKor");
						const poleTekstowe = document.getElementById("InnyAdrKor");
						
						if (checkbox.checked){
							poleTekstowe.style.display = "block";
						}

						checkbox.addEventListener("change", function() {
							poleTekstowe.style.display = checkbox.checked ? "block" : "none";
						});
					</script>
				</div>				

				<div class = "divDodInfoFir">
					<label class = "labDodInfoFir">Dodatkowe informacje:</label>
					<!-- <input type="textarea" id="nazDodInfo" name="nazDodInfo" class = "textnazDodInfo" value ="<?php echo $dodinfo;?>">-->
					<textarea name="form[nazDodInfo]" id="nazDodInfo" class="textnazDodInfo" style="overflow: hidden; overflow-wrap: break-word;"><?php echo $dodinfo?></textarea>
				</div>

				<div class = "divEmailFir">
					<label class = "labEmailFir">Email:</label>
					<input type="text" id="nazEmailFir" name="nazEmailFir" class = "textEmailFir" value="<?php echo $email;?>">
				</div>

				<div class = "divTelFir">
					<label class = "labTelFir">Nr telefonu:</label>
					<input type="text" id="nazTelFir" name="nazTelFir" class = "textTelFir" value="<?php echo $nrtel;?>">
				</div>
			</div>
		</form>
		<div class = "divButtZapisz">
			<input type="submit" value="Zapisz" class ="butZapisz">			
		</div>
	</div>
</div> 

<script src="edytuj_firme.js"></script>

</body>