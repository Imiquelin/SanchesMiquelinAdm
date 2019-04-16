<?php

namespace App\adms\Models\helper;

if (!defined('URL')) {
    header("Location: /");
    exit();
}

/**
 * Description of AdmsEmail
 *
 * @copyright (c) year, Igor Miquelin - IM
 */
class AdmsEmail {

    private $Resultado;
    private $Dados;
    private $Formato;

    function getResultado() {
        return $this->Resultado;
    }

    public function valEmail($Email) {
        $this->Dados = (string) $Email;
        $this->Formato = '/[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9_\.\-]*[a-zA-Z0-9_\.\-]+\.[a-z]{2,4}$/';

        If (preg_match($this->Formato, $this->Dados)) {
            $this->Resultado = true;
        } Else {
            $_SESSION['msg'] = "<div class='alert alert-danger'>Erro: E-mail inválido!</div>";
            $this->Resultado = false;
        }
    }

}
