<?php


namespace App\adms\Controllers;

if (!defined('URL')) {
    header("Location: /");
    exit();
}
/**
 * Description of Usuarios
 *
 * @copyright (c) year, Igor Miquelin - IM
 */
class Usuarios {
    public function listar() {
        echo "Listar Usuários";
    }
}
