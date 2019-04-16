<?php
if (!defined('URL')) {
    header("Location: /");
    exit();
}

echo "<br><br> View Home <br><br>";
echo "<a href='".URLADM. "login/logout'> Sair </a><br>";