<?php 
class Smallunch_lib_plugins_Analytics extends Zend_Controller_Plugin_Abstract
{
	protected $analytics_code;
	protected $enabled;
	
	public function __construct($options)
	{
		$this->analytics_code = $options['code'];
    $this->enabled = $options['enabled'];
	}
	public function dispatchLoopShutdown()
	{
		if ($this->analytics_code != '' && $this->enabled == true)
		{
    $response = $this->getResponse();
    $html = '<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("'.$this->analytics_code.'");
pageTracker._trackPageview();
} catch(err) {}</script>';
        $response->setBody(str_ireplace('</body>', $html.'</body>', $response->getBody()));
		}
	}
}