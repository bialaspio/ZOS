 	<?php
		require '../menu_str.php';
	?>
	
	
	<?php
	
					$appName = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					$conn_string = "host=__host__ port=5432 dbname=__dbname__ user=__user__ password=__passwd__";


					$conn = pg_connect($conn_string);

					/*$result = pg_query($conn, "select o.imie,o.nazwisko , ai.ulica, ai.numer, ai.kod_pocztowy, ai.miejscowosc
					from osoba_adres oa
					INNER join adresy_id ai
					on oa.id_adr_zamel = ai.ogc_fid  
					inner join osoba o 
					on o.id_osoby = oa.id_osoby and o.id_osoby in 
					(select oa.id_osoby from osoba_adres oa where oa.id_adr_zamiesz in (select asz.id_adres from adres_szamba asz where asz.id_szambo in (".$_COOKIE['id_szamba']."))); 
					");*/

					$result = pg_query($conn, "SELECT o.imie, o.nazwisko, a.ulica, a.numer,a.kod_pocztowy, a.miejscowosc from szamba s 
					inner join osoba_szambo os on os.id_szambo = s.id_szamba 
					inner join osoba o on o.id_osoby = os.id_osoba 
					inner join osoba_adres oa on oa.id_osoby = o.id_osoby 
					inner join adresy a on a.ogc_fid = oa.id_adr_zamel 
					where s.id_szamba in (".$_COOKIE['id_szamba'].") and o.wlasciciel = true; 
					");

					//print (var_dump(pg_fetch_all($result)));

					while ($row = pg_fetch_row($result)) {
						$imie = $row[0];
						$nazwisko = $row[1];
						$oso_ulica = $row[2];
						$oso_numer = $row[3];
						$oso_kod = $row[4];
						$oso_miasto= $row[5];
					}

					$imie = str_replace(" ", "&nbsp;", $imie);
					$nazwisko = str_replace(" ", "&nbsp;", $nazwisko);
					$oso_ulica = str_replace(" ", "&nbsp;", $oso_ulica);
					$oso_numer = str_replace(" ", "&nbsp;", $oso_numer);
					$oso_kod = str_replace(" ", "&nbsp;", $oso_kod);
					$oso_miasto = str_replace(" ", "&nbsp;", $oso_miasto);
					
					if(empty($oso_ulica)) {
						$oso_ulica = $oso_miasto;
					}

					pg_free_result($result);
					
					$result = pg_query($conn, "select s.pojemnosc_m3 , s.rodzaj_nieczystosci,  a.ulica, a.numer, a.kod_pocztowy , a.miejscowosc  
from adresy a
INNER join adres_szamba asz 
on a.ogc_fid  = asz.id_adres  
INNER join szamba s 
on s.id_szamba = asz.id_szambo 
where asz.id_szambo =".$_COOKIE['id_szamba'].";");
					
					while ($row = pg_fetch_row($result)) {
						$pojemnosc_m3 = $row[0];
						$rodzaj_nieczystosci = $row[1];
						$sz_ulica = $row[2];
						$sz_numer = $row[3];
						$sz_kod = $row[4];
						$sz_miasto= $row[5];
					}
					
					$rodzaj_nieczystosci = str_replace(" ", "&nbsp;", $rodzaj_nieczystosci);
					$sz_ulica = str_replace(" ", "&nbsp;", $sz_ulica);
					$sz_numer = str_replace(" ", "&nbsp;", $sz_numer);
					$sz_kod = str_replace(" ", "&nbsp;", $sz_kod);
					$sz_miasto = str_replace(" ", "&nbsp;", $sz_miasto);
					
					if(empty($sz_ulica)) {
						$sz_ulica = $sz_miasto;
					}

					pg_free_result($result);
					
					$result = pg_query($conn, "select f.nazwa, f.nip, ai.ulica , ai.numer, ai.miejscowosc, ai.kod_pocztowy::varchar as Adres 
from firmy f
INNER join adresy_id ai
on f.id_adres = ai.ogc_fid  
INNER join umowy u
on u.id_firma = f.id and u.id_szambo = ".$_COOKIE['id_szamba'].";");
					//echo ($query);
					
					while ($row = pg_fetch_row($result)) {
						$nazwa_f = $row[0];
						$nip_f = $row[1];
						$ulica_f = $row[2];
						$numer_f = $row[3];
						$miejscowosc_f = $row[4];
						$kod_pocztowy_f = $row[5];
					}	

					$nazwa_f = str_replace(" ", "&nbsp;", $nazwa_f);
					$nip_f = str_replace(" ", "&nbsp;", $nip_f);
					$ulica_f = str_replace(" ", "&nbsp;", $ulica_f);
					$numer_f = str_replace(" ", "&nbsp;", $numer_f);
					$miejscowosc_f = str_replace(" ", "&nbsp;", $miejscowosc_f);
					$kod_pocztowy_f = str_replace(" ", "&nbsp;", $kod_pocztowy_f);
					
					 
					pg_free_result($result);
					
					$oczyszczalnie = [];
					
					$result = pg_query($conn, "select nazwa_oczysz from oczyszczalnia");
					
					while ($row = pg_fetch_row($result)) {
						$oczyszczalnie[] = $row[0];
					}
					
			?>
	

	
		<div style = "height:20px"></div>
		<form id="protWyw" method="post">
		<div class = "divDodajProtokol">
			<div class = "divNaglowek">
				<h1 class = "nagH1">Protokół wywozu nieczystości płynnych </h1>	
			</div>
			
			<div class = "divProtWywDataNr">
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
							
							echo "<label>Zlecenie nr:   </label>";
							echo "<input type=\"text\" id=\"zlecen_nr\" name=\"zlecen_nr\" value =\"".$date_zam."/".$max_id_in_day."\">";
						?>
				</div>
			</div>
		
		
			<label class = "labDodajOsoba">Dane osobowe. </label >			
			<!--<form id="formDodOsoba" method="post"  style = "display: flex;">-->
			<div id="formDodOsoba"  style = "display: flex;">
				<div class = "divOsobaInProtWyw">
					<div class = "divImNazOsoba">
						<label class = "labDodUmowa">Imie.</label>
						<input id="Imie" name="Imie" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($imie); ?> readonly>
					</div>
					<div class = "divImNazOsoba" >
						<label class = "labDodUmowa">Nazwisko.</label>
						<input id="Nazwisko" name="Nazwisko" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($nazwisko); ?> readonly>
					</div>
	
					<div class = "divAdrOsoba">
						<div style = "display: grid; width: 28%;">
							<label class = "labDodUmowa">Kod:</label>
							<input id="KodAdrWlSzamba" name="KodAdrWlSzamba" class="KodAdrWlSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($oso_kod); ?> readonly>
						</div>
						<div class ="divOdstep"> </div>
						<div <div style = "display: grid; width: 70%;">
							<label class = "labDodUmowa">Miejscowość: </label>
							<input id="MiejscowoscAdrWlSzamba" name="MiejscowoscAdrWlSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($oso_miasto); ?> readonly>
						</div>
					</div>

					<div class = "divAdrOsoba">
						<div style = "display: grid; width: 68%;">
							<label class = "labDodUmowa">Ulica:</label>
							<input id="UlicaAdrWlSzamba" name="UlicaAdrWlSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($oso_ulica); ?> readonly>
							
						</div>
						<div class ="divOdstep"> </div>
						<div <div style = "display: grid; width: 30%;">
							<label class = "labDodUmowa">Numer: </label>
							<input type="text" id="NrAdrWlSzamba" name="NrAdrWlSzamba" class = "textDodUmoweSzamba" value=<?php echo ($oso_numer); ?> readonly>
						</div>
					</div>
				</div>
			</div>
			<!--</form>-->
			
			<label class = "labDodajOsoba">Dane szamba.</label >
			<!--<form id="formSzambo" method="post"  style = "display: flex;">-->
			<div id="formSzambo" style = "display: flex;">
				<div class = "divSzamboInProtWyw">
					<div class = "divDaneSzamba">
						<div <div style = "display: grid; width: 28%;">
							<label class = "labDodUmowa">Pojemność w m3: </label>
							<input id="PojSzamba" name="PojSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($pojemnosc_m3); ?> readonly>
						</div>
						<div class ="divOdstep"> </div>
						<div style = "display: grid; width: 70%;">
							<label class = "labDodUmowa">Rodzaj nieczystości </label>
							<input id="RodzajNieczystosci" name="RodzajNieczystosci" class="RodzajNieczystosci textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($rodzaj_nieczystosci); ?> readonly>
						</div>
					</div>
					<div class = "divAdrSzambo">
						<div style = "display: grid; width: 28%;">
							<label class = "labDodUmowa">Kod:</label>
							<input id="KodAdrSzamba" name="KodAdrSzamba" class="KodAdrSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($sz_kod); ?> readonly>
						</div>
						<div class ="divOdstep"> </div>
						<div <div style = "display: grid; width: 70%;">
							<label class = "labDodUmowa">Miejscowość: </label>
							<input id="MiejscowoscAdrSzamba" name="MiejscowoscAdrSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($sz_miasto); ?> readonly>
						</div>
					</div>

					<div class = "divAdrSzambo">
						<div style = "display: grid; width: 68%;">
							<label class = "labDodUmowa">Ulica:</label>
							<input id="UlicaAdrSzamba" name="UlicaAdrSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($sz_ulica); ?> readonly>
							
						</div>
						<div class ="divOdstep"> </div>
						<div <div style = "display: grid; width: 30%;">
							<label class = "labDodUmowa">Numer: </label>
							<input type="text" id="NrAdrSzamba" name="NrAdrSzamba" class = "textDodUmoweSzamba" value=<?php echo ($sz_numer); ?> readonly>
						</div>
					</div>
				
				</div>
			</div>
			
			
			<div class = "divOczyszczalnia" style = "margin-top: 10px;">
				<label class = "labDodUmowa" style="font-weight: bold; margin-bottom: 5px;">Rodzaj nieczystosci:</label>
				<div class="select-style">
				<?php
					try {
						$pdo = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
					} catch (PDOException $e) {
						echo "Błąd połączenia: " . $e->getMessage();
					}

					$sql = "SELECT nazwa_oczysz FROM oczyszczalnia";
					$stmt = $pdo->query($sql);

					echo '<select name="selOczyszczalnia" id="selOczyszczalnia">';
					echo '<option value="initial">Wybierz oczyszczalnie</option>';
					while ($row = $stmt->fetch())
					{
						echo '<option value="'.$row['nazwa_oczysz'].'">'.$row['nazwa_oczysz'].'</option>';
					}
					echo '</select>';
				?>
				</div>	
			</div>
			
			<div class = "divPlatnosc" style = "margin-top: 10px;">
				<label class = "labDodUmowa" style="font-weight: bold; margin-bottom: 5px;">Rodzaj płatności:</label>
				<div class="select-style">
					<select name="selPlatnosc" id="selPlatnosc">
						<option value="initial">Wybierz rodzaj płatności.</option>
						<option value="Gotowka">Gotowka</option>
						<option value="Przelew">Przelew</option>
					</select>
				</div>	
				
			</div>

			<div class = "divchbWywozGmina" style = "margin-top: 20px; display: flex;">
				<div style = "width: 50%">
					<label for="firma">Data wywozu:</label>
					<input type="date" id="data_wyw" name="data_wyw" value="2023-12-31" min="2020-01-01" max="2035-01-01" />
				</div >
				<div style = "width: 50%">
					<input type="checkbox" name="chkWywozGmina" id="chkWywozGmina" value="chkWywozGmina">
					<label class = "labchkWywozGmina">Wywóz obslugiwany przez gminę.</label>
				</div>
			</div>

			<label class = "labDodajOsoba">Dane firmy.</label >
			<!--<form id="formFirma" method="post"  style = "display: flex;">-->
			<div id="formFirma" style = "display: flex;">
				<div class = "divFirmaInProtWyw">
					<div class = "divNazFirInProtWyw">
						<label class = "labDodFirma">Nazwa firmy:</label>
						<input type="text" id="nazFirmy" name="nazFirmy" class = "textDodFirma" value=<?php echo ($nazwa_f); ?> readonly>
					</div>
					
					<div class = "divNazFirInProtWyw">
						<label class = "labDodFirma">NIP:</label>
						<input type="text" id="nazNIP" name="nazNIP" class = "textNipFirma" value=<?php echo ($nip_f); ?> readonly>
					</div>

					<div class = "divFirmaKodMiejInProtWyw">
						<div class = "divKodFir">
							<label class = "labDodFirma">Kod:</label>
							<input id="nazKod" name="nazKod" class="nazKod textDodFirma form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($kod_pocztowy_f); ?> readonly >
						</div>
						<div class ="divKodOdstepMiej"> </div>
						<div class = "divMiejFir">
							<label class = "labDodFirma">Miejscowość:</label>
							<input id="nazMiejs" name="nazMiejs" class="nazMiejs textDodFirma form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($miejscowosc_f); ?> readonly >
						</div>
					</div>

					<div class = "divFirmaUlicaNrInProtWyw">
						<div class = "divUlicaFir">
							<label class = "divUlicaFir">Ulica:</label>
							<input id="nazUlic" name="nazUlic" class="nazUlic textDodFirma form-control basicAutoComplete" type="text" autocomplete="off" value=<?php echo ($ulica_f); ?> readonly >
						</div>
						<div class ="divUlicOdstepNr"> </div>
						<div class = "divNrFir">
							<label class = "labDodFirma">Nr:</label>
							<input type="text" id="nazNr" name="nazNr" class = "textDodFirma" value=<?php echo ($numer_f); ?> readonly>
						</div>
					</div>
				</div>
			</div>
			<!--</form>-->
			
			
			<div style = "width:100%; height: 60px; display:flex"> 
				<div style = "width:82%;">
				</div>
				<div style = "width:18%; text-align: left;"> 
					<input type="submit" value="Zapisz" id = "bt_ZapiszProtWyw" class ="bt_ZapiszProtWyw butZapisz" style = "width:70%; margin-top:15px;">
				</div>
			</div>
		</div>
		
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="ProtWyw_v02.js"></script> 
<script type="text/javascript" src="pop_up.js"></script> 
</body>