<?php

namespace App\adms\Controllers;

if (!defined('URL')) {
    header("Location: /");
    exit();
}

/**
 * Description of Home
 *
 * @copyright (c) year, Igor Miquelin - IM
 */
class Home {

    private $Dados;

    public function index() {


        $carregarView = new \Core\ConfigView("adms/Views/home/home", $this->Dados);
        $carregarView->renderizar();
    }

}
