<?php 
/*
 * Includes css, javascript and other thirdparty items.
 * 'EASY_GALLERY_PLUGIN_URL' is a globaly defined variable it contains plugin base url
 */
?>
<script language="JavaScript" src="<?php echo EASY_GALLERY_PLUGIN_URL; ?>js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo EASY_GALLERY_PLUGIN_URL; ?>css/easy-gallery.css" />
<div class="wrap">
	<div  class="easy_album_ico"></div>
	<h2 style="padding-top:20px;">EASY GALLERY - Edit Album</h2>
	<div class="tablenav">    
		<h2>
			<a class=" add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home">Home</a>	 
			<a class=" add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=settings">Settings</a>	<a class=" add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=create">Create Album</a>	 
			<a class=" add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=add">Add Photos</a>	 
		</h2>
	</div>
	<br />   

	<?php 
	if(@$_POST['easy_album_edit'])
	{
		date_default_timezone_set(get_option('timezone_string')); 

		$uploadfiles = $_FILES['easy_album_cover'];		
		$fileurl="";
		if (is_array($uploadfiles)) 
		{
			$msg="";
			foreach ($uploadfiles['name'] as $key => $value) 
			{

							 // look only for uploded files
				if ($uploadfiles['error'][$key] == 0) 
				{

					$filetmp = $uploadfiles['tmp_name'][$key];

									//clean filename and extract extension
					$filename = $uploadfiles['name'][$key];

									// get file info
									// @fixme: wp checks the file extension....
					$filetype = wp_check_filetype( basename( $filename ), null );
					$filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
					$filename = $filetitle . '.' . $filetype['ext'];
					$upload_dir = wp_upload_dir();

									/**
									 * Check if the filename already exist in the directory and rename the
									 * file if necessary
									 */
									$i = 0;
									while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) 
									{
										$filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
										$i++;
									}
									$filedest = $upload_dir['path'] . '/' . $filename;
									$fileurl= $upload_dir['url'] . '/' . $filename;


									/**
									 * Check write permissions
									 */
									if ( !is_writeable( $upload_dir['path'] ) ) 
									{
										$msg.='Unable to write to directory %s. Is this directory writable by the server?';
									}

									/**
									 * Save temporary file to uploads dir
									 */
									if ( !@move_uploaded_file($filetmp, $filedest) )
									{
										$msg.="Error, the file $filetmp could not moved to : $filedest ";							 
									}							
						     }//end if
						}//End for each						
			} //end if (is-array)

			$name=$wpdb->escape(trim($_POST['easy_album_name']));
			$desc=$wpdb->escape($_POST['easy_album_desc']);
			$st=$_POST['easy_album_status'];
			$cover=$fileurl;
			$albumid=$_REQUEST['albumid'];
			if($fileurl=="")
			{

				$sql="update easy_album set name='$name',description='$desc',disabled='$st' where album_id=$albumid";
			}
			else
				$sql="update easy_album set name='$name',description='$desc',disabled='$st',album_cover='$fileurl' where album_id=$albumid";
			$ext=$wpdb->get_results("select * from easy_album where name='$name' and album_id!=$albumid");
			if(count($ext)==0)
			{		
				$rows_affected = $wpdb->query($sql);
				if($rows_affected)
				{
					echo '<div class="updated fade below-h2"><p>Album Updated successfully</p></div>'; 
			//image_resize($filedest,242,90,true,)
					if($filedest!="")
					{
						$image = wp_get_image_editor( $filedest ); 
						if ( ! is_wp_error( $image ) ) 
						{
							$h = get_option('EasyGallery-album-cover-height'); 
							$w = get_option('EasyGallery-album-cover-width'); 
							$crop = get_option('EasyGallery-album-cover-crop'); 
							$image->resize( $w, $h, $crop );
							$image->save( $filedest );
						}
					}
				}
				else
				{
					echo '<div class="updated fade below-h2"><p>Nothing is Updated </p></div>'; 
				}
			}
			else
			{
				echo '<div class="updated fade below-h2"><p>Error: Album Name Already Exists. </p></div>';
			}

  }//End if(@$_POST)
  
  ?>
  <?php 
  $albumid="";
  if($_REQUEST['albumid'])
  {
  	$albumid=$_REQUEST['albumid'];
  	$rows=$wpdb->get_results("select * from easy_album where album_id=$albumid");
  	foreach($rows as $obj);
  }
  ?>
  <div class="postbox ">
  	<h3 class="hndle ui-sortable-handle" style="padding: 0 12px 12px;line-height: 1.4em;"><span style="text-transform: capitalize;">Edit <?php echo $obj->name; ?></span></h3>
  	<form method="post" enctype="multipart/form-data" onsubmit="return easy_validate_album();">
  		<table class="inside">
  			<tr>
  				<td>
  					Album Name<br />
  					<input type="hidden" name="albumid" value="<?php echo $albumid; ?>" />
  					<input type="text" name="easy_album_name" value="<?php echo $obj->name; ?>" id="easy_album_name" style="width:450px;"  placeholder="Album Name"/></td>
  				</tr>
  				<tr>

  					<td>  Album Description<br />
  						<textarea name="easy_album_desc" id="easy_album_desc" placeholder="Album Description" style="width:450px; max-width:450px;max-height:150px;"  ><?php echo $obj->description; ?></textarea>
  					</td>
  				</tr>
  				<tr>
  					<td>
  						Album Cover Image<br />
  						<input type="file" style="width:450px;" id="easy_album_cover" name="easy_album_cover[]"  onchange="easy_preview_cover(this);" /> <br />
  						<div class="easy_create_album_box_cover">

  							<img style="" alt="preview" src="<?php echo $obj->album_cover; ?>" id="easy_cvrprv_preview" />
  						</div>
  						<span class="np_help">The cover image is used for show in website frontend gallery. <br />
  							Maximum Upload Size : <?php echo ini_get("upload_max_filesize"); ?></span>
  						</td>
  					</tr>
  					<tr>
  						<td>
  							Album Status<br />
  							<select name="easy_album_status">
  								<option value="0" <?php if($obj->disabled==0){ echo 'selected="selected"';} ?>>Enabled</option>
  								<option value="1" <?php if($obj->disabled==1){ echo 'selected="selected"';} ?>>Disabled</option>
  							</select>
  							<br />

  						</td>
  					</tr>
  					<tr>
  						<td>
  							<input type="submit" value="Update Album" class="np__style np_ button button-primary button-large"  name="easy_album_edit" /> 
  							<a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home" style="text-decoration:none;">
  								<input type="button" style="margin-left:20px;" value="Cancel" class="np__style np_cancel_"  name="easy_album_cancel" /></a>
  							</td>
  						</tr>  

  					</table> 

  				</form> 
  			</div>
  		</div>
  		<script language="javascript">
  		function easy_validate_album()
  		{
		//return true;
		er="";
		
		if($('#easy_album_name').val().trim()=="")
		{
			er+="Please Enter Album Name\n";
		}
		if($('#easy_album_cover').val()!="")
			if(!isValidateImage($('#easy_album_cover'),0))
			{
				er+="Invalid Album Cover Image\n";
			}
			if(er!="")
			{
				alert("Corrections: \n"+er);
				return false;
			}
			return true;
		}
		function easy_preview_cover(input)
		{

			if(isValidateImage($(input),1))
			{
				$('#easy_cvrprv_preview').fadeOut('fast');
				reader = new FileReader();
				reader.onload = function (e) {
					$('#easy_cvrprv_preview')
					.attr('src', e.target.result);
				};

				reader.readAsDataURL(input.files[0]);        

				$('#easy_cvrprv_preview').fadeIn('slow');
			}
			else
			{

				$('#easy_cvrprv_preview').fadeOut('slow');

			}
		}

		function isValidateImage(id,st) {
			filename=id.val();
		//alert(filename);
		var _validFileExtensions = [".jpg", ".jpeg", ".gif", ".png",".bmp"];
		var sFileName =filename;
		if (sFileName.length > 0) 
		{
			var blnValid = false;
			for (var j = 0; j < _validFileExtensions.length; j++)
			{
				var sCurExtension = _validFileExtensions[j];
				if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
					blnValid = true;

					id.closest('div').find('.np_error').hide();
						//alert(filename);
						break;
					}
				}

				if (!blnValid) {

					if(st)
						alert("Sorry, " + sFileName + " is an invalid file, \nAllowed extensions are: " + _validFileExtensions.join(", "));
					id.closest('div').find('.np_error').show();
					return false;
				}
			}

			return true;
		}
		</script>