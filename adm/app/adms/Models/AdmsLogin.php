<?php

namespace App\adms\Models;

if (!defined('URL')) {
    header("Location: /");
    exit();
}

/**
 * Description of AdmsLogin
 *
 * @copyright (c) year, Igor Miquelin - IM
 */
class AdmsLogin {

    private $Dados;
    private $Resultado;

    function getResultado() {
        return $this->Resultado;
    }

    public function acesso(array $Dados) {
        $this->Dados = $Dados;
        $this->validarDados();
        if ($this->Resultado) {
            $validaLogin = new \App\adms\Models\helper\AdmsRead();
            $validaLogin->fullRead("SELECT user.id, user.nome, user.email, user.senha, user.imagem, user.adms_niveis_acesso_id, nivac.ordem ordem_nivac
                FROM adms_usuarios user
                INNER JOIN adms_niveis_acessos nivac ON nivac.id = user.adms_niveis_acesso_id
                WHERE usuario = :usuario
                LIMIT :limit", "usuario={$this->Dados['usuario']}&limit=1");
            $this->Resultado = $validaLogin->getResultado();
            if ($this->Resultado) {
                $this->validarSenha();
            } else {
                $_SESSION['msg'] = "<div class='alert alert-danger'>Erro: Usuário não cadastrado!</div>";
                $this->Resultado = false;
            }
        }
    }

    private function validarDados() {
        $this->Dados = array_map('strip_tags', $this->Dados);
        $this->Dados = array_map('trim', $this->Dados);
        if (in_array('', $this->Dados)) {
            $_SESSION['msg'] = "<div class='alert alert-danger'>Erro: Necessário preencher todos os campos!</div>";
            $this->Resultado = false;
        } else {
            $this->Resultado = TRUE;
        }
    }

    private function validarSenha() {
        if (password_verify($this->Dados['senha'], $this->Resultado[0]['senha'])) {
            $_SESSION['usuarios_id'] = $this->Resultado[0]['id'];
            $_SESSION['usuarios_nome'] = $this->Resultado[0]['nome'];
            $_SESSION['usuarios_email'] = $this->Resultado[0]['email'];
            $_SESSION['usuarios_imagem'] = $this->Resultado[0]['imagem'];
            $_SESSION['adms_niveis_acesso_id'] = $this->Resultado[0]['adms_niveis_acesso_id'];
            $_SESSION['ordem_nivac'] = $this->Resultado[0]['ordem_nivac'];
            $this->Resultado = true;
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'>Erro: Usuário ou senha incorreta!</div>";
            $this->Resultado = false;
        }
    }

    public function cadUser(array $Dados) {
        $this->Dados = $Dados;
        $this->validarDados();
        if ($this->Resultado) {
            $valEmail = new \App\adms\Models\helper\AdmsEmail();
            $valEmail->valEmail($this->Dados['email']);
            if ($valEmail->getResultado()) {
                $this->Dados['senha'] = password_hash($this->Dados['senha'], PASSWORD_DEFAULT);
                $this->Dados['conf_email'] = 2;
                $this->Dados['adms_niveis_acesso_id'] = 5;
                $this->Dados['adms_sits_usuario_id'] = 2;
                $this->Dados['created'] = date('Y-m-d H:i:s');
                $this->inserir();
            } else {
                $this->Resultado = FALSE;
            }
        }
    }

    private function inserir() {
        $cadUser = new \App\adms\Models\helper\AdmsCreate();
        $cadUser->exeCreate("adms_usuarios", $this->Dados);
        if ($cadUser->getResultado()) {
            $_SESSION['msg'] = "<div class='alert alert-success'>Usuário cadastrado com sucesso!</div>";
            $this->Resultado = true;
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'>Erro: Usuário não foi cadastrado com sucesso!</div>";
            $this->Resultado = false;
        }
    }

}
