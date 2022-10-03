<?php

require_once "clases/guarderia.php";

use Animalitos\Mascota;
use Negocios\Guarderia;


$mascotaUno = new Mascota("Tyson", "bulldog");
$mascotaDos = new Mascota("Tyson", "pug",5);

//muestro las mascotas
echo Mascota::mostrar($mascotaUno);
echo "<br>";
echo $mascotaDos->toString();

echo "<br>";
//comparo
echo $mascotaUno->equals($mascotaDos);

echo "<br><br><br>";

//creo nuevas mascotas
$mascotaTres = new Mascota("Pinki", "bulldog", 10);
$mascotaCuatro = new Mascota("Pinki", "bulldog", 2);

echo Mascota::mostrar($mascotaCuatro);
echo "<br>";
echo $mascotaTres->toString();

echo "<br>";
echo $mascotaTres->equals($mascotaCuatro);


echo "<br><br><br>";

//creo mi guarderia
$miGuarderia = new Guarderia("La guardería de Pancho");

$miGuarderia->add($mascotaUno);
$miGuarderia->add($mascotaDos);
$miGuarderia->add($mascotaTres);
$miGuarderia->add($mascotaCuatro);


var_dump($miGuarderia);
echo "<br><br><br>";

echo $miGuarderia->toString();



