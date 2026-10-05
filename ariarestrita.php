<doctype html>
    <?php
    session_start();
    if(!isset$_SESSION["autenticado"])|| !isset($_SESSION["usuario"]){
        header("location:login.php?erro=2");
    }
    ?>
    <html>
        <head>
            <meta charset="UTF-8">
            <title></title>
</head>
<body><p>
    <?php
    echo"ola".$$_SESSION["usuario"]."! Seja bem-vindo!<br/>";
    ?>
    <a href="logoff.php">[sair]</a>
</p>
</body>