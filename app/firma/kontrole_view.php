	<?php
// Odczytywanie wartości z przeglądarki
		require '../menu_str.php';
		// Pobierz wartość id_kon z adresu URL
		$idKontroli = $_GET['id_kon'];
	?>

		<div style = "height:40px"></div>

		<div class = "divKontrole">
			<div class = "divKontroleNaglowek" >
					<h4  style = "font-size: 22px;	color:#154c79;  text-align: center; ">Kontrole</h4>
            </div>
		
			<div style = "height:20px"></div>

			<div class = "divKontrDataNrProt">
				<div class = "divKrakData">
					<label >Kraków.&nbsp;</label> 
					<?php 
						$appName = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

						$conn_string = "host=__host__ port=5432 dbname=__dbname__ user=__user__ password=__passwd__";

						$conn = pg_connect($conn_string);

						//  id do nr 
						$result = pg_query($conn, "SELECT id_kon, id_rodz_kon, data_kon, nr_kon, wlasciciel, adres_nier_id, zalecenia_pokontrolne, uwagi
						FROM public.kontrole
						WHERE id_kon = ".$idKontroli.";");

						while ($row = pg_fetch_row($result)) {
							$id_kon = $row[0];
							$id_rodz_kon= $row[1];
							$data_kon= $row[2];
							$nr_kon= $row[3];
							$wlasciciel = $row[4];
							$adres_nier_id= $row[5];
							$zalecenia_pokontrolne= $row[6];
							$uwagi= $row[7];
						}

						pg_free_result($result);
						pg_close ();

						echo "<label id=\"data_cal_l\" name=\"data_cal_l\">".$data_kon."</label>";
					?>
				</div>
				
				<div class ="divOdstepDataNrProtView" style = "text-align: end;"> </div>
				
				<div class = "divNumerProt">
					<?php
						echo "<label>Zlecenie nr: ".$data_kon."/".$nr_kon."  </label>";
					?>
				</div>
			</div>

			<div style = "width: 100%; margin: auto;  display: grid;">
				<label class = "labKontrolaNag">Rodzaj kontroli:</label>
				<?php
					try {
						$pdo = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
					} catch (PDOException $e) {
						echo "Błąd połączenia: " . $e->getMessage();
					}

					$sql = "select rk.nazwa_kontroli from rodzaj_kontroli rk where id = ".$id_rodz_kon.";";
					$stmt = $pdo->query($sql);

					while ($row = $stmt->fetch())
					{
						echo '<input class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off" value="'.$row[0].'" readonly>';
					}

				?>
			</div>

			<div class = "divOsoba">
				<label class = "labKontrolaNag">Dane właściciela.</label >
				
				<?php
					try {
						$pdo = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
					} catch (PDOException $e) {
						echo "Błąd połączenia: " . $e->getMessage();
					}

					$sql = "select o.imie, o.nazwisko, a.kod_pocztowy, a.miejscowosc, a.ulica, a.numer  from osoba o 
					inner join osoba_adres oa on oa.id_osoby = o.id_osoby 
					inner join adresy a on a.ogc_fid = oa.id_adr_zamel 
					where 
					o.wlasciciel is true 
					and o.id_osoby = ".$wlasciciel.";";
					
					$stmt = $pdo->query($sql);

					while ($row = $stmt->fetch())
					{
						$imie = $row[0];
						$nazwisko= $row[1];
						$kod= $row[2];
						$miejscowosc= $row[3];
						$ulica = $row[4];
						$numer= $row[5];
					}
				?>

				
				<div style = "display:flex">
					<div class = "divImNazOsoba">
						<label class = "labKontrola">Imie.</label>
						<input id="Imie" name="Imie" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off"  value=<?php echo ($imie);?> readonly>
					</div>
					<div class ="divOdstep"> </div>
					<div class = "divImNazOsoba" >
						<label class = "labKontrola">Nazwisko.</label>
						<input id="Nazwisko" name="Nazwisko" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off"  value=<?php echo ($nazwisko);?> readonly>
					</div>
				</div>
				
				<div class = "divAdrOsoba">
					<div style = "display: grid; width: 28%;">
						<label class = "labKontrola">Kod:</label>
						<input id="KodAdrWlSzamba" name="KodAdrWlSzamba" class=" textKontoleWlasciciel basicAutoComplete" type="text" autocomplete="off"  value=<?php echo ($kod);?> readonly>
					</div>
					<div class ="divOdstep"> </div>
					<div style = "display: grid; width: 70%;">
						<label class = "labKontrola">Miejscowość: </label>
						<input id="MiejscowoscAdrWlSzamba" name="MiejscowoscAdrWlSzamba" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off"  value=<?php echo ($miejscowosc);?> readonly>
					</div>
				</div>

				<div class = "divAdrOsoba">
					<div style = "display: grid; width: 68%;">
						<label class = "labKontrola">Ulica:</label>
						<input id="UlicaAdrWlSzamba" name="UlicaAdrWlSzamba" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off"  value=<?php echo ($ulica);?> readonly>
						
					</div>
					<div class ="divOdstep"> </div>
					<div <div style = "display: grid; width: 30%;">
						<label class = "labKontrola">Numer: </label>
						<input type="text" id="NrAdrWlSzamba" name="NrAdrWlSzamba" class = "textKontoleWlasciciel"  value=<?php echo ($numer);?> readonly>
					</div>
				</div>
			</div>

			<div class="divOsobyKontr">
				<label class="labKontrolaNag">Osoby kontrolujące.</label>
				<div class="divOsobyKontrolujace">
					<div class="inputContainer_view">
						<?php
							try {
								$pdo = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
							} catch (PDOException $e) {
								echo "Błąd połączenia: " . $e->getMessage();
							}

							$sql = "select ok.imie , ok.nazwisko from osoby_kontr_kontrole okk
							inner join osoby_kontrolujace ok on ok.id_oso_kont = okk.id_oso_kont 
							where id_kon = ".$idKontroli." order by ok.nazwisko, ok.imie asc;";
							
							$stmt = $pdo->query($sql);
							echo '<div style = "display:grid; width: 100%;">';
							while ($row = $stmt->fetch())
							{
								$imie_kontoler = $row[0];
								$nazwisko_kontoler= $row[1];

								echo '<input id="texOsobaKontr" name="texOsobaKontr" class="textOsobyKontrolujace form-control basicAutoComplete" type="text" autocomplete="off" value ="'.$imie_kontoler.' '.$nazwisko_kontoler.'" readonly>';
								echo '<div style = "height:5px;"></div>';
								
							}
							echo '</div>';
						?>
					</div>
				</div>
			</div>

			<div class="divOsobyBiorUwKontr">
				<label class="labKontrolaNag">Osoby biorące udział w kontroli.</label>
				<div class="divDivOsobyBiorUwKontr">
					<div class="inputContainer">
						
					
					<?php
							try {
								$pdo = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
							} catch (PDOException $e) {
								echo "Błąd połączenia: " . $e->getMessage();
							}

							$sql = "select obuwk.osoba from osoby_bior_u_w_kont obuwk where obuwk.id_kon = ".$idKontroli." order by obuwk.osoba asc;";
							
							$stmt = $pdo->query($sql);

							echo '<div style = "display:grid; width: 100%;">';
							while ($row = $stmt->fetch())
							{
								$osoba_buwk = $row[0];
								echo '<input id="texOsobaBiorUwKontr" name="texOsobaBiorUwKontr" class="textOsobyBUWK form-control basicAutoComplete" type="text" autocomplete="off" value ="'.$osoba_buwk.'" readonly>';
								echo '<div style = "height:5px;"> </div>';
							}
							echo '</div>';
						?>
					</div>
				</div>
			</div>

			<div class = "divAdrNier">
				<label class = "labKontrolaNag">Adres nieruchomości.</label >
					<?php
						try {
							$pdo = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
						} catch (PDOException $e) {
							echo "Błąd połączenia: " . $e->getMessage();
						}

						$sql = "select a.kod_pocztowy, a.miejscowosc, a.ulica, a.numer  from adresy a 
						inner join kontrole k on k.adres_nier_id = a.ogc_fid 
						where k.id_kon = ".$idKontroli.";";
						
						$stmt = $pdo->query($sql);

						while ($row = $stmt->fetch())
						{
							$kod_nier= $row[0];
							$miejscowosc_nier= $row[1];
							$ulica_nier = $row[2];
							$numer_nier = $row[3];
						}
					?>
				<div class = "divDivAdrNier">
					<div style = "display: grid; width: 28%;">
						<label class = "labKontrola">Kod:</label>
						<input id="KodAdrNier" name="KodAdrNier" class=" textKontoleWlasciciel basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($kod_nier);?> readonly>
					</div>
					<div class ="divOdstep"> </div>
					<div style = "display: grid; width: 70%;">
						<label class = "labKontrola">Miejscowość: </label>
						<input id="MiejscowoscAdrNier" name="MiejscowoscAdrNier" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($miejscowosc_nier);?> readonly >
					</div>
				</div>

				<div class = "divDivAdrNier">
					<div style = "display: grid; width: 68%;">
						<label class = "labKontrola">Ulica:</label>
						<input id="UlicaAdrNier" name="UlicaAdrNier" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($ulica_nier);?> readonly >
						
					</div>
					<div class ="divOdstep"> </div>
					<div <div style = "display: grid; width: 30%;">
						<label class = "labKontrola">Numer: </label>
						<input type="text" id="NrAdrNier" name="NrAdrNier" class = "textKontoleWlasciciel" value=<?php echo ($numer_nier);?> readonly >
					</div>
				</div>
			</div>

			<div id="example-table-lista-osob" style ="margin-top:10px"></div>

			<div class="divOkazaneDokumenty">
				<label class="labKontrolaNag">Okazane dokumenty.</label>
				<div class="divDivOkazaneDokumenty" style="display: block;">
					<?php
						try {
							$pdo = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
						} catch (PDOException $e) {
							echo "Błąd połączenia: " . $e->getMessage();
						}

						$sql = "select od.naz_dok, od.naz_zalacznik from okazane_dokumenty od where od.id_kon = ".$idKontroli." order by od.naz_dok asc;";
						
						$stmt = $pdo->query($sql);
						
						while ($row = $stmt->fetch()){
							$naz_dok = $row[0];
							$naz_dok_file= $row[1];

							echo '<div class="inputContainer" style="display: flex; align-items: center;">';

							echo '<div style = "display:grid; width: 100%;">';
							echo '<input id="texOkazaneDokumenty" name="texOkazaneDokumenty" class="textOkazaneDokumenty form-control basicAutoComplete" type="text" autocomplete="off" style="vertical-align: middle; width: 304px" value ="'.$naz_dok.'" readonly>';
							echo '</div>';
						
							echo '<div style = "display:grid; width: 100%;">';
							//echo '<input type="text" id="fileInputOkazaneDokumenty" name="fileInputOkazaneDokumenty" style="margin-left: 10px; vertical-align: middle; border: 1px solid #BDC3C7; border-radius: 5px; width: 50%;" value ="'.$naz_dok_file.'" readonly>';
							echo '<input id="fileInputOkazaneDokumenty" name="fileInputOkazaneDokumenty" class="textOkazaneDokumenty form-control basicAutoComplete" type="text" autocomplete="off" style="vertical-align: middle; width: 304px" value ="'.$naz_dok_file.'" readonly>';
							echo '</div>';

							echo '<button id="addInputOkazDok" style="margin-left: 10px; vertical-align: middle;" onclick="viewOkazanyDokument('.$idKontroli.',\''.$naz_dok.'\',\''.$naz_dok_file.'\')">';
							echo '<img src="search-file.png" alt="podglad">';
							echo '</button>';
							echo '</div>';
							echo '<div style = "height:5px;"> </div>';
						}
					?>
				</div>
			</div>


			<div class="divZalecenia">
				<label class="labKontrolaNag">Zalecenia pokontrolne.</label>
				<div class="divDivZalecenia">
					<div class="inputContainer">
						<textarea name="texZalecenia" id="texZalecenia" class="texZalecenia" readonly><?php echo ($zalecenia_pokontrolne);?></textarea>
					</div>
				</div>
			</div>

			<div class="divUwagi">
				<label class="labKontrolaNag">Uwagi.</label>
				<div class="divDivUwagi">
					<div class="inputContainer">
						<textarea name="texUwagi" id="texUwagi" class="texUwagi" readonly><?php echo ($uwagi);?></textarea>
					</div>
				</div>
			</div>

		</div>

		<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>

		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.2/FileSaver.min.js"></script>

		<link rel="stylesheet" href="/resources/demos/style.css" />

		<script>

			//------------------------------------------------------------------------------------------
			//pobranie/wyświetlenie dokumentu 
			//------------------------------------------------------------------------------------------
			function viewOkazanyDokument(idKon,naz_dok,naz_dok_file){
				console.log("jestm w viewOkazanyDokument"); 
				var url = "ajax/ajax_pobierzPlik.php"; // Adres URL skryptu PHP do pobierania pliku
				var data = {
					idKon: idKon,
					nazwaDok: naz_dok,
					nazwaZalacznik: naz_dok_file
				};

				$.ajax({
					url: url,
					method: "POST",
					data: data,
					
					success: function(response) {
						var dane_dokumenty = JSON.parse(response);
						// Przechodzimy przez każdy element w tablicy
						dane_dokumenty.forEach(function(dokument) {
							// Zapisujemy dane do zmiennych
							var id = dokument.id;
							var id_kon = dokument.id_kon;
							var naz_dok = dokument.naz_dok;
							var naz_zalacznik = dokument.naz_zalacznik;
							var zalacznik = dokument.zalacznik;

							// Możesz teraz użyć tych zmiennych
							console.log({zalacznik});
							saveFile(zalacznik,naz_zalacznik);
						});
					},
					error: function(error) {
					console.error(error);
					}
				});
			}

			function saveFile(data, fileName) {
				console.log("jest w zapise");
				console.log({fileName});
				console.log({data});
				var encoder = new TextEncoder();
				var dataArray = encoder.encode(data);
				var blob = new Blob([dataArray], {type: "application/octet-stream"});
				console.log({blob});
				window.saveAs(blob, fileName);
			}


			//------------------------------------------------------------------------------------------
			//zegar
			//------------------------------------------------------------------------------------------
			let zegarContent=document.querySelector(".czas")
			function zegar(){
			let d =new Date();
			let day = d.getDay()-1;
			let month = d.getMonth()+1;
			let year = d.getFullYear(); 
			let hour= d.getHours();
			let minutes= d.getMinutes();
			let seconds= d.getSeconds();
			hour=hour<10 ? "0" + hour: hour;
			minutes=minutes<10 ? "0" + minutes: minutes;
			seconds=seconds<10 ? "0" + seconds: seconds;
			zegarContent.innerHTML= year+'.'+addLeadingZero(month)+'.'+addLeadingZero(day)+' '+hour + ':'+ minutes+':'+seconds
			};
			zegar();

			setInterval(zegar,1000);

			function addLeadingZero(number) {
			return number < 10 ? "0" + number : number;
			}

		</script>
		


</body>