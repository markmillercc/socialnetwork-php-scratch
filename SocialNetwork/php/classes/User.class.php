<?php
/***
* class: User
* desc: Handles all user actions and data retrieval 
**/
class User {
	
	private $db;
	private $id;
	
	/*
	* CONSTRUCTOR ($id, $db) 
	*	- $id is the id of logged in user
	*	- $db is an instance of class PdoMySql
	***/
	public function __construct($id, $db) {
		$this->id = $id;
		$this->db = $db;
	}
	
	/*
	* FUNCTION getInfo($user_id=false, $fields=false) 
	*	- fetches user info for $user_id; if $user_id = false, $user_id becomes current user
	*	- $fields can be:
	*		1.) false = return all user fields (default): return array
	*		2.) an array = return fields listed in array: return array
	*		3.) a string = return single field defined by string: return single field
	***/
	public function getInfo($user_id=false, $fields=false) {
		
		if (!$user_id) 
			$user_id = $this->id;
		
		if (!$fields) {
			$select = '
				id 
				,fb_id
				,email
				,first_name
				,last_name
				,picture
				,dob
				,gender
				,hometown
				,location
				,quote
				,quote_src
				,education
				,work
				,relationship
				,about
				,reg_date
			';
		}
		elseif(is_array($fields)){
			$select = $comma = '';
			foreach ($fields as $f) {
				$select .= $comma.$f;
				$comma = ',';
			}
		}
		else $select = $fields;
		
		$sql = "SELECT {$select} FROM users WHERE id = {$user_id}";
		
		Debug('Get user info', $sql);
		
		$result = $this->db->pdo_query($sql, false, 'select_one', 'An error occurred while trying to fetch user information.');
		
		if ($select == $fields) 
			return $result[$fields];
		else return $result;
	}
	
	/*
	* FUNCTION getProfilePicture()
	*	- get user's main profile picture
	***/
	public function getProfilePicture($user_id=false) {
		
		$pic_id = $this->getInfo($user_id, 'picture');
		
		if (!is_null($pic_id)) {
			$sql = "
			SELECT small, medium, large 
			FROM images
			WHERE id = {$pic_id}
			;";
			
			Debug('Get profile picture', $sql);
			
			$result = $this->db->pdo_query($sql, false, 'select_one', 'Error getting profile picture.');
		}
		elseif (!is_null($this->getInfo($user_id, 'fb_id'))) {
			// If logged in w/Facebook, and no set profile pic, use FB pic
			$result['small'] = $result['medium'] = $result['large'] = "http://graph.facebook.com/{$this->getInfo($user_id, 'fb_id')}/picture?type=large"; 
		}
		else {
			// If no profile picture set, return default pic
			$result['small'] = $result['medium'] = $result['large'] = 'uploads/default/nopic.jpg';
		}
		return $result;
	}
	
	/*
	* FUNCTION getAllImages()
	*	- Get all images linked to user profile
	*	- If $profile_pic=false, exclude user profile picture
	***/
	public function getAllImages($user_id=false, $profile_pic=false) {
		if (!$user_id) 
			$user_id = $this->id;
		
		$where = '';
		if (!$profile_pic)
			if ($profile_pic_id = $this->getInfo($user_id, 'picture'))
				$where = "WHERE i.id != {$profile_pic_id}";
		
		$sql = "
		SELECT i.id, i.small, i.medium, i.large 
		FROM images i
		INNER JOIN profile_images pi ON pi.image_id = i.id
			AND pi.user_id = {$user_id}
		{$where}
		ORDER BY i.id DESC
		;";

		Debug('Get all profile images', $sql);
		
		return $this->db->pdo_query($sql, false, 'select_many', 'Error getting profile images.');
	}
	
	/*
	* FUNCTION setProfilePicture($file)
	*	- confirm image exists and belongs to user
	*	- update user picture
	***/
	public function setProfilePicture($img_id) {
		// Confirm image exists and belongs to user
		$sql = "
		SELECT pi.image_id FROM profile_images pi 
		INNER JOIN images i ON i.id = pi.image_id
		WHERE pi.image_id = {$img_id}
			AND pi.user_id = {$this->id}
		;";
		
		Debug('Confirm image for profile picture', $sql);
		
		if ($this->db->pdo_query($sql, false, 'select_one', 'Error confirming image.')) {
			$sql = "
			UPDATE users SET picture = {$img_id}
			WHERE id = {$this->id}
			;";
			
			Debug('Update profile picture', $sql);
			
			return $this->db->pdo_query($sql, false, 'alter', 'Error updating profile picture.');	
		}
		
		return false;
	}
	
	/*
	* FUNCTION addProfileImage($file)
	*	- process image
	*	- link image to profile
	*	- if user has no main profile pic and is not using FB, use this pic
	***/
	public function addProfileImage($file) {
		if ($img_id = $this->processImage($file)) {
			$sql = "
			INSERT INTO profile_images (image_id, user_id)
			VALUES ({$img_id}, {$this->id})
			;";
			if ($this->db->pdo_query($sql, false, 'alter', 'Error adding image.')) {
				if (is_null($this->getInfo(false, 'picture')) && is_null($this->getInfo(false, 'fb_id'))) 
					if ($this->setProfilePicture($img_id))
						return true;
				return true;
			}
		}
		return false;
	}
	
	/*
	* FUNCTION deleteProfileImage($id)
	*	 - Delete sml, med, lrg image from uploads dir
	* 	 - Delete rows from images and profile_images
	***/
	public function deleteProfileImage($id) {
		$sql = "SELECT * FROM images WHERE id = {$id};";
		$i = $this->db->pdo_query($sql, false, "select_one", "admin");
		
		unlink($i['small']);
		unlink($i['medium']);
		unlink($i['large']);
		
		$sql = "
		DELETE i, pi
		FROM images i
		INNER JOIN profile_images pi ON pi.image_id = {$id}
		WHERE i.id = '{$id}'
		;";
		
		Debug('delete image', $sql);
		
		return $this->db->pdo_query($sql, false, "alter", "Error deleting image.");
	}
		
	/*
	* FUNCTION updateProfile($data)
	*	- update user profile according to data array
	***/
	public function updateProfile($data) {
		if (!is_array($data)) return false;
		if (empty($data)) return true;
		
		$comma = '';
		$sql = "UPDATE users SET ";
		$params = array();
		
		foreach($data as $f => $v) {
			$sql .= "{$comma}{$f} = :{$f} ";
			$comma = ',';
			$params[":{$f}"] = $v;
		}
		
		$sql .= "WHERE id = {$this->id};";
		
		Debug('Update user sql', $sql);
		
		return $this->db->pdo_query($sql, $params, "alter", "Error updating user");
	}
	
	/*
	* FUNCTION postToWall($user_id=false, $text, $url=false, $img=false)
	*	- get id of wall_owner and post text, image, and/or url
	*	- upload image to DB (if applicable)
	* 	- insert post data
	***/
	public function postToWall($user_id=false, $text=false, $url=false, $img=false) {
		if (!$user_id) // Id of wall_owner
			$user_id = $this->id;
		
		if (!$text && !$url && !$img) return false;
		
		$data = $params = array();
		
		// Standard, required data
		$data['wall_owner'] = $user_id;
		$data['author'] = $this->id;
		$data['date'] = date("Y-m-d H:i:s");
		
		// If image, process and add to data array
		if ($img) {
			if (!$img_id = $this->processImage($img)) {
				echo "Failed to upload image";
				return false;
			}
			else 
				$data['picture'] = $img_id;
		}
		
		// If text, add to data array
		if ($text) $data['text'] = strip_tags($text);
		
		// If URL, add to data array
		if ($url) $data['url'] = $url;
		
		$comma = $set = $values = '';
		foreach ($data as $name => $value) {
			$set .= $comma.$name;
			$values .= $comma.':'.$name;
			$params[":{$name}"] = $value;
			$comma = ' ,';
		}
		
		$sql = "INSERT INTO wall_posts ({$set}) VALUES ({$values});";
		
		Debug('new post data array', $data);
		Debug('new post insert params', $params);
		Debug('new post sql', $sql);
		
		return $this->db->pdo_query($sql, $params, "alter", "Error creating post.");	
	}
	
	/*
	* FUNCTION countWall($user_id=false)
	*	- count wall posts, return number
	***/
	public function countWall($user_id=false) {
		if (!$user_id)
			$user_id = $this->id;
		
		$sql = "SELECT COUNT(id) as c FROM wall_posts WHERE wall_owner = {$user_id};";
		
		Debug('count wall posts', $sql);
		
		$result = $this->db->pdo_query($sql, false, "select_one", "Error counting wall posts.");
		
		return $result['c'];
	}
	
	/*
	* FUNCTION getWall($user_id=false, $start=false, $qty=false)
	*	- get wall posts for $user_id
	*	- $start and $qty control pagination
	*		defaults to display all posts
	***/
	public function getWall($user_id=false, $start=false, $qty=false) {
		if (!$user_id)
			$user_id = $this->id;
		
		$start = (!$start ? 0 : $start);
		$limit = (!$qty ? '' : "LIMIT {$start}, {$qty}");
		
		$sql = "
		SELECT 
			wp.id
			,wp.author AS author_id
			,CONCAT_WS(' ', u.first_name, u.last_name) AS author_name
			,IF(wp.picture IS NULL, 0, i.medium) AS medium_image
			,IF(wp.picture IS NULL, 0, i.large) AS large_image
			,IFNULL(wp.text, 0) AS text
			,IFNULL(wp.url, 0) AS url
			,wp.date
		FROM wall_posts wp 
		INNER JOIN users u ON u.id = wp.author
		LEFT JOIN images i ON i.id = wp.picture
		WHERE wp.wall_owner = {$user_id}
		ORDER BY wp.date DESC
		{$limit}
		;";
		
		Debug('Get wall sql', $sql);
		
		$result = $this->db->pdo_query($sql, false, "select_many", "Error getting wall posts.");
		
		// Add author profile pic to post array
		// Get comments and add to post array
		foreach($result as $i => $r) {
			$auth_pic = $this->getProfilePicture($r['author_id']);
			$result[$i]['author_pic'] = $auth_pic['small'];
			$result[$i]['comments'] = $this->getComments($r['id']);
		}
		return $result;
	}

	/*
	* FUNCTION getComments($post_id)
	*	- get all comments for $post_id
	*	- get commenter profile pic
	***/
	public function getComments($post_id) {
			
		$sql = "
		SELECT
			c.id
			,c.author AS author_id
			,CONCAT_WS(' ', u.first_name, u.last_name) AS author_name
			,c.comment
			,c.date
		FROM comments c
		INNER JOIN users u ON u.id = c.author
		WHERE c.post_id = {$post_id}
		ORDER BY c.date DESC
		;";
		
		Debug('Get comments sql', $sql);
		
		$result = $this->db->pdo_query($sql, false, "select_many", "Error getting comments.");
		
		foreach($result as $i => $r) {
			$auth_pic = $this->getProfilePicture($r['author_id']);
			$result[$i]['author_pic'] = $auth_pic['small'];
		}
		
		return $result;
	}
	
	/*
	* FUNCTION postComment($post_id)
	*	- post a comment to $post_id
	***/
	public function postComment($post_id, $comment) {
		
		$data = $params = array();
		
		$data['post_id'] = $post_id;
		$data['comment'] = strip_tags($comment);
		$data['author'] = $this->id;
		$data['date'] = date('Y-m-d H:i:s');
		
		$comma = $set = $values = '';
		foreach ($data as $name => $value) {
			$set .= $comma.$name;
			$values .= $comma.':'.$name;
			$params[":{$name}"] = $value;
			$comma = ' ,';
		}
		
		$sql = "INSERT INTO comments ({$set}) VALUES ({$values});";
		
		Debug('new comment data array', $data);
		Debug('new comment insert params', $params);
		Debug('new comment sql', $sql);
		
		return $this->db->pdo_query($sql, $params, "alter", "Error posting comment.");
	}
	
	/*
	*	FUNCTION search($terms)
	*		- Search for users by first name, last name, location, education, or work
	*		- Exclude user dev@debug.site (id=1)
	***/
	public function search($terms) {

		if (!$terms) return false;
		
		$terms = "%".trim($terms)."%";
	
		$sql = "
		SELECT
			id
			,first_name
			,last_name
			,location
		FROM users
		WHERE (CONCAT_WS(' ', first_name, last_name) LIKE :terms
			OR hometown LIKE :terms
			OR location LIKE :terms
			OR education LIKE :terms
			OR work LIKE :terms)
			AND id != '1'
		;";
		
		Debug('Search', $sql);
	
		$params = array(":terms" => $terms);

		$result = $this->db->pdo_query($sql, $params, 'select_many', 'Error while searching.');

		if ($result)
			foreach ($result as $i => $r)
				$result[$i]['picture'] = $this->getProfilePicture($r['id']);
		
		return $result;
	}
	
	/*
	* FUNCTION processImage($file) 
	*	- Create Small copy of image
	*	- Create Medium copy of image
	*	- Create Large copy of image
	*	- Upload both to Uploads directory
	*	- Insert paths into DB
	***/
	public function processImage($file) {
		Debug();
        $error_alert = '';
		$uploaded_files = array();
		$temp_uploads_dir = 'tmp_uploads';
		$perm_uploads_dir = 'uploads';
        
        Debug("Uploaded file", $file);
        
		if (is_uploaded_file($file['tmp_name'])) {
			if ($file['size'] > 6291456) // Check file size
				$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- file must be less than 6MB.<br/>";
			
			else if ($file['size'] <= 0) // Check if file is empty
				$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- file is empty.<br/>";
			
			else if (!$img_data = getimagesize($file['tmp_name'])) // Check if file is an image
				$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- file is not an image.<br/>";
			
			else {
				// Get/check extension
				switch ($img_data['mime']) {
					case 'image/jpeg':
						$ext = 'jpg';
						break;
					case 'image/gif':
						$ext = 'gif';
						break;
					case 'image/png':
						$ext = 'png';
						break;
					default:
						$ext = FALSE;
				}
				
				if (!$ext) {
					$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- image file must have extension JPG, PNG, or GIF.<br/>";
				}
				elseif ($error_alert == '') {
					$sizes = array(
						array("name"=>"sml", "width"=>"100", "height"=>"100"),
						array("name"=>"med", "width"=>"400", "height"=>"400"),
						array("name"=>"lrg", "width"=>"800", "height"=>"800")
					);
					foreach ($sizes as $size) {
						$name = $size['name'];
						$max_width = $size['width'];
						$max_height = $size['height'];
						
						$tmp_file_name = $temp_uploads_dir.'/'.uniqid().'.'.$ext;
					
						$width_orig = $img_data[0];
						$height_orig = $img_data[1];
						
						if ($width_orig > $max_width || $height_orig > $max_height) {
							@$ratio_orig = $width_orig/$height_orig;
							
							if ($max_width/$max_height > $ratio_orig) 
								$max_width = $max_height*$ratio_orig;
							else 
								$max_height = $max_width/$ratio_orig;
						}
						else {
							$max_width = $width_orig;
							$max_height = $height_orig;
						}
						if (@!$image_p = imagecreatetruecolor($max_width, $max_height))
							$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- processing error.<br/>";
						else {
							switch ($ext) {
								case 'jpg':
									$image = imagecreatefromjpeg($file['tmp_name']);
									$imageresample = imagecopyresampled($image_p, $image, 0, 0, 0, 0, $max_width, $max_height, $width_orig, $height_orig);
									$imagecopy = imagejpeg($image_p, $tmp_file_name);
								break;
								case 'gif':						
									$image = imagecreatefromgif($file['tmp_name']);
									$imageresample = imagecopyresampled($image_p, $image, 0, 0, 0, 0, $max_width, $max_height, $width_orig, $height_orig);
									$imagecopy = imagegif($image_p, $tmp_file_name);					
								break;
								case 'png':
									$image = imagecreatefrompng($file['tmp_name']);
									$imageresample = imagecopyresampled($image_p, $image, 0, 0, 0, 0, $max_width, $max_height, $width_orig, $height_orig);
									$imagecopy = imagepng($image_p, $tmp_file_name);
								break;
								default:
									$image = FALSE;
									$imageresample = FALSE;
									$imagecopy = FALSE;
							}
							
							// Verify resample success
							if (!$image || !$imageresample || !$imagecopy)
								$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- processing error.<br/>";
							else {
								// On success, create random file name and upload to Temp Uploads dir
								// Store new name in $uploaded_files[] 
								$rand_num = rand(10000, 99999);
								$new_file_name = $size['name'].'_'.$rand_num.uniqid().'.'.$ext;
								if (rename($tmp_file_name, $perm_uploads_dir.'/'.$new_file_name))
									$uploaded_files["{$size['name']}"] = $perm_uploads_dir.'/'.$new_file_name;
								else
									$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- processing error.<br/>";
							}
							imagedestroy($image_p);
						}
					}
                  
					if ($error_alert == '') {
						$sql = "
						INSERT INTO images (small, medium, large) 
						VALUES('{$uploaded_files['sml']}', '{$uploaded_files['med']}', '{$uploaded_files['lrg']}')
						;";
						if ($this->db->pdo_query($sql, false, 'alter', 'Error uploading image.'))
							$img_id = $this->db->last_insert_id();
						else
							$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."to database.<br/>";
					}
				}
			}
		}
		if ($error_alert == '')
			return $img_id;
		else {
			echo $error_alert;
			return false;
		}
	}
}
?>