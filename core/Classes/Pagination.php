<?php

namespace Itval\core\Classes;

use Itval\core\DAO\Database;

/**
 * Class Pagination Classe qui permet de créer une pagination
 *
 * @package Itval\core\Classes
 * @author  Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
 */
class Pagination
{

    /**
     * Nombre total d'occurences
     *
     * @var int
     */
    public $total;

    /**
     * Nombre d'entités par pages
     *
     * @var int nombre d'entités par page
     */
    public $entityPerPage;

    /**
     * Nombre de pages
     *
     * @var int nombre total de pages
     */
    public $numberOfPages;

    /**
     * Page courante
     *
     * @var int numéro de la page en cours
     */
    public $currentPage;

    /**
     * Première occurence pagination
     *
     * @var int
     */
    public $firstEntry;

    /**
     * Limite nombre page visible pagination
     *
     * @var int
     */
    private $limit;

    /**
     * Pagination constructor.
     *
     * @param $model
     * @param null $conditions
     * @param int $entityPerPage
     */
    public function __construct($model, $conditions = null, $entityPerPage = 10)
    {
        $this->entityPerPage = $entityPerPage;
        $count = Database::getPdo()->query(
            'SELECT COUNT(*) AS total FROM ' . $model
            . $conditions
        )->fetch();
        $this->total = $count['total'];
        $this->numberOfPages = ceil($this->total / $this->entityPerPage);
        $this->currentPage = $this->getActualPage();
        if ($this->numberOfPages > 10) {
            $this->limit = $this->currentPage + 10;
        } else {
            $this->limit = $this->currentPage;
        }
        $this->firstEntry = ($this->currentPage - 1) * $this->entityPerPage;
    }

    /**
     * Retourne la page courante
     *
     * @return int
     */
    private function getActualPage(): int
    {
        if (isset($_GET['p']) && !empty($_GET['p'] && intval($_GET['p'] && intval($_GET['p'] > 0)))) {
            $currentPage = intval(filter_input(INPUT_GET, 'p'));
            if ($currentPage > $this->numberOfPages) {
                $currentPage = $this->numberOfPages;
            }
        } else {
            $currentPage = 1;
        }
        return $currentPage;
    }

    /**
     * Génère la pagination
     *
     * @param  string $route la route qui sera utilisée dans les liens de la pagination
     * @return string
     */
    public function generate(string $route): string
    {
        $disable_previous = '';
        $link_previous = getUrl($route) . '/?p=' . ($this->currentPage - 1);
        $link_first = getUrl($route) . '/?p=1';
        $disable_next = '';
        $link_next = getUrl($route) . '/?p=' . ($this->currentPage + 1);
        $link_last = getUrl($route) . '/?p=' . $this->numberOfPages;
        if ($this->currentPage == 1) {
            $disable_previous = ' class="disabled"';
            $link_previous = '';
            $link_first = '';
        }
        if ($this->currentPage == $this->numberOfPages) {
            $disable_next = ' class="disabled"';
            $link_next = '';
            $link_last = '';
        }
        $pagination = '<div class="center"><ul class="pagination pagination-lg">'
            . '<li' . $disable_previous . '><a href="' . $link_first . '">&laquo; Première page</a></li>'
            . '<li' . $disable_previous . '><a href="' . $link_previous . '">&laquo;</a></li>';
        if ($this->numberOfPages < 5) {
            for ($i = 1; $i <= $this->numberOfPages; $i++) {
                if ($i == $this->currentPage) {
                    $pagination .= '<li class="disabled"><a>' . $i . '</a></li>';
                } else {
                    $pagination .= '<li><a href="' . getUrl($route) . '/?p=' . $i . '">' . $i . '</a></li>';
                }
            }
        } elseif ($this->currentPage <= $this->numberOfPages && $this->currentPage >= ($this->numberOfPages - 3)) {
            for ($i = $this->numberOfPages - 4; $i <= $this->numberOfPages; $i++) {
                if ($i == $this->currentPage) {
                    $pagination .= '<li class="disabled"><a>' . $i . '</a></li>';
                } else {
                    $pagination .= '<li><a href="' . getUrl($route) . '/?p=' . $i . '">' . $i . '</a></li>';
                }
            }
        } else {
            for ($i = $this->currentPage - 2; $i <= $this->currentPage + 2; $i++) {
                if ($i == $this->currentPage) {
                    $pagination .= '<li class="disabled"><a>' . $i . '</a></li>';
                } else {
                    $pagination .= '<li><a href="' . getUrl($route) . '/?p=' . $i . '">' . $i . '</a></li>';
                }
            }
        }
        $pagination .= '<li' . $disable_next . '><a href="' . $link_next . '">&raquo;</a></li>'
            . '<li' . $disable_next . '><a href="' . $link_last . '">Dernière page &raquo;</a></li>'
            . '</ul></div>';
        return $pagination;
    }
}
