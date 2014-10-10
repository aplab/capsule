<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
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
use Capsule\App\Cms\Ui\UploadImage\View;
use Capsule\Ui\DialogWindow\DialogWindow;
use Capsule\Common\Path;
use Capsule\Capsule;
use Capsule\I18n\I18n;
use Capsule\Common\TplVar;
use Capsule\App\Cms\Ui\Stylesheet;
use Capsule\App\Cms\Ui\Script;
/**
 * Storage.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class UploadImage extends DefaultController
{
    protected $instanceName = 'capsule-ui-upload-image';
    
    /**
     * (non-PHPdoc)
     * @see \Capsule\Controller\AbstractController::handle()
     */
    public function handle() {
        $this->initSections();
        
        $this->ui->css->append(new Stylesheet($this->app->config->path->imageareaselect->css));
        $this->ui->js->append(new Script($this->app->config->path->imageareaselect->js));
        $this->ui->js->append(new Script($this->app->config->path->js->mousewheel));
        
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
        $button->caption = I18n::_('Open');
        $button->action = 'CapsuleUiUploadImage.getInstance(\'' . $this->instanceName . '\').selectFile()';
        $button->icon = $this->app->config->icons->cms . '/document--plus.png';
        
        $button = new Button;
        $toolbar->add($button);
        $button->caption = I18n::_('Paste');
        $button->action = 'CapsuleUiUploadImage.getInstance(\'' . $this->instanceName . '\').pasteClipboard()';
        $button->icon = $this->app->config->icons->cms . '/clipboard-paste.png';
        
        $button = new Button;
        $toolbar->add($button);
        $button->caption = I18n::_('Crop');
        $button->action = 'CapsuleUiUploadImage.getInstance(\'' . $this->instanceName . '\').toggleCrop()';
        $button->icon = $this->app->config->icons->cms . '/icon16_crop.png';
        
        $button = new Button;
        $toolbar->add($button);
        $button->caption = I18n::_('Resize');
        $button->action = 'CapsuleUiUploadImage.getInstance(\'' . $this->instanceName . '\').toggleResizable()';
        $button->icon = $this->app->config->icons->cms . '/image-resize.png';
        
        $button = new Button;
        $toolbar->add($button);
        $button->caption = I18n::_('Reset');
        $button->action = 'CapsuleUiUploadImage.getInstance(\'' . $this->instanceName . '\').resetImage()';
        $button->icon = $this->app->config->icons->cms . '/arrow-return-180-left.png';
        
        $button = new Button;
        $toolbar->add($button);
        $button->caption = I18n::_('Delete');
        $button->action = 'CapsuleUiUploadImage.getInstance(\'' . $this->instanceName . '\').reset()';
        $button->icon = $this->app->config->icons->cms . '/cross-script.png';
        
        $button = new Button;
        $toolbar->add($button);
        $button->caption = I18n::_('Upload');
        $button->action = 'CapsuleUiUploadImage.getInstance(\'' . $this->instanceName . '\').upload()';
        $button->icon = $this->app->config->icons->cms . '/drive-upload.png';
        
        $button = new Button;
        $toolbar->add($button);
        $button->caption = I18n::_('Favorites');
        $button->action = 'CapsuleUiUploadImage.getInstance(\'' . $this->instanceName . '\').favorites()';
        $button->icon = $this->app->config->icons->cms . '/star_1.png';
        
        $button = new Button;
        $toolbar->add($button);
        $button->caption = I18n::_('History');
        $button->action = 'CapsuleUiUploadImage.getInstance(\'' . $this->instanceName . '\').history()';
        $button->icon = $this->app->config->icons->cms . '/clock-history.png';
        
        $button = new Button;
        $toolbar->add($button);
        $button->caption = I18n::_('Settings');
        $button->action = 'CapsuleUiDialogWindow.getInstance(\'' . $this->instanceName . '-settings\').showCenter()';
        $button->icon = $this->app->config->icons->cms . '/wrench-screwdriver.png';
        
        $view = new View($this->instanceName); 
        
        $this->ui->workplace->append($view);
        
        TplVar::getInstance()->instanceName = $this->instanceName;
        $window = new DialogWindow($this->instanceName . '-settings');
        $window->hidden = true;
        $window->caption = I18n::_('Settings');
        $window->width = 320;
        $window->height = 240;
        $window->content = include(new Path(Capsule::getInstance()->systemRoot, $this->app->config->templates, 'storageSettings.php'));
        $view = new \Capsule\App\Cms\Ui\DialogWindow\View($window);
        $this->ui->wrapper->append($view);
        
        $window = new DialogWindow($this->instanceName . '-history');
        $window->hidden = true;
        $window->caption = I18n::_('History');
        $window->width = 640;
        $window->height = 480;
        $window->content = 'loading...';
        $view = new \Capsule\App\Cms\Ui\DialogWindow\View($window);
        $this->ui->wrapper->append($view);
        
        $window = new DialogWindow($this->instanceName . '-favorites');
        $window->hidden = true;
        $window->caption = I18n::_('Favorites');
        $window->width = 640;
        $window->height = 480;
        $window->content = 'loading...';
        $view = new \Capsule\App\Cms\Ui\DialogWindow\View($window);
        $this->ui->wrapper->append($view);
    }
    

}