<?php
require '../menu_str.php';
?>

<div style="height:20px"></div>

<div class="divDodajOsoba">
    <label class="labDodajOsoba">Dodanie osoby</label>
    <form id="formDodOsoba" method="post" style="display: flex;">
        <div class="divOsobaInForm">
            <!-- Imię i nazwisko -->
            <div class="divImNazOsoba">
                <label class="labDodUmowa">Imię:</label>
                <input id="Imie" name="Imie" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Imię">
            </div>
            <div class="divImNazOsoba">
                <label class="labDodUmowa">Nazwisko:</label>
                <input id="Nazwisko" name="Nazwisko" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwisko">
            </div>

            <!-- Adres zameldowania -->
            <label class="labAdres">Adres zameldowania</label>
            <div class="divAdrOsoba">
                <div style="display: grid; width: 28%;">
                    <label class="labDodUmowa">Kod:</label>
                    <input id="KodAdrWlSzamba" name="KodAdrWlSzamba" class="KodAdrWlSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="00-000">
                </div>
                <div class="divOdstep"></div>
                <div style="display: grid; width: 70%;">
                    <label class="labDodUmowa">Miejscowość:</label>
                    <input id="MiejscowoscAdrWlSzamba" name="MiejscowoscAdrWlSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość">
                </div>
            </div>

            <div class="divAdrOsoba">
                <div style="display: grid; width: 68%;">
                    <label class="labDodUmowa">Ulica:</label>
                    <input id="UlicaAdrWlSzamba" name="UlicaAdrWlSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwa ulicy">
                </div>
                <div class="divOdstep"></div>
                <div style="display: grid; width: 30%;">
                    <label class="labDodUmowa">Numer:</label>
                    <input type="text" id="NrAdrWlSzamba" name="NrAdrWlSzamba" class="textDodUmoweSzamba" placeholder="Wprowadź numer">
                </div>
            </div>

            <!-- Właściciel szamba -->
            <div class="divchbWlascicielSzamba">
                <input type="checkbox" name="chkWlascicielSzamba" id="chkWlascicielSzamba" value="chkWlascicielSzamba">
                <label style="font-size: 15px; font-weight: bold; color: #424949; margin: 20px 0px 10px 0;">
                    Właściciel szamba
                    <span style="color: red; font-weight: normal;">(* należy zaznaczyć, jeżeli dana osoba jest właścicielem szamba)</span>
                </label>
            </div>

            <!-- Inny adres zamieszkania -->
            <div class="divchbInnyAdrZamieszkania">
                <input type="checkbox" name="chkInnyAdrZamieszkania" id="chkInnyAdrZamieszkania" value="chkInnyAdrZamieszkania">
                <label style="font-size: 15px; font-weight: bold; color: #424949; margin: 20px 0px 10px 0;">Inny adres zamieszkania</label>
            </div>

            <div class="divInnyAdrZamieszkania" id="divInnyAdrZamieszkania" style="display: none;">
                <div class="divAdrOsoba">
                    <div style="display: grid; width: 28%;">
                        <label class="labDodUmowa">Kod:</label>
                        <input id="KodZamAdrWlSzamba" name="KodZamAdrWlSzamba" class="KodZamAdrWlSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="00-000">
                    </div>
                    <div class="divOdstep"></div>
                    <div style="display: grid; width: 70%;">
                        <label class="labDodUmowa">Miejscowość:</label>
                        <input id="MiejscowoscZamAdrWlSzamba" name="MiejscowoscZamAdrWlSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość">
                    </div>
                </div>

                <div class="divAdrOsoba">
                    <div style="display: grid; width: 68%;">
                        <label class="labDodUmowa">Ulica:</label>
                        <input id="UlicaZamAdrWlSzamba" name="UlicaZamAdrWlSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwa ulicy">
                    </div>
                    <div class="divOdstep"></div>
                    <div style="display: grid; width: 30%;">
                        <label class="labDodUmowa">Numer:</label>
                        <input type="text" id="NrZamAdrWlSzamba" name="NrZamAdrWlSzamba" class="textDodUmoweSzamba" placeholder="Wprowadź numer">
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Przycisk zapisu -->
    <div style="width: 100%; height: 60px; display: flex;">
        <div style="width: 82%;"></div>
        <div style="width: 18%; text-align: left;">
            <input type="submit" value="Zapisz" id="bt_ZapiszOsosba" class="bt_ZapiszOsosba butZapisz" style="width: 70%; margin-top: 15px;">
        </div>
    </div>
</div>

<!-- Style i skrypty -->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script src="dodaj_osobe.js"></script>
<script src="pop_up.js"></script>

<script>
    // Obsługa pola "Inny adres zamieszkania"
    document.getElementById("chkInnyAdrZamieszkania").addEventListener("change", function () {
        const divInnyAdrZamieszkania = document.getElementById("divInnyAdrZamieszkania");
        divInnyAdrZamieszkania.style.display = this.checked ? "block" : "none";
    });
</script>
</body>
</html>