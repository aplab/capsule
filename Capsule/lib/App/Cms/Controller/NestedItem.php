<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 08.03.2014 3:32:18 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Controller;

use Capsule\Ui\Toolbar\Button;
use Capsule\Ui\DataGrid\DataGrid;
use Capsule\Ui\DialogWindow\DialogWindow;
use App\Cms\Ui\DialogWindow\View;
use Capsule\I18n\I18n;
use Capsule\Common\Path;
use Capsule\Capsule;
use Capsule\Superglobals\Post;
use Capsule\User\Env;
use Capsule\Core\Fn;
use App\Cms\Ui\Dialog\Dialog;
/**
 * NestedItem.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class NestedItem extends ReferenceController
{
    protected $moduleClass = 'Capsule\\Unit\\Nested\\Item';
    
    /**
     * Значение фильтра по контейнеру
     *
     * @var string
     */
    protected $filterByContainer;
    
    /**
     * Some variants
     *
     * @var string
     */
    const ALL = 'all', BOUND = 'bound', WITHOUT_BINDING = 'without_binding';
    
    /**
     * Ключ суперглобального массива фильтра по контейнеру
     *
     * @var string
     */
    const FILTER_BY_CONTAINER = 'containerId';
    
    /**
     * Варианты значений фильтра
     *
     * @var array
     */
    protected $filterVariants = array(
    	self::ALL => array(
    	    'value' => self::ALL,
            'text' => 'All',
            'selected'=> false
        ),
        self::BOUND => array(
            'value' => self::BOUND,
            'text' => 'Bound',
            'selected' => false
        ),
        self::WITHOUT_BINDING => array(
            'value' => self::WITHOUT_BINDING,
            'text' => 'Without binding',
            'selected' => false
        )
    );
    
    protected function listItems() {
        $filter = $this->app->urlFilter;
        
        $module_class = $this->moduleClass;
        $module_config = $module_class::config();
        $container_class = Fn::create_classname($module_config->container);
        $variants = array_replace($this->filterVariants, $container_class::optionsDataList());
        $this->filterByContainer();
        $this->filterByContainer = Env::getInstance()->get($this->filterByContainerKey());
        
//         $dialog = new DialogWindow('filter-by-container-window');
//         $dialog->hidden = true;
//         $dialog->content = include(new Path(Capsule::getInstance()->systemRoot, $this->app->config->templates, 'NestedItemFilter.php'));
//         $dial_view = new View($dialog);
//         $dialog->caption = I18n::_('Filter');
        
        new Dialog(array(
            'title' => I18n::_('Filter'),
            'instanceName' => 'filter-by-container-window',
            'content' => include(new Path(Capsule::getInstance()->systemRoot, $this->app->config->templates, 'NestedItemFilter.php')),
            'appendTo' => 'capsule-cms-wrapper',
            'hidden' => false,
            'minWidth' => 320,
            'minHeight' => 240
        ));
        
        $toolbar = $this->app->registry->toolbar;
        $button = new Button;
        $toolbar->add($button);
        $button->caption = 'New';
        $button->url = $filter($this->mod, 'add');
        $button->icon = $this->app->config->icons->cms . '/document--plus.png';
    
        $button = new Button;
        $toolbar->add($button);
        $button->caption = 'Delete selected';
        $button->icon = $this->app->config->icons->cms . '/cross-script.png';
        $button->action = 'CapsuleUiDataGrid.getInstance("capsule-ui-datagrid").del()';
        
        $button = new Button;
        $toolbar->add($button);
        $button->caption = I18n::_($variants[$this->filterByContainer]['text']);
        $button->icon = $this->app->config->icons->cms . '/funnel.png';
        $button->action = 'CapsuleUiDialog.getInstance("filter-by-container-window").showCenter()';
    
        $c = $this->moduleClass;
        $p = $this->pagination();
        $items = array();
        if (self::ALL === $this->filterByContainer) {
            $items = $c::page($p->currentPage, $p->itemsPerPage);
        } elseif (self::BOUND === $this->filterByContainer) {
            $items = $c::pageWithContainer($p->currentPage, $p->itemsPerPage);
        } elseif (self::WITHOUT_BINDING === $this->filterByContainer) {
            $items = $c::pageWithoutContainer($p->currentPage, $p->itemsPerPage);
        } else {
            $items = $c::pageByContainer($this->filterByContainer, $p->currentPage, $p->itemsPerPage);
        }
        $data_grid = new DataGrid('capsule-ui-datagrid', $items);
        $data_grid->baseUrl = $filter($this->mod);
        $data_grid->p = $p;
        $this->ui->workplace->append(new \App\Cms\Ui\DataGrid\View($data_grid));
        
//         $this->ui->wrapper->append($dial_view);
    }
    
    /**
     * Инициализация переменной окружения - выбор текущего контейнера
     *
     * @param void
     * @return void
     */
    protected function filterByContainer() {
        $module_class = $this->moduleClass;
        $module_config = $module_class::config();
        $container_class = Fn::create_classname($module_config->container);
        $variants = array_replace($this->filterVariants, $container_class::optionsDataList());
        $post = Post::getInstance();
        $env = Env::getInstance();
        $key = $this->filterByContainerKey();
        $default = key($variants);
        if (isset($post->{self::FILTER_BY_CONTAINER})) {
            $filter = $post->{self::FILTER_BY_CONTAINER};
            if (array_key_exists($filter, $variants)) {
                $env->set($key, $filter);
            }
        }
        if (!array_key_exists($env->get($key), $variants)) {
            $env->set($key, $default);
        }
    }
    
    /**
     * Возвращает ключ для хранения текущего контейнера в Env - переменные окружения пользователя
     *
     * @param void
     * @return string
     */
    protected function filterByContainerKey() {
        return Fn::concat_ws('::', $this->moduleClass, self::FILTER_BY_CONTAINER);
    }
    
    /**
     * (non-PHPdoc)
     * @see \App\Cms\Controller\ReferenceController::pages()
     */
    protected function pages($current_items_per_page) {
        $class = $this->moduleClass;
        if (self::ALL === $this->filterByContainer) {
            return $class::pages($current_items_per_page);
        }
        if (self::BOUND === $this->filterByContainer) {
            return $class::pagesWithContainer($current_items_per_page);
        }
        if (self::WITHOUT_BINDING === $this->filterByContainer) {
            return $class::pagesWithoutContainer($current_items_per_page);
        }
        return $class::pagesByContainer($this->filterByContainer, $current_items_per_page);
    }
}