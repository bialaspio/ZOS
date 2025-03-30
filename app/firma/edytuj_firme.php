	<?php
		require '../menu_str.php';
	?>
	
		<div style = "height:20px"></div>
	
	</body>
	
	<?php
		
		//print_r($_COOKIE);
		
		try {
				$baza_danych = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
				$zapytanie = $baza_danych->prepare("SELECT id, nazwa, nip, ulica, numer, miejscowosc, kod_pocztowy, chb_innyadrkor, innyadrkor, dodinfo, email, nrtel FROM public.firmy_adresy_edit where nip =:nip;");

				$zapytanie->bindParam(':nip', $_COOKIE['nip']);
				$zapytanie->execute();
				$dane_firm = $zapytanie->fetchAll(PDO::FETCH_ASSOC);
				
				foreach ($dane_firm as $firma) {

					$nazwa = $firma["nazwa"];
					$nip = $firma["nip"];
					$ulica = $firma["ulica"];
					$numer = $firma["numer"];
					$miejscowosc = $firma["miejscowosc"];
					$kod_pocztowy = $firma["kod_pocztowy"];
					$chb_innyadrkor = $firma["chb_innyadrkor"];
					$innyadrkor = $firma["innyadrkor"];
					$dodinfo = $firma["dodinfo"];
					$email = $firma["email"];
					$nrtel = $firma["nrtel"];

				}

			
			
		} 
		catch (PDOException $e) {
				echo "Błąd połączenia: " . $e->getMessage();
		}
		
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
<script src="pop_up.js"></script>

</body>