<?php

// Pobieramy wartość z formularza

$miejscowosc_w_szamba = $_POST["MiejscowoscAdrWlSzamba"];
$kod_w_szamba = $_POST["KodAdrWlSzamba"];

$host = '__host__';
$dbname = '__dbname__';
$user_db = '__user__';
$password_db = '__passwd__';
// Połącz z bazą danych
$dsn = "pgsql:host=$host;dbname=$dbname";

$baza_danych = new PDO($dsn, $user_db, $password_db);

// Pobieramy wartość z formularza

// Wykonujemy zapytanie SQL
// Pobieramy wartość z formularza

// Wykonujemy zapytanie SQL
if ($kod_w_szamba){
	$zapytanie = $baza_danych->prepare("SELECT distinct miejscowosc FROM adresy WHERE miejscowosc like :part and kod_pocztowy=:kod order by miejscowosc");
	$miejscowosc_w_szamba = $miejscowosc_w_szamba.'%';
	$zapytanie->bindParam(':part', $miejscowosc_w_szamba);
	$zapytanie->bindParam(':kod', $kod_w_szamba);
}
else{
	$zapytanie = $baza_danych->prepare("SELECT distinct miejscowosc FROM adresy WHERE miejscowosc like :part order by miejscowosc");
	$miejscowosc_w_szamba = $miejscowosc_w_szamba.'%';
	$zapytanie->bindParam(':part', $miejscowosc_w_szamba);
}

$zapytanie->execute();
$przewidywane_miejscowosci = $zapytanie->fetchAll(PDO::FETCH_ASSOC);
// Sprawdzamy, czy wartość znajduje się w tabeli

if ($przewidywane_miejscowosci){
	$json = json_encode($przewidywane_miejscowosci);
	echo $json;
}
else{
	// Tworzymy tablicę z danymi
	$data = array(
	);
	// Kodowanie danych do formatu JSON
	$jsonData = json_encode($data);

	// Wyświetlanie wyniku
	echo $jsonData;
}
?>

