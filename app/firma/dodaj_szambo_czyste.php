<?php
require '../menu_str.php';
?>

<div style="height:20px"></div>

<div class="divDodSza">
    <form id="formDodSza" method="post" style="display: block;">
        <div class="divDodSzaOsoba">
            <!-- Nagłówek: Właściciel szamba -->
            <label class="labNaglowki">Właściciel szamba</label>

            <!-- Imię i nazwisko -->
            <div class="divDodSzaImNazOsoba">
                <label class="labDodUmowa">Imię:</label>
                <input id="Imie" name="Imie" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Imię">
            </div>
            <div class="divDodSzaImNazOsoba">
                <label class="labDodUmowa">Nazwisko:</label>
                <input id="Nazwisko" name="Nazwisko" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwisko">
            </div>

            <!-- Adres właściciela -->
            <div class="divDodSzaAdrOsoba">
                <div style="display: grid; width: 28%;">
                    <label class="labDodUmowa">Kod:</label>
                    <input id="KodAdrOsoba" name="KodAdrOsoba" class="KodAdrOsoba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="00-000">
                </div>
                <div class="divOdstep"></div>
                <div style="display: grid; width: 70%;">
                    <label class="labDodUmowa">Miejscowość:</label>
                    <input id="MiejscowoscAdrOsoba" name="MiejscowoscAdrOsoba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość">
                </div>
            </div>

            <div class="divDodSzaAdrOsoba">
                <div style="display: grid; width: 68%;">
                    <label class="labDodUmowa">Ulica:</label>
                    <input id="UlicaAdrOsoba" name="UlicaAdrOsoba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwa ulicy">
                </div>
                <div class="divOdstep"></div>
                <div style="display: grid; width: 30%;">
                    <label class="labDodUmowa">Numer:</label>
                    <input id="NrAdrOsoba" name="NrAdrOsoba" class="textDodUmoweSzamba" type="text" placeholder="Wprowadź numer">
                </div>
            </div>

            <!-- Nagłówek: Dane szamba -->
            <label class="labNaglowki">Dane szamba</label>

            <!-- Adres szamba -->
            <div class="divDodSzaAdrOsoba">
                <div style="display: grid; width: 28%;">
                    <label class="labDodUmowa">Kod:</label>
                    <input id="KodAdrSzamba" name="KodAdrSzamba" class="KodAdrSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="00-000">
                </div>
                <div class="divOdstep"></div>
                <div style="display: grid; width: 70%;">
                    <label class="labDodUmowa">Miejscowość:</label>
                    <input id="MiejscowoscAdrSzamba" name="MiejscowoscAdrSzamba" class="MiejscowoscAdrSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość">
                </div>
            </div>

            <div class="divDodSzaAdrOsoba">
                <div style="display: grid; width: 68%;">
                    <label class="labDodUmowa">Ulica:</label>
                    <input id="UlicaAdrSzamba" name="UlicaAdrSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwa ulicy">
                </div>
                <div class="divOdstep"></div>
                <div style="display: grid; width: 30%;">
                    <label class="labDodUmowa">Numer:</label>
                    <input id="NrAdrSzamba" name="NrAdrSzamba" class="textDodUmoweSzamba" type="text" placeholder="Wprowadź numer">
                </div>
            </div>

            <!-- Pojemność i rodzaj nieczystości -->
            <div class="divDodSzaAdrOsoba">
                <div style="display: grid; width: 50%;">
                    <label class="labDodUmowa">Pojemność zbiornika w m3:</label>
                    <input id="Pojemnosc" name="Pojemnosc" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="m3">
                </div>
                <div style="display: grid; width: 50%;">
                    <label class="labDodUmowa">Rodzaj nieczystości:</label>
                    <select name="RodzajNieczystosci" id="RodzajNieczystosci" class="form-control">
                        <option value="">Wybierz rodzaj</option>
                        <option value="Przemysłowe">Przemysłowe</option>
                        <option value="Bytowe">Bytowe</option>
                    </select>
                </div>
            </div>

            <!-- Mapa -->
            <div id="mapaSzamba" style="width: 100%; height: 300px; margin-top: 20px;"></div>

            <!-- Przycisk zapisu -->
            <div style="width: 100%; text-align: right;">
                <input type="submit" value="Zapisz" id="bt_ZapiszSzambo" class="bt_ZapiszSzambo butZapisz" style="width: 20%; margin-top: 15px;">
            </div>
        </div>
    </form>
</div>

<!-- Style i skrypty -->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/proj4@latest/dist/proj4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="dodaj_szambo_czyste.js"></script>
<script src="pop_up.js"></script>
</body>