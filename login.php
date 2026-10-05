<head>
<title></title>
<style>.erro(color:red;)</style>
</head>
<body>
    <p class="erro">
        <?php
        if (isset($_GET["erro"])){
            if($_GET["erro"]==1){
                echo "login e senha invalidos";
            }
            if ($_GET["erro"]==2){
                echo"efetue login para acessar essa pagina";
            }
        }
?>

<from action="vereficalogin.pho"method="post">
    <label>Usuario:</label><br/>
    <input type="text"name="Usuario"/><br/>
    <label>Senha:</label><br/>
    <input type="password"name="Senha"/><br/>
    <input type="checkbox"name="Lembrar"value="s"/>Lembrar senha<br/>
    <input type="submit" value="Efetuar login"/>
</form>