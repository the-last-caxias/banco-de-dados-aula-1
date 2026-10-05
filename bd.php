<?php
session_start();

$pagina = $_GET["pagina"] ?? "login";
$erroLogin = (int) ($_GET["erro"] ?? 0);
$mensagem = "";
$quantidadeContatos = null;

if ($pagina === "verificar-login") {
    $login = $_POST["usuario"] ?? "";
    $senha = $_POST["senha"] ?? "";

    if ($login === "maria" && $senha === "12345") {
        session_regenerate_id(true);
        $_SESSION["usuario"] = $login;
        $_SESSION["autenticado"] = true;
        header("Location: bd.php?pagina=area-restrita");
        exit();
    }

    header("Location: bd.php?pagina=login&erro=1");
    exit();
}

if ($pagina === "logoff") {
    $_SESSION = [];
    session_destroy();
    header("Location: bd.php?pagina=login");
    exit();
}

if ($pagina === "area-restrita" && (!isset($_SESSION["autenticado"]) || !isset($_SESSION["usuario"]))) {
    header("Location: bd.php?pagina=login&erro=2");
    exit();
}

if ($pagina === "criasessao") {
    $_SESSION["usuario"] = "login";
    $_SESSION["nome"] = "joao";
    $mensagem = "Sessão criada.";
}

if ($pagina === "visitas") {
    $visitas = isset($_COOKIE["qtdvisitas"]) ? (int) $_COOKIE["qtdvisitas"] + 1 : 1;
    setcookie("qtdvisitas", (string) $visitas, time() + 60, "/");
    $mensagem = $visitas === 1
        ? "Esse é o seu primeiro acesso a esta página!"
        : "Você visitou esta página " . $visitas . " vez(es).";
}

if ($pagina === "contato" || $pagina === "cadastro" || $pagina === "listar-contatos") {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $con = mysqli_connect("localhost", "root", "123456", "agenda");
        mysqli_set_charset($con, "utf8mb4");

        if ($pagina === "contato") {
            $nome = "Maria José";
            $telefone = "1234-5678";
            $endereco_id = 1;

            $stmt = mysqli_prepare($con, "INSERT INTO contato (nome, telefone, endereco_id) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssi", $nome, $telefone, $endereco_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $mensagem = "Contato incluído com sucesso!";
        } elseif ($pagina === "listar-contatos") {
            $resultado = mysqli_query($con, "SELECT * FROM contato");
            $quantidadeContatos = mysqli_num_rows($resultado);
            mysqli_free_result($resultado);
            $mensagem = "Registros selecionados com sucesso.";
        } else {
            $rua = "Rua das Flores";
            $cidade = "Natal";
            $cep = "59000-000";
            $nome = "Maria José";
            $telefone = "1234-5678";

            mysqli_begin_transaction($con);
            $stmtEndereco = mysqli_prepare($con, "INSERT INTO endereco (rua, cidade, cep) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmtEndereco, "sss", $rua, $cidade, $cep);
            mysqli_stmt_execute($stmtEndereco);
            $endereco_id = mysqli_insert_id($con);
            mysqli_stmt_close($stmtEndereco);

            $stmtContato = mysqli_prepare($con, "INSERT INTO contato (nome, telefone, endereco_id) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmtContato, "ssi", $nome, $telefone, $endereco_id);
            mysqli_stmt_execute($stmtContato);
            mysqli_stmt_close($stmtContato);
            mysqli_commit($con);
            $mensagem = "Endereço e contato incluídos com sucesso!";
        }

        mysqli_close($con);
    } catch (Throwable $erro) {
        if (isset($con) && $con instanceof mysqli) {
            if ($pagina === "cadastro") {
                mysqli_rollback($con);
            }
            mysqli_close($con);
        }
        $mensagem = "Erro: " . $erro->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Agenda</title>
    <style>
        .erro { color: red; }
    </style>
</head>
<body>
    <nav>
        <a href="bd.php?pagina=login">Login</a> |
        <a href="bd.php?pagina=area-restrita">Área restrita</a> |
        <a href="bd.php?pagina=contato">Incluir contato</a> |
        <a href="bd.php?pagina=listar-contatos">Listar contatos</a> |
        <a href="bd.php?pagina=cadastro">Incluir endereço e contato</a> |
        <a href="bd.php?pagina=visitas">Contar visitas</a> |
        <a href="bd.php?pagina=criasessao">Criar sessão</a> |
        <a href="bd.php?pagina=logoff">Sair</a>
    </nav>

    <?php if ($pagina === "login"): ?>
        <?php if ($erroLogin === 1): ?>
            <p class="erro">Login e senha inválidos.</p>
        <?php elseif ($erroLogin === 2): ?>
            <p class="erro">Efetue login para acessar esta página.</p>
        <?php endif; ?>

        <form action="bd.php?pagina=verificar-login" method="post">
            <label>Usuário:</label><br/>
            <input type="text" name="usuario"><br/>
            <label>Senha:</label><br/>
            <input type="password" name="senha"><br/>
            <input type="checkbox" name="lembrar" value="s"> Lembrar senha<br/>
            <input type="submit" value="Efetuar login">
        </form>
    <?php elseif ($pagina === "area-restrita"): ?>
        <p>Olá <?php echo htmlspecialchars($_SESSION["usuario"], ENT_QUOTES, "UTF-8"); ?>! Seja bem-vindo!</p>
    <?php elseif ($mensagem !== ""): ?>
        <p><?php echo htmlspecialchars($mensagem, ENT_QUOTES, "UTF-8"); ?></p>
        <?php if ($quantidadeContatos !== null): ?>
            <p>Quantidade de registros retornados: <?php echo $quantidadeContatos; ?></p>
        <?php endif; ?>
    <?php else: ?>
        <p>Página não encontrada.</p>
    <?php endif; ?>
</body>
</html>
