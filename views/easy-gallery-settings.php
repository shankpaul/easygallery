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
if(@$_POST['easy_settings_submit'])
{
  update_option("EasyGallery-thumb-height",$_POST['easy_th']);
  update_option("EasyGallery-thumb-width",$_POST['easy_tw']);

  update_option("EasyGallery-album-cover-height",$_POST['easy_ah']);
  update_option("EasyGallery-album-cover-width",$_POST['easy_aw']);
  if(isset($_POST['easy_cp']))
    update_option("EasyGallery-album-cover-crop",true);
  else
    update_option("EasyGallery-album-cover-crop",false);

  update_option("EasyGallery-image-height",$_POST['easy_h']);
  update_option("EasyGallery-image-width",$_POST['easy_w']);

  echo '<div class="updated fade below-h2"><p>Settings Updated<br>'.$msg.'</p></div>'; 

}
?>
<form method="post" onsubmit="return easy_validate_settings();">
  <div class="postbox ">
    <h3 class="hndle ui-sortable-handle" style="padding: 0 12px 12px;line-height: 1.4em;"><span>Settings</span></h3>

    <table class="inside" >

      <tr>
        <td width="250px;">
         Album Cover Image Width <br />
         <input type="text" name="easy_aw" id="easy_aw" value="<?php echo get_option('EasyGallery-album-cover-width'); ?>" /> PX

       </td>
       <td>
        Album Cover Image Height <br />
        <input type="text" name="easy_ah" id="easy_ah" value="<?php echo get_option('EasyGallery-album-cover-height'); ?>" /> PX
      </td>
      <td>&nbsp
      </td>
      <td>
        <br />
        <input type="checkbox" name="easy_cp" id="easy_cp" <?php echo get_option('EasyGallery-album-cover-crop')==true? 'checked="checked"':''; ?> /> Crop Album Cover  
      </td>
    </tr>
    <tr>
      <td colspan="2"> <span class="np_help">This width and height will use for showing album cover images at frontend.</span></td>
    </tr>
    <tr>
      <td colspan="2"><br /></td>
    </tr>


    <tr>
    	<td width="250px;">
       Gallery Thumbnail Image Width <br />
       <input type="text" name="easy_tw" id="easy_tw" value="<?php echo get_option('EasyGallery-thumb-width'); ?>" /> PX

     </td>
     <td>
       Gallery Thumbnail Image Height <br />
       <input type="text" name="easy_th" id="easy_th" value="<?php echo get_option('EasyGallery-thumb-height'); ?>" /> PX
     </td>
   </tr>
   <tr>
     <td colspan="2"> <span class="np_help">This width and height will use for showing gallery thumbnail images at frontend.</span></td>
   </tr>
   <tr>
     <td colspan="2"><br /></td>
   </tr>
   <tr>
     <td>
       Gallery Image Width <br />
       <input type="text" name="easy_w" id="easy_w" value="<?php echo get_option('EasyGallery-image-width'); ?>" /> PX
     </td>
     <td>
       Gallery Image Height <br />
       <input type="text" name="easy_h" id="easy_h" value="<?php echo get_option('EasyGallery-image-height'); ?>" /> PX
     </td>
   </tr>
   <tr>
     <td colspan="2"> <span class="np_help">This width and height will use for showing gallery  images at frontend.</span></td>
   </tr>
   <tr>
     <td colspan="2"><br />
       <input type="submit" name="easy_settings_submit" value="Update Changes" class="np__style np_ button button-primary button-large" />
     </td>
   </tr>

 </table> 
</div>
</form> 
</div>
<script language="javascript">
function easy_validate_settings()
{
		//return true;
		er="";		
		if($('#easy_tw').val().trim()=="")
		{
			er+="Please Enter Thumbnail Width\n";
		}
		else if((parseInt($('#easy_tw').val()))<=0 || isNaN($('#easy_tw').val()))
		{
			er+="Thumbnail Width Should be Greater Than zero \n";
		}		
		if($('#easy_th').val().trim()=="")
		{
			er+="Please Enter Thumbnail Height\n";
		}
		else if(parseInt($('#easy_th').val())<=0 || isNaN($('#easy_th').val()))
		{
			er+="Thumbnail Height Should be Greater Than zero \n";
		}		
		if($('#easy_w').val().trim()=="")
		{
			er+="Please Enter Image Width\n";
		}
		else if(parseInt($('#easy_w').val())<=0 || isNaN($('#easy_w').val()))
		{
			er+="Image Width Should be Greater Than zero \n";
		}		
		if($('#easy_h').val().trim()=="")
		{
			er+="Please Enter Image Height\n";
		}	
		else if(parseInt($('#easy_h').val())<=0 || isNaN($('#easy_h').val()))
		{
			er+="Image Height Should be Greater Than zero \n";
		}			
		if(er!="")
		{
			alert("Corrections: \n"+er);
			return false;
		}
		return true;
  }
  </script>