<?php
require '../menu_str.php';
?>

<div style="height:20px"></div>

<div class="divdodfirma">
    <label class="labDaneFirmy">Dodanie danych firmy</label>
    <form id="form_dod_firma" method="post" action="ajax/ajax_dodaj_firme.php" style="display: flex;">
        <div class="divP1InForm">
            <!-- Nazwa firmy -->
            <div class="divNazFir">
                <label class="labDodFirma">Nazwa firmy:</label>
                <input type="text" id="nazFirmy" name="nazFirmy" class="textDodFirma" placeholder="Wprowadź nazwę firmy">
            </div>

            <!-- NIP -->
            <div class="divNazFir">
                <label class="labDodFirma">NIP:</label>
                <input type="text" id="nazNIP" name="nazNIP" class="textNipFirma" placeholder="Wprowadź NIP" onchange="validateNIP()">
            </div>

            <!-- Kod i miejscowość -->
            <div class="divFirmaKodMiej">
                <div class="divKodFir">
                    <label class="labDodFirma">Kod:</label>
                    <input id="nazKod" name="nazKod" class="nazKod textDodFirma form-control basicAutoComplete" type="text" autocomplete="off" placeholder="00-000">
                </div>
                <div class="divKodOdstepMiej"></div>
                <div class="divMiejFir">
                    <label class="labDodFirma">Miejscowość:</label>
                    <input id="nazMiejs" name="nazMiejs" class="nazMiejs textDodFirma form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość">
                </div>
            </div>

            <!-- Ulica i numer -->
            <div class="divFirmaUlicaNr">
                <div class="divUlicaFir">
                    <label class="labDodFirma">Ulica:</label>
                    <input id="nazUlic" name="nazUlic" class="nazUlic textDodFirma form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Ulica">
                </div>
                <div class="divUlicOdstepNr"></div>
                <div class="divNrFir">
                    <label class="labDodFirma">Nr:</label>
                    <input type="text" id="nazNr" name="nazNr" class="textDodFirma" placeholder="Wprowadź numer">
                </div>
            </div>
        </div>

        <div class="divP1InForm">
            <!-- Inny adres korespondencyjny -->
            <div class="divchbInnyAdrKorFir">
                <input type="checkbox" name="chkInnyAdrKor" id="chkInnyAdrKor" value="chkInnyAdrKor">
                <div class="divchbOdstepInnyAdrKor"></div>
                <label class="labInnyAdrKor">Inny adres korespondencyjny</label>
            </div>

            <div class="divInnyAdrKor">
                <textarea name="form[InnyAdrKor]" id="InnyAdrKor" class="textnazInnyAdrKor" style="overflow: hidden; overflow-wrap: break-word; display: none;"></textarea>
            </div>

            <!-- Dodatkowe informacje -->
            <div class="divDodInfoFir">
                <label class="labDodInfoFir">Dodatkowe informacje:</label>
                <textarea name="form[nazDodInfo]" id="nazDodInfo" class="textnazDodInfo" style="overflow: hidden; overflow-wrap: break-word;"></textarea>
            </div>

            <!-- Email -->
            <div class="divEmailFir">
                <label class="labEmailFir">Email:</label>
                <input type="text" id="nazEmailFir" name="nazEmailFir" class="textEmailFir" placeholder="Wprowadź email">
            </div>

            <!-- Telefon -->
            <div class="divTelFir">
                <label class="labTelFir">Nr telefonu:</label>
                <input type="text" id="nazTelFir" name="nazTelFir" class="textTelFir" placeholder="Wprowadź numer telefonu">
            </div>
        </div>
    </form>

    <!-- Przycisk zapisu -->
    <div class="divButtZapisz">
        <button type="button" class="butZapisz" onclick="DodajFirme()">Zapisz</button>
    </div>
</div>

<!-- Style i skrypty -->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script src="dodaj_firme.js"></script>
<script src="pop_up.js"></script>

<script>
    // Obsługa pola "Inny adres korespondencyjny"
    document.getElementById("chkInnyAdrKor").addEventListener("change", function () {
        const poleTekstowe = document.getElementById("InnyAdrKor");
        poleTekstowe.style.display = this.checked ? "block" : "none";
    });
</script>
</body>
</html>