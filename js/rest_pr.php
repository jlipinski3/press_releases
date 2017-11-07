<?php 
require_once("../data.php");

$all_or_nothing = array("pr_media_types", "pr_topics", "pr_year");
foreach($all_or_nothing as $get_key){$_GET[$get_key] = isset($_GET[$get_key])?$_GET[$get_key]:"all";}

$style_class = @$_GET["topics_toolbar"] == "true"?"corp_pr":"in_the_news";
$style_class .= @$_GET["bottom_border"]=="true"?" pr_bottom_border":""; //add bottom border
$cache_key = "corp_pr";
$year = $topics = array();

function make_dropdown($var_str){ //use var_str as string to point to variable name and get array
	$temp_str = "";
	global $$var_str;
	$temp = array_values(array_unique($$var_str));
	foreach($temp as $temp_val){$temp_str .= "<option value='" . $temp_val . "'" . ($_GET["pr_".$var_str]==$temp_val?" selected='selected'":"") . ">" . $temp_val . "</option>";}
	return $temp_str;
}

function draw_releases($cache_key, $style_class)
{
  $output = array("ajax_block" => "", "ajax_topics" => "", "ajax_yr" => ""); //to be spit out later as json
  $current_month = $year_select = "";
  global $year, $topics;
  
  if(!empty($cache_key)) 
  {
   foreach($cache_key as $media_id => $media_detail) {
    $month_output = "";
    $dorow = true;
    
   if(@array_intersect(array_map("trim", explode(",", $_GET["pr_media_types"])), $media_detail["media_type_id"]) || in_array($media_detail["media_type_id"], array_map("trim", explode(",", $_GET["pr_media_types"]))) || $_GET["pr_media_types"] == "all") //for passed media_type_name
   {
    if(isset($media_detail["tag_name"]))
    {
  if(@array_intersect(array_map("trim", explode(",", $_GET["pr_topics"])), $media_detail["tag_name"]) || in_array($media_detail["tag_name"], array_map("trim", explode(",", $_GET["pr_topics"]))) || $_GET["pr_topics"] == "all") //specific tag is found
  {
   $year[] = $media_detail["year"];
   $topics = array_merge($topics, array_values((array)$media_detail["tag_name"]));
   
   
   if($_GET["pr_year"]!="all") //for year dropdown, only proceed if not all, special case for last12
   {
    $dorow = false;
    if($_GET["pr_year"] == "last12")
    {
  $dorow = (strtotime($media_detail["issued_date"]) > strtotime("-1 year", time()))?true:false;
    } else {$dorow = ($media_detail["year"] == $_GET["pr_year"])?true:false;}
   }
   
   if(!$media_detail["press_release_approval"] && $media_detail["media_type_id"] == 39){$dorow = false;} //corporate press releases must have approval
   
   if($dorow)
   {
    if($current_month != $media_detail["month_year"]) //new month so do month heading
    {
  $current_month = $media_detail ['month_year']; //flag that we already did this months heading
  $first_item = empty($output["ajax_block"])?true:false;
  $output["ajax_block"] .= (!$first_item?"</div>":"") . "<div class='items_block " . ($first_item?"pr_first":"") . "'><div class='items_heading'>" . $media_detail['month_year'] . "</div>";
    }
    
    $output["ajax_block"] .= "<table class='item_row'><tr><td class='pr_published'>" . date("n/j/Y", strtotime($media_detail ['issued_date'])) . "</td><td class='pr_link'><a href=\"" . (!empty($media_detail["url"])?$media_detail["url"]."\" target=\"_blank":$media_detail["url"]) . "\">" . $media_detail["link_text"] . "</a></td></tr></table>";
   }
  }
    }
   }
   }
   
	$year_select = make_dropdown("year");
	sort($topics);
	$topics_select = make_dropdown("topics");
  }
 
 $output["ajax_topics"] = (!empty($topics_select)?"Topic: <select id='pr_topics' name='pr_topics'><option value='all'" . ($_GET["pr_topics"]=="all"?" selected='selected'":"") . ">All</option>".$topics_select."</select>":"");
 $output["ajax_yr"] = (!empty($year_select)?"From: <select id='pr_year' name='pr_year'><option value='last12'>Last 12 Months</option>".$year_select."<option value='all'" . ($_GET["pr_year"]=="all"?" selected='selected'":"") . ">All</option></select>":"");
 $output["ajax_block"] = (!empty($output["ajax_block"])?"<div class='pr_block ".$style_class."'>".$output["ajax_block"]."</div>":"<div class='pr_block ".$style_class."' style='padding-top: 26px'>There are no press releases for the selected topic.</div>");
 
 echo json_encode(array_map("utf8_encode", $output));
}

draw_releases($$cache_key, $style_class);
?>