<?php
$login=$_POST["usuario"];
$senha=$_POST["senha"];
if(login=="maria"&& $senha=="12345"){
    session_start();
    $_SESSION["usuario"]=login;
    $_SESSION["autenticado"]=true;
    header("Location: arearestrita.php");
}else{
    header("location:login.php?erro=1");
}
?>