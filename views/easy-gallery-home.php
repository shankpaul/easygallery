<?php 
/*
 * Includes css, javascript and other thirdparty items.
 * 'EASY_GALLERY_PLUGIN_URL' is a globaly defined variable it contains plugin base url
 */
?>

<script language="JavaScript" src="<?php echo EASY_GALLERY_PLUGIN_URL; ?>js/jquery.js"></script>
<script language="JavaScript" src="<?php echo EASY_GALLERY_PLUGIN_URL; ?>js/remodal.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo EASY_GALLERY_PLUGIN_URL; ?>css/easy-gallery.css" />
<link rel="stylesheet" type="text/css" href="<?php echo EASY_GALLERY_PLUGIN_URL; ?>css/remodal.css" />


<div class="wrap">
	<div  class="easy_album_ico"></div>
	<h2 style="padding-top:20px;  color: #455A64;
	font-weight: bold;">EASY GALLERY <span class="version alignright" >Version 2.0.0</span></h2>
	<div class="tablenav">    
		<h2>
			<a class=" add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home">Home</a>	 
			<a class=" add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=settings">Settings</a>	<a class=" add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=create">Create Album</a>	 
			<a class=" add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=add">Add Photos</a>	 
		</h2>
	</div>
	<br />  
	<?php 

	if(isset($_REQUEST['action']))
	{
		if($_REQUEST['action']=="delete")
		{
			$albumid=$_REQUEST['albumid'];
			$rows_affected = $wpdb->query("delete from easy_album where album_id=$albumid");
		}
	}
	?>

	
	<?php 
	$rows=$wpdb->get_results("select *,(select count(id)  from easy_photos b  where a.album_id=b.album_id ) as no_img from easy_album a"); 
	$items=count($rows);
	$limit = '';
	if($items > 0)
	{				

		$p = new pagination;
		$p->items($items);
					$p->limit(12); // Limit entries per page
					$p->target(get_permalink().'admin.php?page=easy-gallery-home');
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
				<div class="easy_album_list">
					<?php 
					$rows=$wpdb->get_results("select *,(select count(id)  from easy_photos b  where a.album_id=b.album_id ) as no_img from easy_album a order by album_id desc $limit"); 

					if(count($rows)==0)
						echo '<div class="error fade below-h2" style="width:300px;"><p>No Album Found. Please create an album first</p></div>';

					$pageno="";
					if(isset($_REQUEST['paging']))
					{
						$pageno='&paging='.$_REQUEST['paging'];
						if(count($rows)==0){
							wp_redirect( get_option('siteurl').'/wp-admin/admin.php?page=easy-gallery-home');
						}
					}

					foreach($rows as  $obj){
						?>
						<div class="easy_each_album">
							<div class="easy_img_box">
								<?php if($obj->disabled==1){ ?>
								<span class="status_box">Disabled</span>
								<?php } ?>
								<h3 style="text-transform: capitalize;"><?php if(strlen($obj->name)>35) echo substr($obj->name,0,35).'..'; else echo $obj->name; ?> <span> [ID: <?php  echo $obj->album_id; ?>]</span></h3>
								<img src="<?php echo $obj->album_cover; ?>"  />
							</div>
							<div class="easy_img_desc">
								
								<div class="album_control">
									<a class="easy_control_btn code" title="Create shortcode for this album" data-image-src="<?php echo $obj->album_cover; ?>" onClick="initShortCodeWindow($(this))" data-id="<?php echo $obj->album_id; ?>" data-name="<?php echo $obj->name; ?>">&lt;/&gt;</a>
									<a class="easy_control_btn delete" title="Delete this album" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&action=delete&albumid=<?php echo $obj->album_id.$pageno; ?>" onclick="return confirm('Do you want to delete this album ? ');">Delete</a>
									<a class="easy_control_btn edit" title="Edit this album" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=albumedit&albumid=<?php echo $obj->album_id; ?>">View/Edit</a>
									<a class="easy_control_btn add" title="Add photos to this album" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=easy-gallery-home&ac=add&albumid=<?php echo $obj->album_id; ?>">+ Add</a>
									<div class="image_count"  title="<?php echo $obj->no_img; ?> Photos"><?php echo $obj->no_img; ?></div>
								</div>
								<br style="clear:both;" />
							</div>
						</div>
						<?php }?>
						<br style="clear:both;" />
						<?php if($items > 0) echo $p->show(); ?>
					</div>
					<table width="100%" class="np_table" id="straymanage" style="display:none;">
						<thead>
							<th scope="col" width="3%"><div align="center">No</div></th>
							<th scope="col" width="20%" align="left" >Album Name</th>
							<th scope="col" width="40%" align="left" >Description</th>
							<th scope="col"  width="10%" align="left">Created On</th>
							<th scope="col" width="7%" align="left">No of Image</th>
							<th scope="col" width="6%" align="left">Status</th>
						</thead>

						<tbody>

							<?php
							$rows = $wpdb->get_results("select * from easy_album ");
							$no=0;
							if(count($rows)>0)
							{
								foreach($rows as $obj){
									$cat_st=$obj->disabled;	
									$show_cat_st="Enabled";		
									$cat_link_class="np_enabled";
									if($cat_st==1)
									{
										$show_cat_st="Disabled";		
										$cat_link_class="np_disabled";
									}
									?>
									<tr>
										<td align="center" width="7%"><?php echo ++$no; ?></th>
											<td><?php echo $obj->title; ?>					  <div class="row-actions">
												<span class="edit"><a title="Edit" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=news-poster&amp;ac=edit&newsid=<?php echo $obj->news_id; ?>">Edit</a> | </span>
												<span class="trash"><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=news-poster&amp;ac=news-poster&npaction=delete&newsid=<?php echo $obj->news_id; ?>&status=<?php echo $cat_st;?>" >Delete</a></span> 
											</div>
										</td>
										<td> <?php echo $obj->category_name; ?>	</td>

										<td> <?php echo $obj->post_date; ?>	</td>
										<td> <?php echo $obj->expiry_date; ?>	</td>
										<td><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=news-poster&amp;ac=news-poster&npaction=update&newsid=<?php echo $obj->news_id; ?>&status=<?php echo $cat_st;?>" class="<?php echo $cat_link_class; ?>"><?php echo $show_cat_st; ?></a></td>
									</tr>
									<?php }}else{ ?>
									<tr><td colspan="6" align="center">No records available.</td></tr>
									<?php }?>
								</tbody>
							</table>
						</div>

						<div class="remodal" style="width:40% !important; padding-top:0;padding-left:0;padding-right:0; " id="modal" role="dialog" aria-labelledby="modal1Title" aria-describedby="modal1Desc">
							<div class="main-div">
								<h2> &lt;/&gt; Create Shortcode</h2>
								<h3 id="gallery-name">Untitled</h3>
								<img id="album-image" src="http://localhost/wordpress/wp-content/uploads/2015/10/bg_image.jpg">
								<div class="remodal-content" >
									<table style="padding:10px; width:100%;">
										<tr>
											<td width="30%" align="left"><label>View: &nbsp; </label>
												<select id="gallery-view" style="width:110px;" onChange="toggleAlbumMode($(this))">
													<option value="image">Image</option>
													<option value="album">Album</option>
												</select>
											</td>
											<td width="30%" >
												<label>Album: </label>
												<select id="gallery-album" disabled="disabled" style="width:110px;">
													<option value="" cover="" selected="selected"  >Select Album</option>
													<?php  
													$rows=$wpdb->get_results("select album_id,name from easy_album order by name");
													foreach($rows as $obj){
														?>
														<option  <?php if($obj->album_id==$albumid)echo 'selected="selected"'; ?>  value="<?php echo $obj->album_id; ?>"><?php echo $obj->name; ?></option>
														<?php } ?>
													</select>
												</td>
												<td width="30%" align="right"><label>Limit: </label>
													<input type="text" style="width:110px;" id="gallery-limit" value="8" >
												</td>
											</tr>
											<tr>
												<td width="30%" align="left"><label>Order: </label>
													<select id="gallery-order" style="width:110px;">
														<option value="asc">Ascending</option>
														<option value="desc">Descending</option>
													</select>
												</td>
												<td width="30%"><label>Theme: </label>
													<select id="gallery-theme" style="width:110px;">
														<option value="fancybox">Fancybox</option>
														<option value="swipebox">Swipebox</option>
													</select>
												</td>
												<td width="30%" align="right">
													<label>Pagination: </label>
													<input type="radio" name="gallery-pagination" value="true" checked="checked" >Yes
													<input type="radio" name="gallery-pagination" value="false" >No
												</td>
											</tr>
											<tr>
												<td colspan="3" style="padding-top:15px;"  align="center">
													<input type="button" data-remodal-action="close" class="button button-default button-large" value="Close"  >
													<input type="button" onClick="createShortCode($(this))" class="button button-primary button-large" value="Update Code"  >
												</td>
											</tr>
										</table>
										<div id="short_code" >
												[easy_gallery view="image" theme="fancybox" order="asc" pagination="true" limit="8"]										
										</div>
									</div>
								</div>
							</div>

<script type="text/javascript">

function createShortCode(element){
	var view = $('#gallery-view option:selected').val();
	var album = $('#gallery-album option:selected').val();
	var limit = $('#gallery-limit').val();
	var order = $('#gallery-order option:selected').val();
	var theme = $('#gallery-theme option:selected').val();
	var pagination = $('input[name=gallery-pagination]:checked').val();
	var code = '[easy_gallery view="'+view+'" theme="'+theme+'" order="'+order+'" pagination="'+pagination+'" limit="'+limit+'"'; 
	if(view=="album" && album!=""){
		code = code + ' album="'+album+'"';
	}	
	code = code + "]";
	$('#short_code').text(code);

}
function toggleAlbumMode(element){
	var value = element.find('option:selected').val();	
	if(value=="album"){
		$('#gallery-album').attr('disabled',false);
	}else{
		$('#gallery-album').attr('disabled',true);
	}
}
function initShortCodeWindow(element){
	
	var image_src = element.data('image-src');
	$('#album-image').attr('src',image_src);
	var modal_inst = $('#modal').remodal();
	var name = element.data('name');
	$('#gallery-name').text(name);
	modal_inst.open();
}
</script>