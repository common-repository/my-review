<?php
/*
Plugin Name: My Review
Plugin URI: http://wordpress.sapiensworks.com/articles/my-review-nice-plugin-for-reviewers/
Description: This plugin helps you format your post as a review by making it easier for your readers to spot the good and the bad of the product/service you're reviewing.You can also rate different aspects(price, quality, taste - you name it) of it according to your liking.   
Author: Mike T.
Author URI: http://wordpress.sapiensworks.com/
Version: 1.2

/*  Copyright 2008    Mike T.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

*/

if (!class_exists('MyReviewTags'))
{

class MyReviewTags
{
	
	var $good;
	var $bad;
	var $score;
	var $original;
	var $simple_text;
	
	function MyReviewTags($text)
	{
		
		$this->original=$text;
	}
	
	function extract()
	{
			
	if (preg_match_all('/<!\-\- \{(rw_good|rw_bad|rw_score|rw_text)\}(.*)\{\/\1\} \-\->/si',$this->original,$match))
	{
		$this->_isrw=true;
		$tags=array();
		foreach($match[1] as $key=>$tag)
		{
			$tags[$tag]=$key;
		}
		$this->good= $this->getGood($tags,$match);
		$this->bad=$this->getBad($tags,$match);
		$this->score=$this->getScore($tags,$match);
		$this->simple_text=$this->getText($tags,$match);
		return true;
	}
	return false;
	}
	
	function getText(&$tags,&$match)
	{
		if (!isset($tags['rw_text'])) return null;
		if (!isset($match[2][$tags['rw_text']])) return null;
		return $this->cleanP(trim(stripslashes_deep($match[2][$tags['rw_text']])));
	}
	
	
	
	
	function comment()
	{
		$text=preg_replace('`(\{rw_good\}|\{rw_bad\}|\{rw_score\}|\{rw_text\})`ui','<!-- $1',$this->original);
		$text=preg_replace('`(\{\/rw_good\}|\{\/rw_bad\}|\{\/rw_score\}|\{\/rw_text\})`ui','$1 -->',$text);
		return $text;
	}
	
		
	function unComment()
	{
		$text=preg_replace('`<!\-\- (\{rw_good\}|\{rw_bad\}|\{rw_score\}|\{rw_text\})`ui','$1',$this->original);
		$text=preg_replace('`(\{\/rw_good\}|\{\/rw_bad\}|\{\/rw_score\}|\{\/rw_text\}) \-\->`ui','$1',$text);
		return $text;
	}
	
	function cleanPost()
	{
		return preg_replace('`\{(rw_good|rw_bad|rw_score|rw_text)\}.*\{\/\1\}`usi',null,$this->original);
	}
	
	
	
	function getGood(&$tags,&$match)
	{
		if (!isset($tags['rw_good'])) return '';
		if (!isset($match[2][$tags['rw_good']])) return '';
		return $this->cleanP(trim(stripslashes_deep($match[2][$tags['rw_good']])));
	}
	function cleanP($text)
	{
		return preg_replace('`(^</p>|<p>$)`i',null,$text);
	}
	
	function getBad(&$tags,&$match)
	{
		if (!isset($tags['rw_bad'])) return '';
		if (!isset($match[2][$tags['rw_bad']])) return '';
		return $this->cleanP(trim(stripslashes_deep($match[2][$tags['rw_bad']])));
	}
	
	function getScore(&$tags,&$match)
	{
	if (!isset($tags['rw_score'])) return array();
	if (!isset($match[2][$tags['rw_score']])) return array();
	$score=stripslashes_deep($match[2][$tags['rw_score']]);
	if (preg_match_all('`\{for="([^"]+)" value="(\d+)"\}(.*?)\{\/for\}`usi',$score,$scores))
		{
			foreach($scores[1] as $key=>$value)
			{
				$rate[$value]['rating']=$scores[2][$key];
				if ($rate[$value]['rating']>10) $rate[$value]['rating']=10;
				if ($rate[$value]['rating']<1) $rate[$value]['rating']=1;
				$total+=$rate[$value]['rating'];
				$rate[$value]['description']=trim($scores[3][$key]);
				
			}
			$rate['_final_']=round($total/count($rate),2);
			return $rate;
		}
		return array();	
	}
	
		
}

}

function rw_css()
{
	?>
<link href="<?php bloginfo("wpurl"); echo '/',PLUGINDIR,'/',dirname(plugin_basename(__FILE__)),'/rw.css'; ?>" type="text/css" rel="stylesheet" media="screen" />
	<?php
}

function rw_content($text)
{
	$review= new MyReviewTags($text);
	if (!$review->extract()) return $text;
	ob_start();
	include dirname(__FILE__).'/template.php';
	$txt=ob_get_clean();
	return str_replace('<p><!--  --></p>',null,$txt);
}

function rw_save($text)
{
	$rw= new MyReviewTags($text);
	return  $rw->comment();
	
}

function rw_before_edit($text)
{
	$rw= new MyReviewTags($text);
	return  $rw->unComment();
}

function rw_init()
{
	load_plugin_textdomain('my-review',PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));
}

add_filter('content_edit_pre','rw_before_edit');
add_filter('content_save_pre','rw_save');
add_filter('the_content','rw_content');
add_action('wp_head','rw_css');
add_action('init','rw_init');
remove_filter('the_content', 'wptexturize');
?>