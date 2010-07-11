<?php 
class Smallunch_lib_plugins_AjaxLoader extends Zend_Controller_Plugin_Abstract
{
  public $text = 'Processing Please wait..';
  
  public function __construct($options)
  {
    if (array_key_exists('text', $options) && trim($options['text'] != '')) {
      $this->text = $options['text'];
    }
  }
  public function dispatchLoopShutdown()
  {
  	/*
  	 #loading {
			  position:fixed;
			  top:0;left:0;
			  color:#fff;
			  text-align:center;
			  width:100%;
			  padding:2px 2px 2px 20px;
			  display:none;
			  background-color:#1263B9;
			} 
  	 */
    $response = $this->getResponse();
    $html = '<div id="loading">'.$this->text.'</div>';
    $response->setBody(str_ireplace('</body>', $html.'</body>', $response->getBody()));
  }
}