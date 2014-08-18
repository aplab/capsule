<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 11.05.2014 6:55:12 YEKT 2014                                              |
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

use Capsule\Ui\Toolbar\Button;
use Capsule\App\Cms\Ui\Storage\View;
/**
 * Storage.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Storage extends DefaultController
{
    /**
     * (non-PHPdoc)
     * @see \Capsule\Controller\AbstractController::handle()
     */
    public function handle() {
        $this->initSections();
        $this->initMainMenu();
        $this->initToolbar();
        $this->overview();
        $this->ui->menu->append(new \Capsule\App\Cms\Ui\MainMenu\View($this->app->registry->mainMenu));
        $this->ui->toolbar->append(new \Capsule\App\Cms\Ui\Toolbar\View($this->app->registry->toolbar));
        echo $this->ui->html;
    }
    
    protected function overview() {
        $filter = $this->app->urlFilter;
        $toolbar = $this->app->registry->toolbar;
        
        $button = new Button;
        $toolbar->add($button);
        $button->caption = '/ Root';
        $button->url = $filter($this->mod);
        
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
        
        $view = new View('capsule-cms-storage-overview');
        $this->ui->workplace->append($view);
    }
    

}