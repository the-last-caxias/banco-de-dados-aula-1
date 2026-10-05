<?php
if (!isset($_COOKIE["nome"])) {
    echo "o cookie nao existe!";
} else {
    echo "ola " . $_COOKIE["nome"] . "!";
}
?>