<?php

namespace app\Machine\Engine\Pagination;

use app\Machine\Engine\Cylinders\Read;
use app\Machine\Engine\Support\Session;
use app\Machine\Request;

class Paginator {

    /** DEFINE O PAGER */
    private $Page;
    private $Limit;
    private $Offset;

    /** REALIZA A LEITURA */
    private string $model;

    /** DEFINE O PAGINATOR */
    private $Rows;
    private $Link;
    private $MaxLinks;
    private $First;
    private $Last;

    /** RENDERIZA O PAGINATOR */
    private $Paginator;

    /**
     * <b>Iniciar Paginação:</b> Defina o link onde a paginação será recuperada. Você ainda pode mudar os textos
     * do primeiro e último link de navegação e a quantidade de links exibidos (opcional)
     * @param STRING $Link = Ex: index.php?pagina&page=
     * @param STRING $First = Texto do link (Primeira Página)
     * @param STRING $Last = Texto do link (Última Página)
     * @param STRING $MaxLinks = Quantidade de links (5)
     */
    function __construct($Link = null, $First = null, $Last = null, $MaxLinks = null) {
        $this->Link = (string) $Link ? $Link : $_ENV['APP_URL'].(new Request())->getPath().'?p=';
        $this->First = ( (string) $First ? $First : 'Primeira Página' );
        $this->Last = ( (string) $Last ? $Last : 'Última Página' );
        $this->MaxLinks = ( (int) $MaxLinks ? $MaxLinks : 5);
    }

    /**
     * <b>Executar Pager:</b> Informe o índice da URL que vai recuperar a navegação e o limite de resultados por página.
     * Você devere usar LIMIT getLimit() e OFFSET getOffset() na query que deseja paginar. A página atual está em getPage()
     * @param INT $Page = Recupere a página na URL
     * @param INT $Limit = Defina o LIMIT da consulta
     */
    public function ExePager($Page, $Limit) {
        $this->Page = ( (int) $Page ? $Page : 1 );
        $this->Limit = (int) $Limit;
        $this->Offset = ($this->Page * $this->Limit) - $this->Limit;
    }

    public function simplePager($Limit) {

        $Page = filter_input(INPUT_GET, VAR_NAME ?? '', FILTER_VALIDATE_INT);
        $this->Page = ( (int) $Page ? $Page : 1 );
        $this->Limit = (int) $Limit;
        $this->Offset = ($this->Page * $this->Limit) - $this->Limit;
    }

    /**
     * <b>Retornar:</b> Caso informado uma page com número maior que os resultados, este método navega a paginação
     * em retorno até a página com resultados!
     * @return LOCATION = Retorna a página
     */
    public function ReturnPage() {
        if ($this->Page > 1):
            $nPage = $this->Page - 1;
            header("Location: {$this->Link}{$nPage}");
        endif;
    }

    /**
     * <b>Obter Página:</b> Retorna o número da página atualmente em foco pela URL. Pode ser usada para validar
     * a navegação da paginação!
     * @return INT = Retorna a página atual
     */
    public function getPage() {
        return $this->Page;
    }

    /**
     * <b>Limite por Página:</b> Retorna o limite de resultados por página da paginação. Deve ser usada na SQL que obtém
     * os resultados. Ex: LIMIT = getLimit();
     * @return INT = Limite de resultados
     */
    public function getLimit() {
        return $this->Limit;
    }

    /**
     * <b>Offset por Página:</b> Retorna o offset de resultados por página da paginação. Deve ser usada na SQL que obtém
     * os resultado. Ex: OFFSET = getLimit();
     * @return INT = Offset de resultados
     */
    public function getOffset() {
        return $this->Offset;
    }

    /**
     * <b>Executar Paginação:</b> Cria o menu de navegação de paginação dentro de uma lista não ordenada com a class paginator.
     * Informe o nome da tabela e condições caso exista. O resto é feito pelo método. Execute um <b>echo getPaginator();</b>
     * para exibir a paginação na view.
     * @param STRING $Table = Nome da tabela
     */
    public function ExePaginator(string $Table) {
        $this->model = $Table;
        $this->getFullPaginator();
    }

    public function simplePaginator(string $Table) {
        $this->model = $Table;
        $this->getSimplePaginator();
    }

    /**
     * <b>Exibir Paginação:</b> Retorna os links para a paginação de resultados. Deve ser usada com um echo para exibição.
     * Para formatar as classes são: ul.paginator, li a e li .active.
     * @return HTML = Paginação de resultados
     */
    public function getPaginator() {
        return $this->Paginator;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    //Cria a paginação de resultados
    private function getSimplePaginator() {
        $read = new Read;
        $read->all($this->model);
        $this->Rows = $read->getRowCount();

        if ($this->Rows > $this->Limit):

            $TotalPage = ceil($this->Rows / $this->Limit);
            $disabledP = '';
            $disabledN = '';

            $previousPage = ($this->Page - 1);
            if ($previousPage == 0){ $disabledP = 'disabled'; $previousPage = 1;}

            $this->Paginator = "<ul class=\"pagination\">";
            $this->Paginator .= "<li class=\"page-item $disabledP\"><a class=\"page-link\" title=\"{$this->First}\" href=\"{$this->Link}{$previousPage}\">{$this->First}</a></li>";

            $nextPage = ($this->Page + 1);
            if ($nextPage > $TotalPage){ $disabledN = 'disabled'; $nextPage = $TotalPage;}

            $this->Paginator .= "<li class=\"page-item $disabledN\"><a class=\"page-link\" title=\"{$this->Last}\" href=\"{$this->Link}{$nextPage}\">{$this->Last}</a></li>";
            $this->Paginator .= "</ul>";
        endif;

        Session::$session->destroy('linkPage');
        Session::$session->set('linkPage', $this->Paginator );
    }

    private function getFullPaginator() {
        $read = new Read;
        $read->all($this->model);
        $this->Rows = $read->getRowCount();

        if ($this->Rows > $this->Limit):

            $TotalPage = ceil($this->Rows / $this->Limit);
            $MaxLinks = $this->MaxLinks;

            $this->Paginator = "<ul class=\"pagination\">";
            $this->Paginator .= "<li class=\"page-item\"><a class=\"page-link\" title=\"{$this->First}\" href=\"{$this->Link}1\">{$this->First}</a></li>";

            for ($iPag = $this->Page - $MaxLinks; $iPag <= $this->Page - 1; $iPag ++):
                if ($iPag >= 1):
                    $this->Paginator .= "<li class=\"page-item\"><a class=\"page-link\" title=\"Página {$iPag}\" href=\"{$this->Link}{$iPag}\">{$iPag}</a></li>";
                endif;
            endfor;

            $this->Paginator .= "<li class=\"page-item active\"><a class=\"page-link\" href=\"#\">{$this->Page}</a></li>";

            for ($dPag = $this->Page + 1; $dPag <= $this->Page + $MaxLinks; $dPag ++):
                if ($dPag <= $TotalPage):
                    $this->Paginator .= "<li class=\"page-item\"><a class=\"page-link\" title=\"Página {$dPag}\" href=\"{$this->Link}{$dPag}\">{$dPag}</a></li>";
                endif;
            endfor;

            $this->Paginator .= "<li class=\"page-item\"><a class=\"page-link\" title=\"{$this->Last}\" href=\"{$this->Link}{$TotalPage}\">{$this->Last}</a></li>";
            $this->Paginator .= "</ul>";
        endif;

        Session::$session->destroy('linkPage');
        Session::$session->set('linkPage', $this->Paginator );
    }

}
