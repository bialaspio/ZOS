<?php

// Pobieramy wartość z formularza

$miejscowosc_w_szamba = $_POST["MiejscowoscAdrSzamba"];

$host = '__host__';
$dbname = '__dbname__';
$user_db = '__user__';
$password_db = '__passwd__';
// Połącz z bazą danych
$dsn = "pgsql:host=$host;dbname=$dbname";

$baza_danych = new PDO($dsn, $user_db, $password_db);

// Pobieramy wartość z formularza

// Wykonujemy zapytanie SQL
$zapytanie = $baza_danych->prepare("SELECT distinct miejscowosc FROM adresy WHERE miejscowosc like :part order by miejscowosc");
$miejscowosc_w_szamba = $miejscowosc_w_szamba.'%';
$zapytanie->bindParam(':part', $miejscowosc_w_szamba);
$zapytanie->execute();
$przewidywane_miejscowosci = $zapytanie->fetchAll(PDO::FETCH_ASSOC);
// Sprawdzamy, czy wartość znajduje się w tabeli

if ($przewidywane_miejscowosci){
	$json = json_encode($przewidywane_miejscowosci);
	echo $json;
}
else{
	echo 'nic';
}
?>

