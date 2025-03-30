	<?php
		require '../menu_str.php';
	?>
	
		<div style = "height:40px"></div>

		<div class = "divKontrole">
			<form id="formKontrole" method="post" ">
			<div class = "divKontroleNaglowek" >
					<h4  style = "font-size: 22px;	color:#154c79;  text-align: center; ">Kontrole</h4>
            </div>
		
			<div style = "height:20px"></div>

			
			<div class = "divKontrDataNrProt">
				<div class = "divKrakData">
					<label >Kraków.&nbsp;</label> 
					<?php 
							
							$date = date('Y.m.d', time());
							$date_zam = date('Y-m-d', time());
							echo "<label id=\"data_cal_l\" name=\"data_cal_l\">".$date."</label>";
							echo "<input type=\"text\" style =\"visibility: hidden;\" id=\"data_cal\" name=\"data_cal\" value =\"".$date."\">";
					?>
				</div>
				
				<div class ="divOdstepDataNrProt"> </div>
				
				<div class = "divNumerProt">
					<?php
						$appName = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
						$conn_string = "host=__host__ port=5432 dbname=__dbname__ user=__user__ password=__passwd__";

						$conn = pg_connect($conn_string);

						//  id do nr 
						$result = pg_query($conn, "select max(k.nr_kon) from kontrole k where k.data_kon = NOW()::timestamp::date;");

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
						
						echo "<label>Zlecenie nr:   </label>";
						echo "<input type=\"text\" id=\"zlecen_nr\" name=\"zlecen_nr\"  value =\"".$date_zam."/".$max_id_in_day."\" style=\"width: 65%;\">";
					?>
				</div>
			</div>

			<div class = "divChbRodzKontr" style = "margin-top: 10px; ">
				<div style = "width: 90%; margin: auto;  display: block;">
					<label class = "labKontrola" style="font-weight: bold; margin-bottom: 5px;">Rodzaj kontroli:</label>
					<div class="select-style-rodzaj-kontroli">
					<?php
						try {
							$pdo = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
						} catch (PDOException $e) {
							echo "Błąd połączenia: " . $e->getMessage();
						}

						$sql = "SELECT nazwa_kontroli FROM rodzaj_kontroli";
						$stmt = $pdo->query($sql);

						echo '<select name="selRodzKontroli" id="selRodzKontroli">';
						echo '<option value="initial">Wybierz rodzaj kontroli</option>';
						while ($row = $stmt->fetch())
						{
							echo '<option value="'.$row['nazwa_kontroli'].'">'.$row['nazwa_kontroli'].'</option>';
						}
						echo '</select>';
					?>
					</div>	
				</div>
			</div>


			<?php 
				$appName = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				$conn_string = "host=__host__ port=5432 dbname=__dbname__ user=__user__ password=__passwd__";

				$conn = pg_connect($conn_string);

				$query = "select  o.imie, o.nazwisko, a.kod_pocztowy, a.miejscowosc, a.ulica, a.numer  from budynki_egib be 
							left join adr_bud ab on ab.adr_bud = be.ogc_fid 
							left join adresy a on a.ogc_fid = ab.id_adr 
							left join osoba_adres oa on oa.id_adr_zamel = ab.id_adr 
							left join osoba o on o.id_osoby = oa.id_osoby 
							where o.wlasciciel is true 
							and be.id_budynku like '".$_COOKIE['id_budynku']."';";
				//$result = pg_query_params($conn, $query, array($_COOKIE['id_budynku']));
				$result = pg_query($conn, $query);

				while ($row = pg_fetch_row($result)) {
					$imie = $row[0];
					$nazwisko = $row[1];
					$kod_pocztowy = $row[2];
					$miejscowosc = $row[3];
					$ulica = $row[4];
					$numer = $row[5];
				}
				pg_free_result($result);
				pg_close($conn);
			?>

			<div class = "divOsoba">
				<label class = "labKontrolaNag">Dane właściciela.</label >
				<div style = "display:flex">
					<div class = "divImNazOsoba">
						<label class = "labKontrola">Imie.</label>
						<input id="Imie" name="Imie" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($imie);?> readonly>
					</div>
					<div class ="divOdstep"> </div>
					<div class = "divImNazOsoba" >
						<label class = "labKontrola">Nazwisko.</label>
						<input id="Nazwisko" name="Nazwisko" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($nazwisko);?> readonly>
					</div>
				</div>
				
				<div class = "divAdrOsoba">
					<div style = "display: grid; width: 28%;">
						<label class = "labKontrola">Kod:</label>
						<input id="KodAdrWlSzamba" name="KodAdrWlSzamba" class=" textKontoleWlasciciel basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($kod_pocztowy);?> readonly>
					</div>
					<div class ="divOdstep"> </div>
					<div style = "display: grid; width: 70%;">
						<label class = "labKontrola">Miejscowość: </label>
						<input id="MiejscowoscAdrWlSzamba" name="MiejscowoscAdrWlSzamba" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($miejscowosc);?> readonly>
					</div>
				</div>

				<div class = "divAdrOsoba">
					<div style = "display: grid; width: 68%;">
						<label class = "labKontrola">Ulica:</label>
						<input id="UlicaAdrWlSzamba" name="UlicaAdrWlSzamba" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($ulica);?> readonly>
						
					</div>
					<div class ="divOdstep"> </div>
					<div <div style = "display: grid; width: 30%;">
						<label class = "labDodUmowa">Numer: </label>
						<input type="text" id="NrAdrWlSzamba" name="NrAdrWlSzamba" class = "textKontoleWlasciciel" value=<?php echo ($numer);?> readonly>
					</div>
				</div>
			</div>

			<div class="divOsobyKontr">
				<label class="labKontrolaNag">Osoby kontrolujące.</label>
				<div class="divOsobyKontrolujace">
					<div class="inputContainer">
						<div style = "display:grid; width: 100%;">
							<input id="texOsobaKontr" name="texOsobaKontr" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Osoba kontrolująca." >
						</div>	
						<button id="addInput" style="margin-left: 10px;">
							<img src="plus.png" alt="plus">
						</button>
					</div>
				</div>
			</div>

			<div class="divOsobyBiorUwKontr">
				<label class="labKontrolaNag">Osoby biorące udział w kontroli.</label>
				<div class="divDivOsobyBiorUwKontr">
					<div class="inputContainer">
						<div style = "display:grid; width: 100%;">
							<input id="texOsobaBiorUwKontr" name="texOsobaBiorUwKontr" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Osoba biorąca udział w kontroli." >
						</div>
						<button id="addInputOBUWK" style="margin-left: 10px;">
							<img src="plus.png" alt="plus">
						</button>
					</div>
				</div>
			</div>

			<div class = "divAdrNier">
				<label class = "labKontrolaNag">Adres nieruchomości.</label >
				
				<div class = "divDivAdrNier">
					<div style = "display: grid; width: 28%;">
						<label class = "labKontrola">Kod:</label>
						<input id="KodAdrNier" name="KodAdrNier" class=" textKontoleWlasciciel basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($kod_pocztowy);?> readonly>
					</div>
					<div class ="divOdstep"> </div>
					<div style = "display: grid; width: 70%;">
						<label class = "labKontrola">Miejscowość: </label>
						<input id="MiejscowoscAdrNier" name="MiejscowoscAdrNier" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($miejscowosc);?> readonly>
					</div>
				</div>

				<div class = "divDivAdrNier">
					<div style = "display: grid; width: 68%;">
						<label class = "labKontrola">Ulica:</label>
						<input id="UlicaAdrNier" name="UlicaAdrNier" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($ulica);?> readonly>
						
					</div>
					<div class ="divOdstep"> </div>
					<div <div style = "display: grid; width: 30%;">
						<label class = "labDodUmowa">Numer: </label>
						<input type="text" id="NrAdrNier" name="NrAdrNier" class = "textKontoleWlasciciel" value=<?php echo ($numer);?> readonly>
					</div>
				</div>
			</div>

			<div id="example-table-lista-osob" style ="margin-top:10px"></div>

			
			<div class="divOkazaneDokumenty">
				<label class="labKontrolaNag">Okazane dokumenty.</label>
				<div class="divDivOkazaneDokumenty">
					<div class="inputContainer" style="display: flex; align-items: center;">
						<div style = "display:grid; width: 100%;">
							<input id="texOkazaneDokumenty" name="texOkazaneDokumenty" class="textKontoleWlasciciel form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Dodaj dokument" style="vertical-align: middle; width: 304px">
						</div>
						<!-- Dodane pole do wyboru pliku -->
						<div style = "display:grid; width: 100%;">
							<input type="file" id="fileInput" name="fileInput" style="margin-left: 10px; vertical-align: middle; width: 244px">
						</div>
						<button id="addInputOkazDok" style="margin-left: 10px; vertical-align: middle;" onclick="addNewInput()">
							<img src="plus.png" alt="plus">
						</button>
					</div>
				</div>
			</div>

			<div class="divZalecenia">
				<label class="labKontrolaNag">Zalecenia pokontrolne.</label>
				<div class="divDivZalecenia">
					<div class="inputContainer">
						<textarea name="texZalecenia" id="texZalecenia" class="texZalecenia" ></textarea>
					</div>
				</div>
			</div>

			<div class="divUwagi">
				<label class="labKontrolaNag">Uwagi.</label>
				<div class="divDivUwagi">
					<div class="inputContainer">
						<textarea name="texUwagi" id="texUwagi" class="texUwagi" ></textarea>
					</div>
				</div>
			</div>
			
			<div style = "height:20px"></div>

			<div class = "divButtZapisz">
				<input id = "bt_Zapisz_kon" type="submit" value="Zapisz" class ="bt_Zapisz_kon butZapisz">			
			</div>
			
			<div style = "height:20px"></div>
			</form>
		</div>
		
<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="kontrole.js"></script>
<script src="kontrole_walidacja.js"></script>
<script src="pop_up.js"></script>
</body>