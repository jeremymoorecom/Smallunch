<?php
class Smallunch_lib_View_Helper__Short extends Zend_View_Helper_FormSelect
{
  /**
   * Return Categories Doctrine Array
   * for categories that have active listings
   */
  function short($text, $chars)
  {
  	$text = strip_tags(stripslashes($text));
    if (strlen($text) > $chars) {
    	$text = substr($text,0,$chars);
      $text = substr($text,0,strrpos($text,' '));
      $text = $text." ...";
    }
    return $text;
  }
}