<?php

require_once 'clase_studenti.php';

try {
    $nume = $_POST['nume'];
    $prenume = $_POST['prenume'];
    $idnp = $_POST['idnp'];
    $dataNastere = $_POST['dataNastere'];
    $medii = array_map('trim', explode(',', $_POST['medii']));
    $grupa = $_POST['grupa'];
    $absenteMot = $_POST['absenteMot'];
    $absenteNemot = $_POST['absenteNemot'];

    if (count($medii) != 8) {
        throw new Exception("Trebuie să introduceți exact 8 medii.");
    }

    $student = new Student($nume, $prenume, $idnp, $dataNastere, $medii, $grupa, $absenteMot, $absenteNemot);
    adaugareStudent($student);

    echo "Student adăugat cu succes!";
} catch (Exception $e) {
    echo "Eroare la adăugarea studentului: " . $e->getMessage();
}

?>