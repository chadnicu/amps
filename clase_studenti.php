<?php
function curatareFisierStudenti($numeFisier = "studenti.in")
{
    if (file_exists($numeFisier)) {
        $continut = file_get_contents($numeFisier);
        $linii = explode("\n", $continut);
        $linii = array_filter($linii, 'trim'); // Elimină liniile goale
        $continutCurat = implode("\n", $linii);
        file_put_contents($numeFisier, $continutCurat);
    }
}
curatareFisierStudenti();

class Persoana
{
    protected $nume;
    protected $prenume;
    protected $idnp;
    protected $dataNastere;

    public function __construct($nume, $prenume, $idnp, $dataNastere)
    {
        $this->nume = $nume;
        $this->prenume = $prenume;
        $this->idnp = $idnp;
        $this->dataNastere = $dataNastere;
    }

    public function toString()
    {
        return "Nume: {$this->nume}, Prenume: {$this->prenume}, IDNP: {$this->idnp}, Data nașterii: {$this->dataNastere}";
    }

    public function getNume()
    {
        return $this->nume;
    }
    public function getPrenume()
    {
        return $this->prenume;
    }
    public function getIdnp()
    {
        return $this->idnp;
    }
    public function getDataNastere()
    {
        return $this->dataNastere;
    }
}

class Student extends Persoana
{
    private $medii = [];
    private $grupa;
    private $absenteMot;
    private $absenteNemot;
    private $bursa;

    public function __construct($nume, $prenume, $idnp, $dataNastere, $medii, $grupa, $absenteMot, $absenteNemot)
    {
        parent::__construct($nume, $prenume, $idnp, $dataNastere);
        $this->medii = $medii;
        $this->grupa = $grupa;
        $this->absenteMot = $absenteMot;
        $this->absenteNemot = $absenteNemot;
        $this->bursa = 0;
    }

    public function toString()
    {
        $parentString = parent::toString();
        $mediiString = implode(", ", $this->medii);
        return "$parentString, Grupa: {$this->grupa}, Absențe motivate: {$this->absenteMot}, Absențe nemotivate: {$this->absenteNemot}, Bursă: {$this->bursa}, Medii: $mediiString";
    }

    public function media()
    {
        return array_sum($this->medii) / count($this->medii);
    }

    public function areRestante()
    {
        return in_array(4, $this->medii);
    }

    public function setBursa($bursa)
    {
        $this->bursa = $bursa;
    }

    public function getMedii()
    {
        return $this->medii;
    }
    public function getGrupa()
    {
        return $this->grupa;
    }
    public function getAbsenteMot()
    {
        return $this->absenteMot;
    }
    public function getAbsenteNemot()
    {
        return $this->absenteNemot;
    }
}

function citireStudentiDinFisier($numeFisier)
{
    $studenti = [];
    $linii = file($numeFisier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linii as $linie) {
        $date = explode(",", $linie);
        if (count($date) >= 16) {
            $medii = array_slice($date, 4, 8);
            $studenti[] = new Student($date[0], $date[1], $date[2], $date[3], $medii, $date[12], $date[13], $date[14]);
        } else {
            error_log("Linie invalidă în fișierul studenti.in: " . $linie);
        }
    }
    return $studenti;
}

function afisareStudenti($studenti)
{
    echo "<h2>Lista studenților:</h2>";
    echo "<ul>";
    foreach ($studenti as $student) {
        echo "<li>" . $student->toString() . "</li>";
    }
    echo "</ul>";
}

function afisareStudentiCuRestante($studenti)
{
    echo "<h2>Studenți cu restanțe:</h2>";
    echo "<ul>";
    foreach ($studenti as $student) {
        if ($student->areRestante()) {
            echo "<li>" . $student->toString() . "</li>";
        }
    }
    echo "</ul>";
}

function adaugareStudent($student)
{
    $linie = implode(",", [
        $student->getNume(),
        $student->getPrenume(),
        $student->getIdnp(),
        $student->getDataNastere(),
        implode(",", $student->getMedii()),
        $student->getGrupa(),
        $student->getAbsenteMot(),
        $student->getAbsenteNemot()
    ]);

    if (file_exists("studenti.in") && filesize("studenti.in") > 0) {
        $linie = PHP_EOL . $linie;
    }

    file_put_contents("studenti.in", $linie, FILE_APPEND);
}

function calculeazaBurse(&$studenti)
{
    $studentiEligibili = array_filter($studenti, function ($student) {
        return !$student->areRestante();
    });

    usort($studentiEligibili, function ($a, $b) {
        return $b->media() <=> $a->media();
    });

    $nrStudenti = count($studentiEligibili);

    for ($i = 0; $i < $nrStudenti; $i++) {
        if ($i === 0) {
            $studentiEligibili[$i]->setBursa(1000);
        } elseif ($i === 1) {
            $studentiEligibili[$i]->setBursa(900);
        } elseif ($i >= 2 && $i <= 7) {
            $studentiEligibili[$i]->setBursa(800);
        } else {
            $studentiEligibili[$i]->setBursa(0);
        }
    }
}

?>