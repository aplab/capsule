<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.2.1                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2011                                                   |
// +---------------------------------------------------------------------------+
// | 14.08.2011 0:13:03 YEKT 2011                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Engine
 */
/**
 * CSSupaWrapper.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class CSSupaWrapper
{
    const SUPA_VERSION = '0.6a';
    
    /**
     * id объекта в документе
     * 
     * @var string
     */
    protected $id;
    
    /**
     * encoding (string="base64")
     * This determines the encoding used by method getClipboardContents(). 
     * Supported values (atm): "none", "base64" 
     * 
     * @var string
     */
    protected $encoding = 'base64';
    
    /**
     * previewscaler (string="fit to canvas")
     * In which way should the preview be scaled. 
     * Supported values: "fit to canvas", "original size"; 
     * 
     * @var string
     */
    protected $previewscaler = 'original size';
    
    /**
     * imagecodec (string="png")
     * The pasted image will get converted to this image format. 
     * Supported values (atm): "jpg", "png" 
     * 
     * @var string
     */
    protected $imagecodec = 'png';
    
    /**
     * trace (boolean=false)
	 * Do some output on the java plugin console. 
	 * Intended for debugging purposes only. 
     * 
     * @var boolean
     */
    protected $trace = false;
    
    /**
     * ClickForPaste (boolean=false)
	 * If set to true, clicking on the applet pastes the clipboard content. 
     * 
     * @var boolean
     */
    protected $clickForPaste = true;
    
    /**
     * Избежать повторов id
     * 
     * @var array
     */
    private static $cache = array();
    
    /**
     * Путь
     * 
     * @var string
     */
    private $path;
    
    /**
     * Размеры области просмотра
     * 
     * @var int
     */
    protected $previeverWidth = '2048';
    protected $previeverHeight = '2048';
    
    /**
     * Конструктор
     * 
     * @param string $id
     */
    public function __construct($id = 'supaApplet') {
        $validator = new CSValidateString();
        $validator
            ->setAllowNull(false)
            ->setNonempty(true)
            ->setTrim(true)
            ->setName('id');
        if ($validator->isValid($id)) {
            $this->id = $validator->getClean();
        } else {
            $msg = 'invalid id';
            trigger_error($msg, E_USER_ERROR);
        }
        if (array_key_exists($id, self::$cache)) {
            $msg = 'id already exists';
            trigger_error($msg, E_USER_ERROR);
        }
        $this->path = CSPath::relativeDir();
    }
    
    /**
     * Получить HTML - код объекта
     * 
     * @param boolean $as_object
     */
    public function getAppletCode($as_object = true) {
        if ($as_object) {
            ob_start();?> 
            <object id="<?php echo $this->id; ?>"
                    archive="<?php echo $this->path; ?>/<?php echo self::SUPA_VERSION; ?>/lib/Supa.jar"
                    classid="java:de.christophlinder.supa.SupaApplet.class" 
                    type="application/x-java-applet"
                    <?php if ($this->previeverWidth) : ?>
                    width="<?php echo $this->previeverWidth; ?>"
                    <?php endif; ?>
                    <?php if ($this->previeverHeight) : ?>
                    height="<?php echo $this->previeverHeight; ?>">
                    <?php endif; ?>
                <?php if ($this->clickForPaste) : ?><param name="ClickForPaste" value="true" /><?php endif; ?>               
                <param name="imagecodec" value="<?php echo $this->imagecodec; ?>" />
                <param name="encoding" value="<?php echo $this->encoding; ?>" />
                <param name="previewscaler" value="<?php echo $this->previewscaler; ?>" />
                <?php if ($this->trace) : ?><param name="trace" value="true" /><?php endif; ?>
                Applets disabled. Please enable applets.
            </object>         
            <?php return ob_get_clean();
        } else {
            ob_start();?>
            <applet id="<?php echo $this->id; ?>"
                    archive="<?php echo $this->path; ?>/<?php echo self::SUPA_VERSION; ?>/lib/Supa.jar"
                    code="de.christophlinder.supa.SupaApplet.class" 
                    <?php if ($this->previeverWidth) : ?>
                    width="<?php echo $this->previeverWidth; ?>"
                    <?php endif; ?>
                    <?php if ($this->previeverHeight) : ?>
                    height="<?php echo $this->previeverHeight; ?>">
                    <?php endif; ?>
                <?php if ($this->clickForPaste) : ?><param name="ClickForPaste" value="true" /><?php endif; ?>               
                <param name="imagecodec" value="<?php echo $this->imagecodec; ?>" />
                <param name="encoding" value="<?php echo $this->encoding; ?>" />
                <param name="previewscaler" value="<?php echo $this->previewscaler; ?>" />
                <?php if ($this->trace) : ?><param name="trace" value="true" /><?php endif; ?>
                Applets disabled. Please enable applets.
            </applet>         
            <?php return ob_get_clean();
        }
    }
    
    public function putAppletCode($as_object = true) {
        echo $this->getAppletCode($as_object);   
    }
    
    /**
     * Получить код javascript для вывода в документ
     * 
     * @param void
     * @return void
     */
    public function getJSCode() {
        $code = '<script type="text/javascript">';
        $code.= file_get_contents(dirname(__FILE__) . '/CSSupaWrapper.js');
        $code.= 'var ' . $this->id . ' = new CSSupaWrapper(\'' . $this->id . '\')';
        $code.= '</script>';
        return $code;
    }
    
    public function putJsCode() {
        echo $this->getJsCode();   
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}