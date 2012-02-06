<?php
/*
 * The Output And Shortcode Functions For sociable
 */
/*
 * Returns The Skyscraper Output For The Global $post Object Do Not 
 */
function diff_date($date1, $date2){
		
		
	$date1 = mktime(substr($date1,8,2), substr($date1,10,2), substr($date1,12,2), substr($date1,4,2), substr($date1,6,2), substr($date1,0,4));
	$date2 = mktime(substr($date2,8,2), substr($date2,10,2), substr($date2,12,2), substr($date2,4,2), substr($date2,6,2), substr($date2,0,4));
	
	$diff_time = ceil((($date2 - $date1)/60));
	return $diff_time;
}
 
 
function skyscraper_html( $where = "" ){
    global $skyscraper_options, $wp_query; 
	if (!is_admin() || 1==1){
				
		
	//	echo "<script type='text/javascript'>";
	//	echo "var skyscraper_dir = '".SOCIABLE_HTTP_PATH."' ;";
	//	echo "</script>";
		echo " var skyscraper_dir =  document.createElement('input');
				skyscraper_dir.id = 'skyscraper_dir';
				skyscraper_dir.type = 'hidden';
				skyscraper_dir.value = '".SOCIABLE_HTTP_PATH."';
				document.body.appendChild(skyscraper_dir);	";
			
		$widget_width = str_replace("px", "", $skyscraper_options["widget_width"]);
		
		$widget_position = "null";
		if (isset($skyscraper_options["widget_position"])){
			$widget_position = 1;
		}
		
		$labels_color = $skyscraper_options["labels_color"];
		$text_size = str_replace("px", "", $skyscraper_options["text_size"]);
		$background_color = $skyscraper_options["background_color"];
		
		$addWhere = "";
		
		if ($where == ""){
			$addWhere = "var div = document.createElement('div');
						div.id = 'skyscraper';
						document.body.appendChild(div);";
		}		
		
		$url_site= $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
				
		$script = "			
				
				if (!document.getElementById('fb-root')){
					var div = document.createElement('div');
					div.id = 'fb-root';
					document.body.appendChild(div);
				}
			
				(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = \"http://connect.facebook.net/en_US/all.js#xfbml=1\";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
				
			  (function() {
			    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			    po.src = 'https://apis.google.com/js/plusone.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			  })();
			
				
				(function() {
			    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			    po.src = 'http://platform.twitter.com/widgets.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			  })();
				
			".$addWhere."
			
			jQuery(document).ready(function(){
			
						oPlugin.toolbarStart('skyscraper', ".$widget_position.",230,".$widget_width.",'".$background_color."','".$labels_color."',false,'#6A6A6A',".$text_size.",'#587cc8');
										 		
						".get_share_node()."
						
						".get_counters_node()."			
						
						".get_latest_node()."
															
						".get_mentions_node()."
										
						".get_follow_us_node()."										
						
						".get_rss_node()."
						
						oPlugin.CreateGoToTop('New_Id_12','Top','<img src=\"".SOCIABLE_HTTP_PATH."images/toolbar/gototop.png\" style=\"width:30px;\" />');
						
						
						oPlugin.CreateGoToHome('New_Id_13','Go Home','<img src=\"".SOCIABLE_HTTP_PATH."images/toolbar/gotohome.png\" style=\"width:30px;\" />');
												
	    });
	   
	
	
		";
	
		echo $script;
	
	}
}
function get_rss_node(){
	$rss_node = "";
	$latest_posts = "";
	global $skyscraper_options;
	
	$version = phpversion();
	
	if ( substr($version,0,1) == 5 &&  isset($skyscraper_options["rss_feed"]) &&  $skyscraper_options["rss_feed"]!="http://"){ 
	
		include("rss_php.php");
		
		$rss = new rss_php;
    	$rss->load($skyscraper_options["rss_feed"]);
    	$items = $rss->getItems();
		
		if (!empty($skyscraper_options["rss_feed"])){
			 
			if (count($items) > 0){
				
				$cant = 0;
				foreach($items as $item){
					
					if ($cant <= $skyscraper_options["num_rss"]){
						
						$title="";
						if (isset($item["title"])){
							$title =  addslashes($item["title"]);
						}
						$description="";
						if (isset($item["description"])){
							$description =  addslashes($item["description"]);
						}
						$guid="";
						if (isset($item["link"])){
							$guid =  addslashes($item["link"]);
						}
						$pubDate="";
						if (isset($item["pubDate"])){
							$pubDate =  ago(strtotime($item["pubDate"]));
						}
																	
						$latest_posts .= "['".$title."','','".$description."','".$guid."','".$pubDate."'],";
					}
					else{
						break;
					}
				 	$cant++;						 
				} 
				$latest_posts = trim($latest_posts, ",");	
			}						
		}	
	}
	
	if ($latest_posts != ""){
	
	$rss_node = "var LatestBlogPostContent = [
										".$latest_posts."
										];
					oPlugin.CreateNode('New_Id_5','Posts','',LatestBlogPostContent,'Notice',220,460);";
	}				
	return $rss_node;
}
function get_latest_node(){
	$latest_node = "";	
	global $skyscraper_options;	
	
	if ( isset($skyscraper_options["twitter_username"])){
	
		$latest_tweets = get_option_tweets("skyscraper_latest");
		if ($skyscraper_options["twitter_username"] != ""){
		
			if ($latest_tweets != ""){
			
				$latest_node = "var LastestTwittsContent = [
													".$latest_tweets."	
													];
														
									oPlugin.CreateNode('New_Id_3','Latest','',LastestTwittsContent,'Notice',220,460);";
			}
		}
	}
	return $latest_node;
}
function get_mentions_node(){
	$mentions_node = "";
	
	global $skyscraper_options;
	
	if ( isset($skyscraper_options["twitter_username"])){
	
		$mentions_tweets = get_option_tweets("skyscraper_mentions");
		if ($skyscraper_options["twitter_username"] != ""){
		
			if ($mentions_tweets != "" ){
			
				$mentions_node = "var  TweetsMentionsContent = [
													".$mentions_tweets."	
														];										
									oPlugin.CreateNode('New_Id_4','Mentions','',TweetsMentionsContent,'Notice',220,460);";
			}
		}
	}
	return $mentions_node;
}
function get_counters_node(){
	
	global $skyscraper_options;
	global $title_shared;
	global $url_shares;
	
	$counters_node = "";
	/*
	
	<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script><span><a href="http://twitter.com/share" style="color:#ffffff" class="twitter-share-button" data-url="" data-text="" data-count="vertical" data-via="gBrowser" data-lang="en">Tweet</a></span>
	
	*/
	if ((!empty($skyscraper_options["counters"]["check"]))){
		 
		$counters_node = " var url = '". addslashes(trim($url_shares))."';
							var title = '".addslashes(trim($title_shared)) ."';
							
							var counter = '<table align=\"center\" cellspacing=\"0\" cellpadding=\"0\" ><tr><td style=\"height:72px;width:50px;\" align=\"center\">';
							counter += '<div class=\"fb-like\" data-send=\"false\" data-layout=\"box_count\" data-width=\"50\" data-href=\"'+url+'\" data-show-faces=\"false\"></div>';
							counter += '</td></tr>';
							counter += '<tr><td align=\"center\">';
							counter +=' <div class=\"g-plusone\" data-size=\"tall\" data-href=\"'+url+'\"></div>';
							counter += '</td></tr>';
							counter += '<tr><td align=\"center\"  style=\"height:72px\">';						
							counter +=  '<a href=\"https://twitter.com/share\" data-with=\"50\" data-text=\"'+ title +' (via @sociablesite)  [skyscraper_counters] \" data-url=\"'+ url + '\" class=\"twitter-share-button\" data-count=\"vertical\">Tweet</a>';
							counter += '</td></tr>';
							counter += '</table>';
							
							oPlugin.CreateSimpleNode('New_Id_2','Counters<br/>', counter ,".$skyscraper_options["counters"]["folded"].");				
								";
	}
	
	return $counters_node;						
}
function get_share_node(){
	
	global $skyscraper_options;
	
	$share_node = "";
		
	if (!empty($skyscraper_options["share"]["check"])){
		$share_buttons = share_links();
		$share_node = "oPlugin.CreateSimpleNode('New_Id_1','Share', '".$share_buttons."',".$skyscraper_options["share"]["folded"].");";
	}
	
	return $share_node;
}
function get_follow_us_node(){
	$follow_us_node = "";
	global $skyscraper_options; 
	
	if (isset($skyscraper_options["follow_us"])){
	
		$follow_info = empty_accounts();
		 	
		if ( $follow_info["active"] > 0 && ($follow_info["empty"] <  $follow_info["active"])){
		
			$follow_us_node = "oPlugin.CreateNode('New_Id_6','Follow', '',  '".sc_follow_links()."','Plano',40,140)";	
		}
	}
	return $follow_us_node;
}
function empty_accounts(){
	$empty = 0;
	$active = 0;
	global $skyscraper_options; 
	
	foreach($skyscraper_options["follow_us"] as $follow_us){
		
		if (empty($follow_us["account"])){
			$empty++;
		}
		
		if (isset($follow_us["active"])){
			$active++;
		}
	}
	
	return array("empty" =>$empty, "active"=>$active);
}
function sc_follow_links(){
	global $skyscraper_options;
	
	$follow_buttons = "";
	
	foreach($skyscraper_options["follow_us"] as $follow_us){
		
		$follow_us["account"]= trim($follow_us["account"]);
		
		if (!empty($follow_us["active"]) && !empty($follow_us["account"]) ){
			
			$follow_us["account"] = str_replace("http://", "", $follow_us["account"]);
			$follow_us["account"] =  "http://".$follow_us["account"];
			
			$follow_buttons .=  "<a target=\'_blank\' rel=\'nofollow\' href=\'".$follow_us["account"]."\'><img  src=\'".SOCIABLE_HTTP_PATH."images/toolbar/".$follow_us["logo"]."\' /></a>";
		}
	}
	
	return $follow_buttons;
}
function share_links(){
	
	$url = addslashes(get_bloginfo('wpurl'));
	$blogname = addslashes(get_bloginfo('name'));
	global $title_shared;
	global $url_shares;
	
	
	$page = trim(addslashes($url_shares));
	$permalink = trim(addslashes($url_shares));
	$title = trim(addslashes($title_shared));
	
	$share_links = array();
	$share_links = array(
	
 
		"twitter" => array('favicon' => 't.png',
            				'url' => 'http://twitter.com/intent/tweet?text='.$title.'%20-%20'.$permalink.'%20(via%20@sociablesite) %23sociable [skyscraper_share]',
							 'title' => "Share on Twitter"),
            				
        "facebook" => array('favicon' => 'f.png',
							'url' => 'http://www.facebook.com/share.php?u='.$permalink.'&amp;t='.$title.'',
							 'title' => "Share on Facebook"),
							
		"google" => array('favicon' => 'g.png',
						'url' => 'https://mail.google.com/mail/?view=cm&fs=1&to&su='.$title.'&body='.$permalink.'&ui=2&tf=1&shva=1',
							 'title' => "Share on Gmail"),
							
		"favorites" => array('favicon' => 'fv.png',
			 			     'url' => 'javascript:AddToFavorites();',
							 'title' => "Add to favorites - doesn\'t work in Chrome"),
							
		"stumble" => array('favicon' => 's.png',
			 			   'url' => 'http://www.stumbleupon.com/submit?url='.$permalink.'&title='.$title.'',
							'title' => "Share on StumpleUpon"),
							
		"delicious" => array('favicon' => 'o.png',
							 'url' => 'http://delicious.com/post?url='.$permalink.'&amp;title='.$title.'&amp;notes=EXCERPT',
							 "title" => "Share on delicious"),
							
		"reader" => array('favicon' => 'n.png',
							'url' => 'http://www.google.com/reader/link?url='.$permalink.'&amp;title='.$title.'&amp;srcURL='.$permalink.'&amp;srcTitle='.$blogname.'',
							"title" => "Share on Google Reader"),
		
		"linkedin" => array('favicon' => 'i.png',
							'url' => 'http://www.linkedin.com/shareArticle?mini=true&amp;url='.$permalink.'&amp;title='.$title.'&amp;source='.$blogname.'&amp;summary=EXCERPT',
							"title" => "Share on LinkedIn")
	
	);
	
	$share_buttons = "";
 
	foreach($share_links as $link){
		
		$share_buttons .=  "<a target=\'_blank\' rel=\'nofollow\' href=\'".addslashes($link["url"])."\' title=\'".addslashes($link["title"])."\'><img  src=\'".SOCIABLE_HTTP_PATH."images/toolbar/".addslashes($link["favicon"])."\' /></a>";
	}
		
	return $share_buttons;
}
/*
 * Template Tag To Echo The Sociable 2 HTML
 */
function do_skyscraper(){
    echo  skyscraper_html();
}
/*
 * Sociable 2 Shortcode
 */
function skyscraper_shortcode(){    
    return skyscraper_html();
}
function auto_skyscraper($content, $admin = false){
	global $skyscraper_options;
//	echo $_SERVER["REQUEST_URI"];
	if ($admin){
		//	 die("acaa22");
		$content =  skyscraper_html();
		return $content;
	}
	
	 if( ! isset( $skyscraper_options['active'] )){
       $content =  "";
	   return $content;	
    }
	
    if( ! isset( $skyscraper_options['locations'] ) || ! is_array( $skyscraper_options['locations'] ) || empty( $skyscraper_options['locations'] ) ){
		
       $content =  "";
    } else {
		
        $locations = $skyscraper_options['locations'];
    }
    /*
     * Determine if we are supposed to be displaying the output here.
     */
    $display = false;
    
    /*
     * is_single is a unique case it still returning true 
     */
	
    //If We Can Verify That We are in the correct loaction, simply add something to the $display array, and test for a true result to continue.
    foreach( $locations as $location => $val ){
     
        //First We Handle is_single() so it returning true on Single Post Type Pages is not an issue, this is not the intended functionality of this plugin
        if( $location == 'is_single' ){
            //If we are not in a post, lets ignore this one for now

            if( is_single() && get_post_type() == 'post' ){
                $display = true;
                break;
            } else {
                continue; // So not to trigger is_single later in this loop, but still be allowed to handle others
            }
            
        } elseif( strpos( $location , 'is_single_posttype_' ) === 0 ){ //Now We Need To Check For The Variable Names, Taxonomy Archives, Post Type Archives and Single Custom Post Types.
            
            //Single Custom Post Type
            $post_type = str_replace( 'is_single_posttype_' ,  '' , $location );
            if( is_single() && get_post_type() == $post_type ){
                $display = true;
                break;
            }
            
        } elseif( strpos( $location , 'is_posttype_archive_' ) === 0 ){
            
            //Custom Post Type Archive
            $post_type = str_replace( 'is_posttype_archive_' ,  '' , $location );
            if( is_post_type_archive( $post_type ) ){
                $display = true;
                break;
            }
            
        } elseif( strpos( $location , 'is_taxonomy_archive_' ) === 0 ) {
            
            //Taxonomy Archive
            $taxonomy = str_replace( 'is_taxonomy_archive_' ,  '' , $location );
            if( is_tax( $taxonomy ) ){
                $display = true;
                break;
            }
            
        } elseif( function_exists( $location ) ) {
            
            //Standard conditional tag, these will return BOOL
            if( call_user_func( $location ) === true ){
                $display = true;
                break;
            }
            
        } else {
            continue;
        }
        
        
    }
    
    //If We have passed all the checks and are looking in the right place lets do this thang
    if( isset( $skyscraper_options['automatic_mode'] ) && $display === true ){
		if (isset($skyscraper_options["topandbottom"])){
        	$content =  skyscraper_html();
		}else{
			$content =  skyscraper_html();
		}
    }
	else{
		$content =  skyscraper_html();
	} 
 
    
    
    return $content;
}
function get_tweets_username($username_complete){
	
	if (function_exists('curl_init')) {
		
		// last tweets 
		$username = str_replace("@", "", $username_complete);
		$url = "https://api.twitter.com/1/statuses/user_timeline/".$username.".json";
		$latest = curl_call($url);
		$latest_row = parser_twitter_results($latest,0);		
		update_option( "skyscraper_latest", $latest_row );
		
		// last mentions
		$url = "http://search.twitter.com/search.json?q=@".$username."&rpp=5&include_entities=true&result_type=mixed";
		$mentions = curl_call($url);
		
		if (count($mentions["results"]) > 1){
			
			$mentions_row = parser_twitter_results($mentions["results"],1);			
			update_option( "skyscraper_mentions", $mentions_row );			
		}					
	}
}
function ago($time){
   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
   $lengths = array("60","60","24","7","4.35","12","10");
   $now = time();
   $difference     = $now - $time;
   $tense         = "ago";
   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
       $difference /= $lengths[$j];
   }
   $difference = round($difference);
   if($difference != 1) {
       $periods[$j].= "s";
   }
   return $difference." ".$periods[$j]."ago";
}
function parser_twitter_results($results = array(), $mention){
	
	$options_latest = array();
	$options_latest = array("date" => date("YmdHis"));
	global $skyscraper_options;
	$i = 0;
	
	if (is_array($results)){
	
		foreach($results as $tweet){
		
			$options_latest[$i] = array();	
			$options_latest[$i]["text"] = $tweet["text"];
			$options_latest[$i]["created_at"] = ago(strtotime($tweet["created_at"]));
			
			if ($mention){
				$options_latest[$i]["name"] = $tweet["from_user_name"];
			}
			else{
				$options_latest[$i]["name"] = $tweet["user"]["name"];
			}
			
			$i++;
			if ($i == $skyscraper_options["num_tweets"]){
				break;
			}			
		}
	}
	
	return $options_latest;
}
function  curl_call($url){
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);		
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);	
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		if ($info["http_code"] == "200"){
			
			$return = json_decode($output,1);
		}
		else{
			$return = false;
		}
		
		return $return;
}
function get_option_tweets($option){
	
	global $skyscraper_options;
	$skyscraper_latest = get_option($option);
		
	if (empty($skyscraper_latest)){
	
		get_tweets_username($skyscraper_options["twitter_username"]);
		$skyscraper_latest = get_option($option);
	}
	else{
   
		// 5 minutes
		if (diff_date($skyscraper_latest["date"], date("YmdHis")) > 5){
		
			get_tweets_username($skyscraper_options["twitter_username"]);
			$skyscraper_latest = get_option($option);
		}
	}
	
	
	return generate_tweets_box_content($skyscraper_latest);
}
function generate_tweets_box_content($tweets){
	$content = "";
	if (isset($tweets["date"])){
		unset($tweets["date"]);
	}
	
	
	foreach($tweets as $tweet){
		
		$tweet["name"] = addslashes($tweet["name"]);
		$tweet["text"] = addslashes($tweet["text"]);
			
		$content .= "['".$tweet["name"]."','".$tweet["name"]."','".$tweet["text"]."','','".$tweet["created_at"]."'],";
	}
	$content = trim(trim(trim($content), ","));
	
	return $content;
}
if (!empty($_GET["sky"])){
add_action('wp_ajax_my_action', 'my_action_callback');
function my_action_callback() {
	global $wpdb; global $skyscraper_options; // this is how you get access to the database
	$whatever = intval( $_POST['whatever'] );
	$whatever += 10;
        echo $whatever;
	die(); // this is required to return a proper result
}
}
?>