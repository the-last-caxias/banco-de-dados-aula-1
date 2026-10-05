<?php
$tempomaximo=time()+60;
if (isset($_COOKIE["qtdvisitas"])){
    echo "você visitou essa pagina ".$_COOKIE["qtdvisitas"]." vez(es).";
    setcookie("qtdvisitas",$_COOKIE["qtdvisitas"]+1,tempomaximo);
}else{
    echo "esse é o seu primeiro acesso a esta pagina!";
    setcookie("qtdvisitas",1,$tempomaximo);
}
?>