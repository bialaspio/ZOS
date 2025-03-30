<?php

// Połączenie z bazą danych __user__QL
$db = new PDO('pgsql:host=__host__;dbname=__dbname__;user=__user__;password=__passwd__');

// Pobranie danych z bazy
$zapytanie = "SELECT imie || ' ' || nazwisko AS label FROM osoby_kontrolujace WHERE imie || ' ' || nazwisko LIKE :zapytanie";
$wynik = $db->prepare($zapytanie);
$tmp = '%' . $_GET['zapytanie'] . '%';
$wynik->bindValue(':zapytanie', $tmp);
$wynik->execute();

// Zwrócenie danych w formacie JSON
$osoby = [];
while ($row = $wynik->fetch(PDO::FETCH_ASSOC)) {
    $osoby[] = $row;
}

echo json_encode($osoby);
