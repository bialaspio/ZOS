<?php

$partKodPocz = $_POST["KodAdrWlSzamba"];

$host = '__host__';
$dbname = '__dbname__';
$user_db = '__user__';
$password_db = '__passwd__';
// Połącz z bazą danych
$dsn = "pgsql:host=$host;dbname=$dbname";

$baza_danych = new PDO($dsn, $user_db, $password_db);

// Pobieramy wartość z formularza

// Wykonujemy zapytanie SQL
$zapytanie = $baza_danych->prepare("select distinct kod_pocztowy from adresy o WHERE kod_pocztowy like :part");
$partKodPocz = $partKodPocz.'%';
$zapytanie->bindParam(':part', $partKodPocz);
$zapytanie->execute();
$przewidywane_kody = $zapytanie->fetchAll(PDO::FETCH_ASSOC);
// Sprawdzamy, czy wartość znajduje się w tabeli

if ($przewidywane_kody){
	$json = json_encode($przewidywane_kody);
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

