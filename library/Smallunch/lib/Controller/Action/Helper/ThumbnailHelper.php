<?php
/**
 *
 * @author x
 * @version 
 */
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Controller/Action/Helper/Abstract.php';
require_once 'Smallunch/lib/bundles/phpthumb/ThumbLib.inc.php';
/**
 * Smaullunch Thumbnail Action Helper 
 * 
 * @uses actionHelper Zend_Controller_Action_Helper
 */
class Smallunch_lib_Controller_Action_Helper_ThumbnailHelper extends Zend_Controller_Action_Helper_Abstract {
	/**
	 * @var Zend_Loader_PluginLoader
	 */
	public $pluginLoader;
	
	/**
	 * Constructor: initialize plugin loader
	 * 
	 * @return void
	 */
	public function __construct() {
		// TODO Auto-generated Constructor
		$this->pluginLoader = new Zend_Loader_PluginLoader ( );
	}
	
	/**
	 * Strategy pattern: call helper as broker method
	 */
	public function direct() {
		// TODO Auto-generated 'direct' method
	}
	/**
	 * Generate thumbnail from provided file w/ specified size
	 *
	 * @param string $sourcePath
	 * @param string $destPath
	 * @param integer $width
	 * @param integer $height
	 * @param integer $quality
	 */
  public function createThumbnail($sourcePath, $destPath, $width, $height, $quality = 100)
  {
    try {
      $thumb = PhpThumbFactory::create($sourcePath, array ('jpegQuality' => $quality));
      $thumb->resize($width, $height);
      $thumb->save($destPath);
    }
    catch (Exception $e) {
    	echo $e->getMessage();
    }
  }
}

