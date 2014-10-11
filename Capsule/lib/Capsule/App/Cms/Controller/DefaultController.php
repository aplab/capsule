<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 10.05.2014 9:08:07 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\App\Cms\Controller;

use Capsule\App\Cms\Controller\AbstractController;
use Capsule\Ui\DropdownMenu\SubPunct;
use Capsule\Ui\DropdownMenu\Punct;
use Capsule\Ui\DropdownMenu\Menu;
use Capsule\App\Cms\Ui\Ui;
use Capsule\App\Cms\Ui\Script;
use Capsule\App\Cms\Ui\Stylesheet;
use Capsule\Ui\Toolbar\Toolbar;
use Capsule\App\Cms\Ui\Section;
use Capsule\App\Cms\Ui\MainMenu\View;
use Capsule\Capsule;
use Capsule\Ui\DialogWindow\DialogWindow;
use Capsule\Common\Path;
use Capsule\App\Cms\Ui\Dialog\Dialog;
/**
 * DefaultController.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class DefaultController extends AbstractController
{
    /**
     * @var Ui
     */
    protected $ui;
    
    protected function _init() {
        parent::_init();
        $this->ui = $this->app->ui;
    }
    
    public function handle() {
        $this->initSections();
        $this->initMainMenu();
        $this->initToolbar();
        $this->ui->menu->append(new View($this->app->registry->mainMenu));
        $this->ui->toolbar->append(new \Capsule\App\Cms\Ui\Toolbar\View($this->app->registry->toolbar));
        echo $this->ui->html;
    }
    
    /**
     * Инициализация секций
     *
     * @param void
     * @return void
     */
    protected function initSections() {
        $section = new Section;
        $html = clone $section;
        $html->id = 'html';

        $head = clone $section;
        $head->id = 'head';
        $html->append($head);
        
        $title = clone $section;
        $title->id = 'title';
        $title->append('Capsule ' . Capsule::getInstance()->config->version);
        $head->append($title);
        
        $body = clone $section;
        $body->id = 'body';
        $html->append($body);
        
        $pop_calendar = clone $section;
        $pop_calendar->id = 'popcalendar';
        $body->append($pop_calendar);

        $css = clone $section;
        $css->id = 'css';
        $head->append($css);

        $css->append(new Stylesheet($this->app->config->path->css->cssReset));
        $css->append(new Stylesheet($this->app->config->path->css->cssStyle));

        $css->append(new Stylesheet($this->app->config->ui->controls->css));

        $builtinCss = clone $section;
        $builtinCss->id = 'builtinCss';
        $head->append($builtinCss);

        $js = clone $section;
        $js->id = 'js';
        $head->append($js);

        $js->append(new Script($this->app->config->ui->controls->js));
        
        $js->append(new Script($this->app->config->path->js->jquery));
        $js->append(new Script($this->app->config->path->js->capsule));
        
        $css->append(new Stylesheet($this->app->config->path->jqueryUi->css1));
        $css->append(new Stylesheet($this->app->config->path->jqueryUi->css2));
        $css->append(new Stylesheet($this->app->config->path->jqueryUi->css3));
        $js->append(new Script($this->app->config->path->jqueryUi->js));

        $onload = clone $section;
        $onload->id = 'onload';
        $head->append($onload);
        
        $builtinJs = clone $section;
        $builtinJs->id = 'builtinJs';
        $head->append($builtinJs);
        
        $alert = clone $section;
        $alert->id = 'alert';
        $builtinJs->append($alert);

        $wrapper = clone $section;
        $wrapper->id = 'wrapper';

        $body->append($wrapper);

        $menu = clone $section;
        $menu->id = 'menu';
        
        $toolbar = clone $section;
        $toolbar->id = 'toolbar';

        $wrapper->append($menu);
        
        $workplace = clone $section;
        $workplace->id = 'workplace';
        
        $workplace->append($toolbar);

        $wrapper->append($workplace);
        $content = include(new Path(Capsule::getInstance()->systemRoot, $this->app->config->templates, 'about.php'));
        new Dialog(array(
            'instanceName' => 'about',
            'content' => $content,
            'appendTo' => 'capsule-cms-wrapper',
            'hidden' => true
        ));
//         $window = new DialogWindow('about');
//         $window->hidden = true;
//         $window->caption = 'About';
//         $window->width = 320;
//         $window->height = 240;
//         $window->content = include(new Path(Capsule::getInstance()->systemRoot, $this->app->config->templates, 'about.php'));
//         $view = new \Capsule\App\Cms\Ui\DialogWindow\View($window);
//         $wrapper->append($view);
        
        
    }
    
    /**
     * init toolbar
     *
     * @param void
     * @return void
     */
    protected function initToolbar() {
        $toolbar = new Toolbar('cms-toolbar');
        $this->app->registry->toolbar = $toolbar;
        Ui::getInstance()->css->append(new Stylesheet(
            $this->app->config->ui->toolbar->css
        ));
        Ui::getInstance()->js->append(new Script(
            $this->app->config->ui->toolbar->js
        ));
    }

    /**
     * init main menu
     *
     * @param void
     * @return void
     */
    protected function initMainMenu() {
        Ui::getInstance()->css->append(
            new Stylesheet($this->app->config->ui->mainMenu->css),
            'mainmenucss'
        );
        Ui::getInstance()->js->append(
            new Script($this->app->config->ui->mainMenu->js),
            'mainmenujs'
        );
        $menu = new Menu('main-menu');
        $this->app->registry->mainMenu = $menu;
        $menu_config = $this->app->config->mainMenu;
        foreach ($menu_config->item as $id => $config) {
            if ($config->get('disabled')) continue;
            $punct = new Punct($config->get('caption', 'не названный пункт'));
            $menu->addPunct($punct, $id);
            $this->_initMainMenuSubitem($punct, $config);
        }
    }
    
    /**
     * init main menu subitems
     *
     * @param void
     * @return void
     */
    private function _initMainMenuSubitem($o, $config) {
        $items = $config->get('item');
        if (!$items) {
            return;
        }
        $filter = $this->app->urlFilter;
        foreach ($items as $id => $config) {
            if ('delimiter' == $config->get('type')) {
                $o->addDelimiter();
                continue;
            }
            $name = $config->get('caption', 'не названный подункт');
            $url = $config->get('url');
            if (!preg_match('|^http://|', $url) && $url) {
                $url = $filter($url);
            }
            $sub = new SubPunct($name, $url);
            $sub->setDisabled($config->get('disabled'));
            $sub->setTarget($config->get('target'));
            $sub->setAction($config->get('action'));
            $o->addSubPunct($sub);
            $this->_initMainMenuSubitem($sub, $config);
        }
    }
}