<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 12 мая 2015 г. 0:35:17 YEKT 2015                                              |
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
use Capsule\Ui\ObjectEditor\Oe2 as Oe; 
use Capsule\I18n\I18n;

use Capsule\Ui\Toolbar\Button;
use Capsule\Url\Redirect;
use Capsule\Superglobals\Post;
use App\Cms\Ui\ObjectEditor\View;

/**
 * Product.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Product extends NestedItem 
{
    protected $moduleClass = 'Capsule/Module/Catalog/Product';
    
    protected function add() {
        $filter = $this->app->urlFilter;
        $toolbar = $this->app->registry->toolbar;
    
        $button = new Button;
        $toolbar->add($button, 'save');
        $button->caption = 'Save';
        $button->icon = $this->app->config->icons->cms . '/disk.png';
        $button->action = 'CapsuleUiObjectEditor.getInstance("object_editor").save()';
    
        $button = clone $button;
        $toolbar->add($button, 'save and exit');
        $button->caption = 'Save and exit';
        $button->icon = $this->app->config->icons->cms . '/disk_go.png';
        $button->action = 'CapsuleUiObjectEditor.getInstance("object_editor").saveAndExit()';
    
        $button = clone $button;
        $toolbar->add($button, 'exit');
        $button->caption = 'Exit without saving';
        $button->url = $filter($this->mod);
        $button->icon = $this->app->config->icons->cms . '/arrow-return-180.png';
        $button->action = null;
    
        $button = clone $button;
        $toolbar->add($button, 'save and add new');
        $button->caption = 'Save and add new';
        $button->icon = $this->app->config->icons->cms . '/disk--plus.png';
        $button->action = 'CapsuleUiObjectEditor.getInstance("object_editor").saveAndAdd()';
        $button->url = null;
    
        $class = $this->moduleClass;
        $tmp = $this->createElement($class);
        if ($tmp->status) {
            $oe = new Oe($tmp->item, 'object_editor');
            $this->ui->workplace->append(new View($oe));
            return;
        }
        if (isset(Post::getInstance()->{self::SAVE_AND_EXIT})) {
            Redirect::go($filter($this->mod));
            return;
        }
        if (isset(Post::getInstance()->{self::SAVE_AND_ADD})) {
            Redirect::go($filter($this->mod, 'add'));
            return;
        }
        Redirect::go($filter($this->mod, 'edit', $tmp->item->id));
    }
    
    protected function edit() {
        $id = reset($this->param);
        $class = $this->moduleClass;
        $item = $class::getElementById($id);
        $filter = $this->app->urlFilter;
        if (!($item instanceof $class)) {
            $msg = 'Object not found';
            $this->ui->alert->append(I18n::_($msg));
            Redirect::go($filter($this->mod));
            return;
        }
        $toolbar = $this->app->registry->toolbar;
    
        $button = new Button;
        $toolbar->add($button, 'save');
        $button->caption = 'Save';
        $button->icon = $this->app->config->icons->cms . '/disk.png';
        $button->action = 'CapsuleUiObjectEditor.getInstance("object_editor").save()';
    
        $button = clone $button;
        $toolbar->add($button, 'save and exit');
        $button->caption = 'Save and exit';
        $button->icon = $this->app->config->icons->cms . '/disk_go.png';
        $button->action = 'CapsuleUiObjectEditor.getInstance("object_editor").saveAndExit()';
    
        $button = clone $button;
        $toolbar->add($button, 'exit');
        $button->caption = 'Exit without saving';
        $button->url = $filter($this->mod);
        $button->icon = $this->app->config->icons->cms . '/arrow-return-180.png';
        $button->action = null;
    
        $button = clone $button;
        $toolbar->add($button, 'save and add new');
        $button->caption = 'Save and add new';
        $button->icon = $this->app->config->icons->cms . '/disk--plus.png';
        $button->action = 'CapsuleUiObjectEditor.getInstance("object_editor").saveAndAdd()';
        $button->url = null;
    
        $tmp = $this->updateItem($item);
        if ($tmp->status) {
            $oe = new Oe($item, 'object_editor');
            $this->ui->workplace->append(new View($oe));
            return;
        }
        if (isset(Post::getInstance()->{self::SAVE_AND_EXIT})) {
            Redirect::go($filter($this->mod));
            return;
        }
        if (isset(Post::getInstance()->{self::SAVE_AND_ADD})) {
            Redirect::go($filter($this->mod, 'add'));
            return;
        }
        $oe = new Oe($item, 'object_editor');
        $this->ui->workplace->append(new View($oe));
    }
}