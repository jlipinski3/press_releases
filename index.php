<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<head>
<title>
Press Releases using simple REST API
</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
//for press releases and in the news mediamanager fed dynamic content
function get_releases(ajax_params, span_id) //use span_id also for variable name of ajax calls array for this instance of func
{
    var ajax_data = $.extend({pr_year: $("select#pr_year").val(),pr_topics: $("select#pr_topics").val()}, ajax_params);
    ajax_calls[span_id].push(
        $.ajax({
            url: "js/rest_pr.php",
            data: ajax_data,
            dataType: "json",
            success: function(data){
                $("span#ajax_yr").html(data["ajax_yr"]);
				$("span#ajax_topics").html(data["ajax_topics"]);
                $("span#"+span_id).html(data["ajax_block"]);
                $.each(ajax_calls[span_id], function(i,v){v.abort();}); //abort any outstanding requests after content is delivered
            },
            complete: function(){$("select#pr_year, select#pr_topics").change(function(){get_releases(ajax_params, span_id);});}, //reapply change binder because #pr_year and #pr_topics are delivered in content
            cache: false
        })
    );
}
</script>
<style>
body {margin: 10px}
body, td {font: 12px normal lucida sans, arial, sans-serif}
a {color: #ff6600}
a:hover {color: #00cc00}

/* FOR CORPORATE PRESS RELEASES */
div.press_releases {padding-top: 18px; margin-bottom: 26px}
div.press_releases select {font-size: 15px}
div.press_releases .user_select {font-size: 14px; display: inline-block; padding-right: 15px; text-transform: uppercase; letter-spacing: 1px}
div.press_releases .corp_pr div.items_block {border-top: 1px solid #cccccc; padding: 16px 0px 11px 0px}
div.press_releases div.items_block.pr_first {border-top: none}
div.press_releases .in_the_news div.items_block {border-top: 1px solid #cccccc; padding: 6px 0px 1px 0px}
div.press_releases .in_the_news div.items_block.pr_first {padding-top: 0px; border-top: none}
div.press_releases .items_heading {font-size: 16px; color: #000000; padding-bottom: 5px; text-transform: uppercase;}
div.press_releases .pr_published {font-size: 13px; color: #333333; width: 85px; vertical-align: top; text-align: right; line-height: 130%}
div.press_releases .pr_link {text-align: left; max-width: 585px; vertical-align: top; line-height: 130%; padding-left: 20px}
div.press_releases .pr_block.corp_pr {border-top: 1px solid #666666}
div.press_releases .pr_block.corp_pr {margin-top: 16px}
div.press_releases .pr_bottom_border {border-bottom: 1px solid #666666}
</style>
</head>
<body>
<div class="press_releases">
<span class="user_select">Type: <select id="pr_type" name="pr_type">
<option value="all">All</option>
<option value="39">General Corporate Press Release</option>
<option value="98">EV In The News</option>
<option value="67">NextShares In The News</option>
</select></span>
<span class="user_select" id="ajax_topics"></span>
<span class="user_select" id="ajax_yr"></span>
<span id="pr_display"></span>
</div><script type="text/javascript">
$(function(){
    get_releases(
      {
        pr_topics: $("select#pr_topics").val(),
        pr_year: $("select#pr_year").val(),
        pr_media_types: $("select#pr_type").val(),
        bottom_border: false,
        topics_toolbar: true
      },
    "pr_display");
    $("select#pr_type").change(function(){get_releases({pr_media_types:$("select#pr_type").val(),bottom_border:false,topics_toolbar:true},"pr_display");});
});
ajax_calls = typeof ajax_calls!="undefined"?ajax_calls:{}; //to be used as ajax call reference for aborting after delivering data
ajax_calls["pr_display"] = [];
</script>
</body>
</html>