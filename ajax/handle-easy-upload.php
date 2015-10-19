<?php 
function handle_easy_upload()
{
  if(isset($_FILES['file']))
  {
	  //	date_default_timezone_set(get_option('timezone_string')); 
	  	$albumid=$_POST['albumid'];
		$desc=$_POST['desc'];
		$picid= $_POST['picid'];
		$uploadfiles = $_FILES['file'];		
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
									// echo $filedest;
									if ( !@move_uploaded_file($filetmp, $filedest) )
									{
									  $msg.="Error, the file $filetmp could not moved to : $filedest ";							 
									}			
									//clearstatcache();
												
						     }//end if
						}//End for each						
			} //end if (is-array)
	
	$data['album_id']=$albumid;
	$data['description']=$desc;	
	$data['image_path']=$fileurl;	
	$data['thumb_path']=$fileurl_thumb;
	$data['post_date']=date('Y-m-d',time());
	$data['post_time']=time();
	$th=get_option("EasyGallery-thumb-height");
    $tw=get_option("EasyGallery-thumb-width");
    $ih=get_option("EasyGallery-image-height");
    $iw=get_option("EasyGallery-image-width");
	
	if($filedest!="")
			{
				$image = wp_get_image_editor( $filedest ); 
				//echo $filedest;
				if ( ! is_wp_error( $image ) ) 
				{
					$image->resize( $iw, $ih, false );
					$image->save( $filedest );
				}
				
			}
	if($filedest!="")
			{
				$image = wp_get_image_editor( $filedest ); 
				//print_r($image);
				if ( ! is_wp_error( $image ) ) 
				{
					$image->resize( $tw, $th, false );
					$image->save( $filedest_thumb );
				}
				
			}
	
	global  $wpdb;
	$format=array('%d','%s','%s','%s','%s','%s');
				
				$rows_affected = $wpdb->insert("easy_photos",
										$data,
										$format);
		if($rows_affected>0)
		{
			echo $picid.'|';
		}
		
			
					
  }//End if(@$_POST)
  
}

  
?>