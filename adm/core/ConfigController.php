<?php

namespace Core;

/**
 * Description of ConfigController
 *
 * @copyright (c) year, Igor Miquelin - IM
 */
class ConfigController {

    private $Url;
    private $UrlConjunto;
    private $UrlController;
    private $UrlParametro;
    private $UrlMetodo;
    private $Classe;
    private $Paginas;
    private static $Format;

    public function __construct() {

        if (!empty(filter_input(INPUT_GET, 'url', FILTER_DEFAULT))) {

            $this->Url = filter_input(INPUT_GET, 'url', FILTER_DEFAULT);
            $this->limparUrl();
            $this->UrlConjunto = explode("/", $this->Url);

            if (isset($this->UrlConjunto[0])) {
                $this->UrlController = $this->slugController($this->UrlConjunto[0]);
            } else {
                $this->UrlController = $this->slugController(CONTROLER);
            }
            if (isset($this->UrlConjunto[1])) {
                $this->UrlMetodo = $this->slugMetodo($this->UrlConjunto[1]);
            } else {
                $this->UrlMetodo = $this->slugMetodo(METODO);
            }
            if (isset($this->UrlConjunto[2])) {
                $this->UrlParametro = $this->UrlConjunto[2];
            } else {
                $this->UrlParametro = NULL;
            }
            //echo "URL: {$this->Url}<br>";
            //echo "Controller: {$this->UrlController} <br>";
            //echo "Controller: {$this->UrlMetodo} <br>";
            //echo "Parametro: {$this->UrlParametro} <br>";
        } else {
            $this->UrlController = $this->slugController(CONTROLER);
            $this->UrlMetodo = $this->slugMetodo(METODO);
            $this->UrlParametro = NULL;
        }
    }

    private function limparUrl() {
        //Elimina as Tags
        $this->Url = strip_tags($this->Url);
        //Elimina espaços em branco
        $this->Url = trim($this->Url);
        //Elimiina a barra no final da URL
        $this->Url = rtrim($this->Url, "/");
        //
        self::$Format = array();
        self::$Format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]?;:.,\\\'<>°ºª ';
        self::$Format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr--------------------------------';
        $this->Url = strtr(utf8_decode($this->Url), utf8_decode(self::$Format['a']), self::$Format['b']);
    }

    private function slugController($SlugController) {
        $UrlController = str_replace(" ", "", ucwords(implode(" ", explode("-", strtolower($SlugController)))));

        return $UrlController;
    }

    private function slugMetodo($SlugMetodo) {
        $UrlController = str_replace(" ", "", ucwords(implode(" ", explode("-", strtolower($SlugMetodo)))));

        return lcfirst($UrlController);
    }

    public function carregar() {

        $listarPg = new \App\adms\Models\AdmsPaginas();
        $this->Paginas = $listarPg->listarPaginas($this->UrlController, $this->UrlMetodo);

        if ($this->Paginas) {
            extract($this->Paginas[0]);
            $this->Classe = "\\App\\{$tipo_tpg}\\Controllers\\" . $this->UrlController;
            if (class_exists($this->Classe)) {
                $this->carregarMetodo();
            } else {
                $this->UrlController = $this->slugController(CONTROLER);
                $this->UrlMetodo = $this->slugMetodo(METODO);
                $this->carregar();
            }
        } else {
            $this->UrlController = $this->slugController('Login');
            $this->UrlMetodo = $this->slugMetodo('acesso');
            $this->carregar();
        }
    }

    private function carregarMetodo() {
        $classeCarregar = new $this->Classe;
        if (method_exists($classeCarregar, $this->UrlMetodo)) {
            if ($this->UrlParametro !== null) {
                $classeCarregar->{$this->UrlMetodo}($this->UrlParametro);
            } else {
                $classeCarregar->{$this->UrlMetodo}();
            }
        } else {
            $this->UrlController = $this->slugController(CONTROLER);
            $this->UrlMetodo = $this->slugMetodo(METODO);
            $this->carregar();
        }
    }

}
