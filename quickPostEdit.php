<?php

/*
Plugin Name: Quick post editor
Description: Quickly edit posts.
Author: Nikola Pavlicevic
Author URI: http://npavlicevic.wordpress.com
Version: 1.1
Tags: quick,edit,post
License: GPL2
*/


//da vidim da li nekako mogu prom. naslov clanka u tabeli posle cuvanja promena
add_action("init","quick_enq_scripts_styles");
add_filter("publish_post","quick_get_pages");
add_filter("post_row_actions","quick_custom_menu",10,2);
add_action("admin_footer","quick_print_form");
add_action("admin_head","quick_print_scripts");
add_action("wp_head","quick_print_scripts");
add_action("wp_footer","quick_print_form");
add_filter("the_content","quick_front_link");

function quick_enq_scripts_styles(){
	$dir=basename(dirname(__FILE__));
	$path=plugins_url()."/".$dir;
	wp_enqueue_script("jquery");
	wp_register_style("cleditor_style","$path/cleditor/jquery.cleditor.css");
	wp_enqueue_style("cleditor_style");
	wp_register_script("cleditor","$path/cleditor/jquery.cleditor.js");
	wp_enqueue_script("cleditor");
}

function quick_custom_menu($actions,$post){

	//die(print_r($actions));
	$actions["quick_post_edit"]="<a href='javascript:void(0)' class='quick_post_edit' onclick='quick_edit_click({$post->ID})'>Quickly edit post</a>";
	
	return $actions;
}

function quick_print_form(){
	?>
		<div id="quick_post_holder">
		<div id="quick_form" class="window" style="display:none;width:95%;">
		<form id="quick_post_editor" style="width:95%;">
			<label style="color:#f2f2f2;">Post title</label><br/>
			<input type="text" id="quick_post_title" style="width:95%;" /><br/>
			<label style="color:#f2f2f2;">Post contents</label><br/>
			<textarea cols="125" rows="25" id="quick_edit_textarea" style="width:95%;"></textarea><br/>
			<input type="hidden" id="quick_edit_hidden" value="0"/>
			<button type="button" name="quick_save" id="quick_save" onclick="quick_save_click()">Save</button><button type="button" name="quick_close" id="quick_close" onclick="quick_close_click()">Close</button>&nbsp;<label style="color:#f2f2f2;display:none;" id="quick_save_success"></label>
		</form>
		</div>
		</div>
		<div id="quick_mask"></div>
	<?php
}

function quick_print_scripts(){
	if(!is_page()){
		?>
		<script type="text/javascript">
		
			docReady=false;
			change=false;
			
			jQuery(document).ready(function(){
				docReady=true;
			});
			
			function create_rich_editor(){
				jQuery("#quick_edit_textarea").cleditor({width:"95%",height:450});
			}
			
			function update_rich_editor(){
				jQuery("#quick_edit_textarea").cleditor()[0].updateFrame();
			}
			
			function quick_save_click(){
				//alert("quick save");
				title=jQuery("#quick_post_title").attr("value");
				content=jQuery("#quick_edit_textarea").val();//contents();
				id=jQuery("#quick_edit_hidden").attr("value");
				//alert(title);
				//alert(content);
				//alert(id);
				jQuery("#quick_close").attr("disabled","disabled");
				
				jQuery.ajax({
					type:"GET",
					url:"<?php echo plugins_url()."/quickPostEdit/savePost.php";?>",
					data:"post_id="+id+"&post_title="+title+"&post_content="+content,
					success:function(){
						//alert("data saved");
						
						change=true;
						
						jQuery("#quick_close").removeAttr("disabled");
						
						//jQuery("#quick_save_success").text("Data saved.");
						//jQuery("#quick_save_success").fadeIn(1000,function(){
							//jQuery("#quick_save_success").fadeOut(1000);
						//});
					}
				});
			}
			
			function quick_close_click(){
				//alert("quick close");
				
				//$("#quick_post_holder").hide();
				
				jQuery("#quick_mask").css({"top":"","left":"","width":"","height":""});
				jQuery("#quick_form").css({"top":"","left":""});
				jQuery("#quick_mask, .window").hide();
				if(change)
					window.location.reload(false);
			}
			
			function quick_edit_click(rowid){
				//alert("quick edit click");
				//alert(formid.id);
				
				nav=navigator.appName;
				
				if(docReady){
					if(/*nav!="Opera"&&*/nav!="Microsoft Internet Explorer"){
						change=false;
						id="#quick_form";
						//alert(id);
						jQuery("#quick_edit_hidden").attr("value",rowid);	
				
						jQuery("#quick_post_title").attr("value","Loading, please wait.");
						jQuery("#quick_edit_textarea").text("Loading, please wait.");
				
						jQuery.ajax({
							type:"GET",
							url:"<?php echo plugins_url()."/quickPostEdit/getPostContent.php";?>",
							data:"post_id="+rowid,
							dataType:"text",
							success:function(postReturn){
								//alert("success");
								//alert(rowid);
								postArray=postReturn.split("|");
								//alert(postArray[0]);
								//alert(postArray[1]);
								jQuery("#quick_post_title").attr("value",postArray[0]);
								jQuery("#quick_edit_textarea").text(postArray[1]);
								update_rich_editor();
							}
						});
				
						maskHeight=jQuery(document).height();
						maskWidth=jQuery(document).width();
						jQuery("#quick_mask").css({"top":0,"left":0,"width":maskWidth,"height":maskHeight});
				
						jQuery("#quick_mask").fadeIn(500);
						jQuery("#quick_mask").fadeTo("slow",0.8);
				
						winh=jQuery(window).height();
						winw=jQuery(window).width();
				
						jQuery(id).css("top",winh/2-jQuery(id).height()/2);
						jQuery(id).css('left', winw/2-jQuery(id).width()/2);
				
						jQuery(id).fadeIn(500);
					
						create_rich_editor();
					}
					else
						alert("You are using unsupported browser. Supported browsers are: Safari, Opera, Google Chrome, Mozilla Firefox and Maxthon.");
				}
			}
			
			/*if(jQuery)
				alert("JQuery loaded");
			else
				alert("jquery not loaded");*/
			
		</script>
		
		<style type="text/css">
			#quick_mask { 
				position:absolute; 
				z-index:9000; 
				background-color:#000; 
				display:none;
			}
			
			#quick_post_holder .window { 
				position:absolute; 
				z-index:9999; 
				padding:20px;
				width:440px; 
				height:200px; 
				display:none;
			}
			
			#quick_post_holder #quick_form { 
				width:375px;  
				height:203px; 
			} 
		</style>
	<?php
	}
}

function quick_front_link($content){
	global $post;
	
	if(!is_page()&&!is_admin()&&current_user_can('manage_options')){
		$content.="<a href='#' onclick='quick_edit_click(".$post->ID.")' style='font-size:10px;'>Quickly edit post</a><br/>";
	}

	return $content;
}

?>