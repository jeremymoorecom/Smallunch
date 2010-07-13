<?php
class Smallunch_lib_View_Helper_Short extends Zend_View_Helper_FormSelect
{
  /**
   * Trims $text to the number of specified $chars, 
   * appends $ending to return value
   * 
   * @param $text Text to trim
   * @param $chars Number of characters to trim to
   * @param $ending String to append to trimmed text
   */
  function short($text = '', $chars = 50, $ending = ' ...')
  {
  	$text = strip_tags(stripslashes($text));
    if (strlen($text) > $chars) {
    	$text = substr($text,0,$chars);
      $text = substr($text,0,strrpos($text,' '));
      $text = $text.$ending;
    }
    return $text;
  }
}