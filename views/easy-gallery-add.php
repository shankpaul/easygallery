<?php 
/*
 * Includes css, javascript and other thirdparty items.
 * 'EASY_GALLERY_PLUGIN_URL' is a globaly defined variable it contains plugin base url
 */
?>

<script language="JavaScript" src="<?php echo EASY_GALLERY_PLUGIN_URL; ?>js/jquery.js"></script>
<script language="JavaScript" src="<?php echo EASY_GALLERY_PLUGIN_URL; ?>thirdparty/sort/jquery.tablesorter.min.js"></script>
<script language="JavaScript" src="<?php echo EASY_GALLERY_PLUGIN_URL; ?>thirdparty/sort/jquery.tablesorter.pager.js"></script>
<script language="JavaScript" src="<?php echo EASY_GALLERY_PLUGIN_URL; ?>js/remodal.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo EASY_GALLERY_PLUGIN_URL; ?>css/remodal.css" />
<script language="javascript">

function putcenter(id)
{
	id.css("top", ( $(window).height() -   id.height() ) / 2);
	id.css("left", ( $(window).width() -  id.width() ) / 2);
}
</script>
<link rel="stylesheet" type="text/css" href="<?php echo EASY_GALLERY_PLUGIN_URL; ?>css/easy-gallery.css" />
<div class="wrap">
	<div  class="easy_album_ico"></div>
	<h2 style="padding-top:20px;  color: #455A64;
	font-weight: bold;">EASY GALLERY</h2>
	<div class="tablenav">    
		<h2>
			<a class=" add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home">Home</a>	 
			<a class=" add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=settings">Settings</a>	<a class=" add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=create">Create Album</a>	 
			<a class=" add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=add">Add Photos</a>	 
		</h2>
	</div>
	<br />   

	<?php 
	$albumid="";
	$nodata=false;
	if(@$_POST['easy_album_create'])
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

			$data['name']=$_POST['easy_album_name'];
			$data['description']=$_POST['easy_album_desc'];
			$data['disabled']=$_POST['easy_album_status'];
			if($fileurl=="")
			{

				$fileurl=EASY_GALLERY_PLUGIN_URL.'images/default-cover.jpg';
			}
			$data['album_cover']=$fileurl;
			$data['create_date']=date('Y-m-d',time());
			$data['create_time']=time();


			$format=array('%s','%s','%s','%s','%s','%s');

			$rows_affected = $wpdb->insert("easy_album",
				$data,
				$format);
			if($rows_affected>0)
			{
				echo '<div class="updated fade below-h2"><p>Album created successfully</p></div>'; 
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
				echo '<div class="updated fade below-h2"><p>Error: Sorry !! News Adding Failed </p></div>'; 
			}
			

  }//End if(@$_POST)
  else if(@$_POST['change_photo'])
  {

  	date_default_timezone_set(get_option('timezone_string')); 

  	if(isset($_FILES['easy_file_edit']))
  	{

	   //date_default_timezone_set(get_option('timezone_string')); 
  		$uploadfiles = $_FILES['easy_file_edit'];		
  		$fileurl=$fileurl_thumb=$filedest="";
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
									$filedest_thumb = $upload_dir['path'] . '/easy_thumb_' . $filename;
									
									$fileurl= $upload_dir['url'] . '/' . $filename;
									$fileurl_thumb= $upload_dir['url'] . '/easy_thumb_' . $filename;


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
		}

		$desc=$_POST['change_desc'];
		$id=$_POST['imageid'];
		$aid=$_POST['albumid'];

		$image_path=$fileurl;	
		$thumb_path=$fileurl_thumb;
		$th=get_option("EasyGallery-thumb-height");
		$tw=get_option("EasyGallery-thumb-width");
		$ih=get_option("EasyGallery-image-height");
		$iw=get_option("EasyGallery-image-width");

		if($filedest!="")
		{
			$image = wp_get_image_editor( $filedest ); 
			if ( ! is_wp_error( $image ) ) 
			{
				$image->resize( $iw, $ih, false );
				$image->save( $filedest );
			}

		}
		if($filedest!="")
		{
			$image = wp_get_image_editor( $filedest ); 
			if ( ! is_wp_error( $image ) ) 
			{
				$image->resize( $tw, $th, false );
				$image->save( $filedest_thumb );
			}
		}

		global  $wpdb;
		if($fileurl!="")
		{

			$sql="update easy_photos set description='$desc',image_path='$image_path',thumb_path='$thumb_path' where id=$id";
		}
		else
			$sql="update easy_photos set description='$desc' where id=$id";
	//echo $sql;
		$rows_affected = $wpdb->query($sql);
		if($rows_affected>0)
		{
			$pageno="";
			if(isset($_REQUEST['paging']))
				$pageno='&paging='.$_REQUEST['paging'];
			wp_redirect( get_option('siteurl').'/wp-admin/admin.php?page=easy-gallery-home&ac=add&albumid='.$aid.$pageno);
		}	
		else
		{
			$pageno="";
			if(isset($_REQUEST['paging']))
				$pageno='&paging='.$_REQUEST['paging'];
			wp_redirect( get_option('siteurl').'/wp-admin/admin.php?page=easy-gallery-home&ac=add&albumid='.$aid.$pageno);
		//echo '<div class="updated fade below-h2"><p>Nothing Updated</p></div>'; 
		}


  }//End if(@$_POST)


  if(isset($_REQUEST['albumid']))
  {
  	$albumid=$_REQUEST['albumid'];

  }
  if(isset($_REQUEST['action']))
  {
  	if($_REQUEST['action']=='status'&&isset($_REQUEST['status'],$_REQUEST['imageid'],$_REQUEST['albumid']))
  	{

  		$status=$_REQUEST['status'];
  		$imageid=$_REQUEST['imageid'];
  		$aid=$_REQUEST['albumid'];
  		if($status==1)
  			$status=0;
  		else
  			$status=1;
  		$rows_affected = $wpdb->query("update easy_photos set disabled=$status where album_id=$aid and id=$imageid"); 

  	}
  	else if($_REQUEST['action']=='delete'&&isset($_REQUEST['imageid'],$_REQUEST['albumid']))
  	{		  		 
  		$imageid=$_REQUEST['imageid'];
  		$aid=$_REQUEST['albumid'];	
  		$rows_affected = $wpdb->query("delete from  easy_photos  where album_id=$aid and id=$imageid");
  	}

  }
  
  ?>
  <div class="postbox ">
  	<h3 class="hndle ui-sortable-handle" style="padding: 0 12px 12px;line-height: 1.4em;"><span>Add Photos</span></h3>
  	<div class="inside" >
  		<form method="POST" id="easy_gallery_name" >
  			<div style="float:left;padding-top:15px;">
  				Selected Album<br />
  				<select style="width:350px;float:left;" name="albumid" id="easy_album_name"  onchange="easy_show_album_cover($(this))">
  					<option value="0" cover="" selected="selected"  >Select Album</option>
  					<?php  
  					$rows=$wpdb->get_results("select album_id,name from easy_album order by name");
  					foreach($rows as $obj){

  						?>
  						<option  <?php if($obj->album_id==$albumid)echo 'selected="selected"'; ?>  value="<?php echo $obj->album_id; ?>"><?php echo $obj->name; ?></option>
  						<?php }?>
  					</select>
  					<?php 
  					if($albumid!="")    
  						$rows=$wpdb->get_results("select album_cover,(select count(id)  from easy_photos b  where a.album_id=b.album_id ) as no_img from easy_album a where a.album_id=$albumid");
  					else
  						$rows = array();
  					foreach($rows as $obj); 
  					?>
  					<?php
  					if($albumid!=""){?>
  					<div class="image_no"><?php echo $obj->no_img; ?> Photos</div> <br style="clear:both;"/>
  					<?php }?>
  					<div style="margin:10px 0;">
  						<?php
  						if($albumid!=""){?>
  						<input type="button" class="np__style np_more_ button button-primary button-large" value="Add Photos" onclick="show_easy_upload()" />
  						<?php } ?>
  					</div>
  				</div>

  				<div style="border-bottom:1px solid #D8D8D8; padding-bottom:10px;" >
  					<div class="easy_create_album_box_cover" style="float:right;">
  						<img  alt="preview" src="<?php
  						if($albumid!=""){ echo $obj->album_cover; }?>" id="easy_cvrprv_preview" />
  					</div><br style="clear:both;" />
  				</div>

  			</form> 

  			<br style="clear:both;" />
  			<?php if($albumid!=""){

  				$rows=$wpdb->get_results("select * from easy_photos where album_id=$albumid");
  				$items=count($rows);
  				if($items > 0)
  				{				

  					$p = new pagination;
  					$p->items($items);
					$p->limit(5); // Limit entries per page
					$p->target(get_permalink()."admin.php?page=easy-gallery-home&ac=add&albumid=$albumid");
					//$p->urlFriendly();
					if(isset($p->paging))
                                            $p->currentPage($_GET[$p->paging]); // Gets and validates the current page
					$p->calculate(); // Calculates what to show
					$p->parameterName('paging');
					$p->nextLabel('');//removing next text
					$p->prevLabel('');//removing previous text
					$p->nextIcon('&#9658;');//Changing the next icon
					$p->prevIcon('&#9668;');//Changing the previous icon
					$p->adjacents(1); //No. of page away from the current page

					if(!isset($_GET['paging'])) 
					{
						$p->page = 1;
					} else {
						$p->page = $_GET['paging'];
					}

					//Query for limit paging
					$limit = "LIMIT " . ($p->page - 1) * $p->limit  . ", " . $p->limit;

				}

				?>
				<div id="pager" style="text-align:right;">
					<?php if($items > 0) echo $p->show(); ?>

				</div>
				<br style="clear:both" />
				<table class="np_table" id="easyphototable" width="100%">
					<thead>
						<th width="2%"  align="center" >No</th>
						<th width="10%" align="left">Image</th>
						<th width="40%" align="left">Description</th>
						<th width="8%"  align="left" >Upload Date</th>
						<th width="6%"  align="left" >action</th>
						<th width="5%"  align="left" >Status</th>        
					</thead>
					<tbody>
						<?php 
						$pageno="";
						$nodata=false;
						if(isset($_REQUEST['paging']))
							$pageno='&paging='.$_REQUEST['paging'];
						if(count($rows)>0)
						{
							$i=0;
							$rows=$wpdb->get_results("select * from easy_photos where album_id=$albumid $limit");
							foreach($rows as $obj) {
								$img_st=$obj->disabled;	
								$show_img_st="Enabled";		
								$img_link_class="np_enabled";

								if($img_st==1)
								{
									$show_img_st="Disabled";		
									$img_link_class="np_disabled";
								}
								?>
								<tr>
									<td align="center" class="rownums"></td>
									<td width="10%"><div class="easy_thumb_nail"> <img src="<?php echo $obj->thumb_path;  ?>" /> </div> </td>
									<td><?php echo $obj->description;  ?></td>
									<td><?php echo date("F j, Y", strtotime($obj->post_date));  ?></td>
									<td>
										<span class="edit"><a title="Edit" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=add&action=edit&imageid=<?php echo $obj->id; ?>&albumid=<?php echo $obj->album_id; echo $pageno; ?>">Edit</a> | </span>
										<span class="trash"><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=add&action=delete&imageid=<?php echo $obj->id; ?>&albumid=<?php echo $obj->album_id;echo $pageno; ?>" onclick="return confirm('Do you want to delete this phot0? ');" >Delete</a></span> 
									</td>


									<td>
										<a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=add&action=status&imageid=<?php echo $obj->id; ?>&status=<?php echo $img_st;?>&albumid=<?php echo $obj->album_id; echo $pageno;?>" class="<?php echo $img_link_class; ?>"><?php echo $show_img_st; ?></a>

									</td>

								</tr>
								<?php }}else { $nodata=true; ?>

								<?php } ?>


							</table>

							<?php }?>
							<?php if($nodata){ ?>
							<div style="margin:auto; text-align:center;padding:5px;border:1px solid #CFCFCF; border-top:none;">No Records Found</div><?php }?>
							<!-- <div class="np_modal" id="easy_upload_modal" ></div> -->

							<!-- --------------modal -->
							<div class="remodal" style="width:60% !important; padding:10px;   " id="modal" role="dialog" aria-labelledby="modal1Title" aria-describedby="modal1Desc">
								<h2>Upload Images</h2>
								<div style="display:none;" id="file_list">
									<!--<input type="file" id="easy_1" class="easy_upd_file" onchange="show_img_preview(this)" />-->
								</div>
								<div id="preview_list"></div>
								<div class="upd_control" id="upd_control">
									<div style="padding:10px; text-align:left;padding-left: 3px;">
										<input type="button" name="" value="Add Files"  style="cursor: pointer; #D51010;"   onclick="easy_show_upload()" checked="false"  class="np__style np_more_ add-new-h2 "  />
										<span style="margin-right: 5px;
										padding: 6px 1px;
										float: left; " id="show_select_all"><input type="checkbox" name="select_all" value="0" id="select_all"  style="margin-right:3px;" />Select All</span>
										<input type="button" style="cursor: pointer;"  value="Delete Files"  onclick="delete_upload()" class="np__style np_cancel_ add-new-h2"   />

										<span style="margin-left:10px; color:#ACACAC;">Maximum Upload Size : <?php echo ini_get("upload_max_filesize"); ?></span>
										<input type="button" style="float:right;" value="Upload Images" class="np__style np_ button button-primary button-large"  onclick="easy_upload()" />
										<input type="button" style="float:right; margin-right:10px;" data-remodal-action="close" class="button button-default button-large" value="Close"  >
										
										<!-- <input type="button" style="float:right;margin-right:15px;"  value="Close" class="np__style np_cancel_" onclick="hide_easy_upload()"   />-->
									</div>
								</div>
							</div>
						</div>
						<!-- ---------------modal ends -->
						



					</div>
				</div>
			</div>
			<br style="clear:both;">
			<?php
			if(isset($_REQUEST['action']))
				if($_REQUEST['action']=='edit'&&isset($_REQUEST['imageid'],$_REQUEST['albumid']))
				{


					$imageid=$_REQUEST['imageid'];
					$aid=$_REQUEST['albumid'];	
					$rows=$wpdb->get_results("select * from easy_photos where album_id=$aid and id=$imageid ");
					foreach($rows as $obj);


					?>
					<script language="javascript">
					putcenter($('#np_edit_form'));
					$('#easy_upload_modal').show();

					</script>

					<form method="post" enctype="multipart/form-data" onsubmit="return easy_validate_edit()" >
						<div class="np_upload_form np_edit_form" id="np_edit_form">
							<h4>Edit Photo <span onclick="hide_easy_upload_edit()">close</span> </h4>
							<div class="upd_content" > 

								<input type="hidden" value="<?php echo $obj->id; ?>" name="imageid" />
								<input type="hidden" value="<?php echo $obj->album_id; ?>" name="albumid" />
								<table width="100%" >
									<tr>
										<td valign="bottom"> <div style="width:300px; text-align:center;margin:auto;overflow:hidden;"><img id="easy_preview_change" style="max-width:100%;" src="<?php echo $obj->image_path; ?>" /></div>

										</td>

									</tr>
									<tr>
										<td>Change Image<br />
											<input type="file" style="float:left;margin:10px; margin-left:0;" name="easy_file_edit[]" value="" id="change_photo"  onchange="easy_preview_photo(this)" />
											<div style="width:80px; margin-left:10px;float:left; display:none;" id="easy_img_preview_box"><img src="" style="max-width:100%;border:1px solid #D4D4D4;padding:1px;" id="easy_preview_change1"  /></div>


										</td>

									</tr>
									<tr>
										<td>Description<br />
											<textarea name="change_desc" style="resize:none; width:100%;max-height:100px;" id="change_desc"><?php echo $obj->description; ?></textarea>
										</td>
									</tr>
									<tr>

									</tr>
								</table>

							</div>
							<div class="upd_control">
								<div style="padding:12px;">
									<input type="submit" name="change_photo" value="Update " class="np__style np_ button button-primary button-large"  /> 
									<input type="button" name="change_photo_cancel" value="Cancel" class="np__style np_cancel_ " onclick="hide_easy_upload_edit()"  />
								</div>
							</div>
						</div>
					</form>
					<?php }?>

					<script language="javascript">
					var image_ulpload=false;
					var remove_list=[];
					$(document).ready(function() 
					{ 
						$("#easyphototable").tablesorter({ 
        // pass the headers argument and assing a object 
        headers: { 
            // assign the secound column (we start counting zero) 
            0: { 
                // disable it by setting the property sorter to false 
                sorter: false 
              }, 
              1: { 
                // disable it by setting the property sorter to false 
                sorter: false 
              } 
              , 
              2: { 
                // disable it by setting the property sorter to false 
                sorter: false 
              } ,
              4: { 
                // disable it by setting the property sorter to false 
                sorter: false 
              } 


            } 
          });

						$('#select_all').click(function(){

							if(document.getElementById('select_all').checked)
							{

			//$('.chk_box').attr('checked',true);
			checkAll( true);
		}
		else
		{
		//	$('.chk_box').attr('checked',false);
		checkAll( false);

	}
	
})

					});
function checkAll( checktoggle)
{
	var checkboxes = new Array(); 
	checkboxes = document.getElementsByTagName('input');

	for (var i=0; i<checkboxes.length; i++)  {
		if (checkboxes[i].type == 'checkbox')   {
			checkboxes[i].checked = checktoggle;
		}
	}
}
function easy_validate_edit()
{
	
	er="";		

	if($('#change_photo').val()!="")
		if(!isValidateImage($('#change_photo'),0))
		{
			er+="Invalid Image Selected\n";
		}
		if(er!="")
		{
			alert("Corrections: \n"+er);
			return false;
		}
		return true;
	}
	function easy_show_album_cover(id)
	{
		if($('#easy_album_name option:selected').val()!=$('#easy_album_name option:first').val())
		{
		//dt=new Date();
		// $('#easy_cvrprv_preview').attr('src',id.find('option:selected').attr('cover')+'?q='+dt.getTime() );
		// $('#easy_cvrprv_preview').fadeIn('slow');
		
		$('#easy_gallery_name').submit();
	}
}
function easy_validate_album()
{
		//return true;
		er="";		
		if($('#easy_album_name').val().trim()=="")
		{
			er+="Please Enter Album Name\n";
		}
		if($('#change_photo').val()!="")
			if(!isValidateImage($('#change_photo'),0))
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
		function easy_preview_photo(input)
		{

			if(isValidateImage($('#change_photo'),1))
			{

				$('#easy_preview_change').fadeOut('fast');
				reader = new FileReader();
				reader.onload = function (e) {
					$('#easy_preview_change').attr('src', e.target.result);
				};

				reader.readAsDataURL(input.files[0]);        

				$('#easy_preview_change').fadeIn('slow');
				//  $('#easy_img_preview_box').fadeIn('slow');
			}
			else
			{

				$('#easy_preview_change').fadeOut('slow');

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
		function easy_show_upload()
		{

			$('.easy_upd_file:last').click();


		}
		function show_img_preview(input)
		{
			k=-1;
			for(i=0;i<input.files.length;i++)
			{	
				if(isValidateImage($(input),1))
				{
					reader = new FileReader();
					reader.onload = function (e) {
						k++;
				//alert(k);
				
				a='<div class="easy_prv" id="prev_'+$(input).attr('id')+'"><div style="float:left; margin:15px 5px 5px 3px;"><input type="checkbox" index="'+k+'" fid='+$(input).attr('id')+'  class="chk_box"  /></div>';
				b='<div class="img"><img src="'+e.target.result+'"  /></div>';
				c='<div class="img_info"><textarea placeholder="Type Your Description.."></textarea></div>';
                //d='<span class="delete" onclick="delete_image($(this))" file="'+$(input).attr('id')+'">delete</span>';
                e='<div class="status" status="0">Upload Pending</div>';
                f='</div>';

                $('#preview_list').prepend(a+b+c+e+f);
			//	   document.getElementById('#preview_list').scrollTop = document.getElementById('#preview_list').scrollHeight;
		};

		reader.readAsDataURL(input.files[i]); 

		$('#show_select_all').fadeIn('fast');
		var d = new Date();
		idno=d.getTime();
		no=$('.easy_upd_file').length;

	}
}
$('#file_list').append('<input type="file" name="files[]" multiple="" id="easy_'+no+idno+'" class="easy_upd_file" onchange="show_img_preview(this)" accept="image/*"  />');
}
function delete_image(id)
{
	file=id.attr('file');
	$('#'+file).remove()
	id.closest('.easy_prv').fadeOut('slow',function(){$(this).remove();}); 
}

var upload_st=false;
function easy_upload()
{
	//url='<?php //echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=upload';
	//url='<?php //echo EASY_GALLERY_PLUGIN_URL; ?>/ajax/handle-upload.php';
	albumid=$('#easy_album_name option:selected').val();
	image_ulpload=true;
	//$.each('.easy_upd_file',function()
	//{
		//alert(albumid);
		count=$('.easy_upd_file').length;	
	//$('#upd_control input[type=]').attr('disabled',true);
	for(i=0;i<count;i++)
	{
		upload_st=true;
		picid=$('.easy_upd_file:eq('+i+')').attr('id');	
		obj=document.getElementById(picid);
		for(j=0;j<obj.files.length;j++)
		{
			flag=true;
			for(x in remove_list)	
			{
				list=remove_list[x].split('^');
				list_id=list[0];
				list_inx=list[1];
				//alert(picid+'='+list_id+'^'+list_inx+'='+j);
				if(list_id==picid && j==list_inx)
					flag=false;
			}
			if(flag)
			{	



				$('.easy_prv').find('.status[status=0]').addClass('load_gif');
				$('.easy_prv').find('.status[status=0]').html('Uploading');

				var file = document.getElementById(picid).files[j]; 
				fd = new FormData();
				fd.append("file[]", file);
				fd.append("desc", $('#prev_'+picid).find('textarea').val());			
				fd.append("albumid", albumid);
				fd.append("picid",picid);
				fd.append("action",'easyupload');

				$.ajax({		
					url:ajaxurl,
					data:fd,
					type: 'POST',
					//async:false,
					processData: false,
					contentType: false,		
					success:function(response)
					{
						if(response!="")
						{
							tmp=response.split('|');
							data=tmp[0].trim();
							cid='prev_'+data;
							$('.easy_prv').each(function(){								
								if($(this).attr('id')==cid)
								{
									if($(this).find('.status').attr('status')==0)
									{	
										$(this).find('.status').removeClass('load_gif');
										$(this).find('.status').html('Completed');
										$(this).find('.status').attr('status','1');
										dsc= $(this).find('textarea').val();
										$(this).find('textarea').parent().html(dsc);
										$(this).find('.chk_box').remove();
										$(this).css('padding-left',20);
									}
								}
							});
							//$('#prev_'+data).find('.status').removeClass('load_gif');
							//$('#prev_'+data).find('.status').html('Completed');
							//$('#prev_'+data).find('.delete').hide('slow');
							$('#'+data).remove();
							upload_st=false;	
						}
					}
				});
}
}



}

}
function hide_easy_upload()
{
	$('#easy_upload_modal').hide();
	$('#easy_upload_window').fadeOut('fast');
	$('#preview_list,#file_list').html('');
	if(image_ulpload)
	{
		//location.reload();
		url=(document.location.href);
		document.location.href=url;
	}
	image_ulpload=false;
}
function hide_easy_upload_edit()
{
	if(!upload_st)
	{
		$('#easy_upload_modal').hide();
		$('#np_edit_form').fadeOut('fast');
		url=(document.location.href).replace('&action=edit','');
		document.location.href=url;
	//$('#preview_list,#file_list').html('');
}
else
	alert("Please Wait..");
}

function show_easy_upload()
{
	// putcenter($('#easy_upload_window'));
	$('#easy_upload_modal').show();
	$('#easy_upload_window').fadeIn('fast');

//	$('#preview_list,#file_list').html('');
var modal_inst = $('#modal').remodal();
modal_inst.open();
var d = new Date();
idno=d.getTime();
no=$('.easy_upd_file').length;
$('#file_list').append('<input type="file" name="files[]" multiple="" id="easy_'+no+idno+'" class="easy_upd_file" onchange="show_img_preview(this)" accept="image/*"  />')   ;
}
function delete_upload()
{
	$('#preview_list input[type=checkbox]:checked').each(function(){
		
		inx=$(this).attr('index');
		fid=$(this).attr('fid');
		obj=document.getElementById(fid);
		
		remove_list.push(fid+'^'+inx);
		$(this).closest('.easy_prv').remove();
		
	});
	if($('.easy_prv').length==0)
	{

		$('#show_select_all').hide();
		document.getElementById('select_all').checked=false;
	}
}

</script>
