<?php
session_Start();
if (isset($_SESSION["autenticado"])) && isset($_SESSION["usuario"]){
    unset($_SESSION["autenticado"]);
    unset($_SESSION["usuario"]);
}
header("location:login.php");