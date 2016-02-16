<?php require('php/setup.php'); ?>		
<!doctype html>
<html>
	
	<head>
		<meta charset= "utf-8" />
		<title>SocialNetwork | <?=ucwords($user_info['first_name'].' '.$user_info['last_name'])?></title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel="stylesheet" type="text/css" href="css/colorbox.css" />
	</head>
	
	<body>

		<div id="header">
			
			<h1>SocialNetwork</h1>
			
			<a class="menu" href="logout.php">logout</a>
			<a class="menu" id="search_button">search</a>
			<a class="menu name" href="profile.php"><?=ucwords($logged_user['first_name'].' '.$logged_user['last_name'])?></a>
			
			<div id="search">
				
				<input id="search_terms" type="text" placeholder="Search for people by name, location, school, etc..." autocomplete="off"/>	
			
			</div><!--/#search-->
			
			<div id="search_results"><!--ajax--></div>
			
			<div id="search_load" style="display:none"><img src="images/sload.gif" alt="loading"/></div>
			
			<div style="clear:both"></div>
		
		</div><!--/#header-->
		
		<div style="clear:both"></div>
		
		<div id="wrap">
			
			<div id="profile">

				<div id="who">
			
					<h1><?=ucwords($user_info['first_name'].' '.$user_info['last_name'])?></h1>
				
					<img src='<?=$user_pic['medium']?>' alt="profile picture"/>
			
					<?php if ($user_info['quote']) { ?>
						
						<div id="quote">
							
							<span class="quote_text">&#8220;<?=$user_info['quote']?>&#8221;</span>
							
							<span class="quote_src">-<?=$user_info['quote_src']?></span>
						
						</div><!--/#quote-->
					
					<?php } ?>
					
				</div><!--/#who-->

				<div id="about">
					
					<?php if (!$user_id) { ?>
						
						<a class="edit" href="edit.php">Edit</a>
					
					<?php } ?>
					
					<h2>About</h2>
					
					<ul>
						<?php if ($user_info['location']) { ?>
							<li>
								<span class="title">Currently lives</span>
								<span class="value"><?=$user_info['location']?></span>
							</li>
						<?php } ?>
						
						<?php if ($user_info['hometown']) { ?>
							<li>
								<span class="title">Originally from</span>
								<span class="value"><?=$user_info['hometown']?></span>
							</li>
						<?php } ?>
						
						<?php if ($user_info['education']) { ?>
							<li>
								<span class="title">Education</span>
								<span class="value"><?=$user_info['education']?></span>
							</li>
						<?php } ?>
					
						<?php if ($user_info['work']) { ?>
							<li>
								<span class="title">Work/Career</span>
								<span class="value"><?=$user_info['work']?></span>
							</li>
						<?php } ?>
						
						<?php if ($user_info['relationship']) { ?>
							<li>
								<span class="title">Relationship</span>
								<span class="value"><?=$user_info['relationship']?></span>
							</li>
						<?php } ?>
						
						<?php if ($user_info['gender']) { ?>
							<li>
								<span class="title">Gender</span>
								<span class="value"><?=($user_info['gender']=='m'?'Male':'Female')?></span>
							</li>
						<?php } ?>
						
						<?php if ($user_info['dob']) { ?>
							<li>
								<span class="title">Birthday</span>
								<span class="value"><?=date('F d, Y', strtotime($user_info['dob']))?></span>
							</li>
						<?php } ?>
						
						<?php if (!$user_id) { ?>
							<li>
								<span class="title">Email</span>
								<span class="value"><?=$user_info['email']?> <i>(private)</i></span>
							</li>
						<?php } ?>
						
						<?php if ($user_info['about']) { ?>
							<li>
								<span class="title">About</span>
								<span class="value"><?=$user_info['about']?></span>
							</li>
						<?php } ?>
					</ul>
					
				</div><!--/#about-->
				
				<div id="pics">
						
					<?php if (!$user_id) { ?>
						
						<form id="upload_profile_images" enctype="multipart/form-data" method="POST">
							<input class="add_pic" type="file" name="profile_image"/>
						</form>
					
					<?php } ?>
					
					<h2>Pictures</h2>
					
					<div id="loading" style="display:none"><br/><img src="images/loadingbar.gif" alt="loading"/><br/><br/></div>
					
					<?php
					$i = 0;
					foreach ($cols as $n) {
					?>
						<div class="pic_col">
							
							<?php 
							for ($j=0; $j<$n; $j++) {
								$title = ''; 
								if (!$user_id)  
									$title = "<a href='profile.php?set_pic={$all_user_pics[$i]['id']}'>Set as profile picture</a><br/><a href='profile.php?delete_pic={$all_user_pics[$i]['id']}'>Delete picture</a>"; ?>
								
								<a class="cbox_profile_pic" href="<?=$all_user_pics[$i]['large']?>" title="<?=$title?>">
									<img style="width:<?=$max_width?>" src="<?=$all_user_pics[$i][$pic_size]?>" alt="profile pic"/>
								</a>
								
								<?php $i++; ?>
							
							<?php } ?>
						
						</div><!--/.pic_col-->
						
					<?php } ?>
				
				</div><!--/#pics-->
			
			</div><!--/#profile-->
			
			<div id="wall">
			
				<div id="new_post">
			
					<form action="profile.php<?=($user_id?"?id={$user_id}":'')?>" enctype="multipart/form-data" method="POST" autocomplete="off">
							
						<textarea id="comment_txt" name="text" placeholder="Post to <?=(!$user_id?'your':ucfirst($user_info['first_name'])."'s")?> wall..."></textarea>
							
						<div id="post_options">
							
							<div id="buttons">
								
								<a id="add_url_button">Add a URL</a> or 
								<a id="add_img_button">Add a picture</a>
							
							</div><!--/#buttons-->
						
							<div id="add_url">
								
								<input id="url" type="text" name="url" placeholder="Enter a URL you'd like to share..." autocomplete="off"/>
								<a id="cancel_url">cancel</a>
							
							</div><!--/#add_url-->
							
							<div id="add_img">
								
								<input type="file" name="image" id="img"/>
								<a id="cancel_img">cancel</a>
							
							</div><!--/#add_img-->
							
							<div id="preview_url"></div>
						
							<div id="preview_img"><img style="display:none" id="pre_img" src="#" alt="upload preview"/></div>
						
							<div id="submit_post">
								
								<input type="submit" name="post" value="Post" />
								<a id="cancel_new">cancel</a>
							
							</div><!--/#submit_post-->
							
							<div style="clear:both"></div>
						
						</div><!--/#post_options-->
					
					</form>
				
				</div><!--/new_post-->
				
				<?php if ($current_pg != 1) { ?>
					<div class="page_nav">Page <?=$current_pg?></div>
				<?php } ?>
				
				<?php
				$urls = array();
				foreach($wall as $post) {
				?>
					<div class="post">
							
						<div class="post_author">
							
							<img class="post_author_pic" src="<?=$post['author_pic']?>" alt="post author"/>
							
							<span class="post_author_name">
								
								<a href="profile.php?id=<?=$post['author_id']?>"><?=ucwords($post['author_name'])?></a>
							
								<span class="post_date"><?=date('M j, Y g:ia', strtotime($post['date']))?></span>
							
							</span>

						</div><!--/.post_author-->
							
						<div class="post_body">
							
							<?php if ($post['text']) { ?>
								
								<div class="post_text"><?=$post['text']?></div>
							
							<?php } if ($post['url']) { ?>
								
								<div id="url_post_<?=$post['id']?>" class="post_url"><img src="images/loading.gif" alt="loading"/></div>
								
								<?php $urls[] = array('div'=>"url_post_{$post['id']}", 'url'=>"{$post['url']}"); ?>
							
							<?php } if ($post['medium_image']) { ?>
								
								<div class="post_image">
									
									<a class="cbox_wall_pic" href="<?=$post['large_image']?>">
										<img src="<?=$post['medium_image']?>" alt="wall pic"/>	
									</a>
								
								</div><!--/.post_image-->
							
							<?php } ?>	
						
						</div><!--/.post_body-->
							
						<div class="add_comment">
							
							<form method="POST">
								
								<textarea id="comment<?=$post['id']?>" name="comment" placeholder="Write a comment..." maxlength=255 onkeydown="if(event.keyCode==13)addComment(<?=$post['id']?>)"></textarea>
								
								<input type="hidden" id="post_id<?=$post['id']?>" name="post_id" value="<?=$post['id']?>" />
							
							</form>
						
						</div><!--/.add_comment-->
							
						<div id="post_comments<?=$post['id']?>">
							
							<?php foreach ($post['comments'] as $comment) { ?>
								
								<div class="comment">
									
									<img class="comment_author_pic" src="<?=$comment['author_pic']?>" alt="comment author"/>

									<div class="comment_body">
										
										<a href="profile.php?id=<?=$comment['author_id']?>"><?=ucwords($comment['author_name'])?></a>
									
										<span class="comment_date"><?=date('M j, Y g:ia', strtotime($comment['date']))?></span>
										
										<div class="comment_text"><?=$comment['comment']?></div><!--/.comment_body-->
										
									</div><!--/.comment_body-->

									<div style="clear:both"></div>
								
								</div><!--/.comment-->
							
							<?php } ?>
							
						</div><!--/#post_comments{id}-->
							
					</div><!--/.post-->
				
				<?php } ?>
				
				<?php if ($wall_num > 0) { ?>
					
					<div class="page_nav">
						
						Showing <?=$first?> - <?=$last?> of <?=$wall_num?>
						
						<br/>
						
						<?php if ($current_pg > 1) { ?>
							<a href='profile.php<?=($user_id?"?id={$user_id}&":'?')?>p=<?=($current_pg-1)?>'>Previous</a>
						<?php } ?>
						
						<?php if (!$last_pg) { ?>
							<a href='profile.php<?=($user_id?"?id={$user_id}&":'?')?>p=<?=($current_pg+1)?>'>Next</a>
						<?php } ?>
					
					</div><!--/.page_nav-->
				
				<?php } ?>
				
			</div><!--/#wall-->
			
			<div style='clear:both'></div>
		
		</div><!--/#wrap-->
  
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
		<script src="js/jquery.colorbox-min.js" type="text/javascript"></script>
		<script src="js/functions.js" type="text/javascript"></script>
		
		<script type="text/javascript">
			<?php
				foreach ($urls as $url)
					echo "embedly('{$url['div']}', '{$url['url']}');";
			?>
		</script>
		
	</body>
</html>