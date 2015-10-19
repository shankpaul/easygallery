<?php 
function show_easy_gallery($atts,$content=null)
{
	
	$order='desc';
	$pagesql='';
	$limit='';
	$count=8;
	$page=true;
	$page_st=true;
	$theme='fancybox';
	$view='album';
	$album='';
	global  $wpdb;
	//echo "select * from easy_photos order  by $order limit $limit offset $offset";
	
	
	if(isset($atts['order']))
		$order=$atts['order'];
	if(isset($atts['theme']))
	{
		$theme=trim($atts['theme']);
		if($theme!='fancybox'&&$theme!='swipebox')
		{
			
			$theme='fancybox';
			
		}
	}
	if(isset($atts['view']))
	{
		$view=trim($atts['view']);
		if($view!='album'&&$view!='image')
		{
			
			$view='album';
			
		}         
	}
	if(isset($atts['album']))
	{
		$album=trim($atts['album']);
		
	}
	if(isset($atts['limit']))
	{	
		$count=trim($atts['limit']);
		if($count<1)
		{
			$count=2;
		}
	}
	$order=strtolower($order);
	if($order!='asc'&&$order!='desc')
		$order='desc';
	
	if(isset($atts['pagination']))
	{
		$page_st=trim($atts['pagination']);
		if($page_st!='true' && $page_st!='false' )
			$page_st='true';
	}
	if($page_st=='true')
	{
		$page=true;
		$sql="  SELECT DISTINCT(a.album_id) FROM easy_album a,easy_photos b WHERE a.album_id=b.album_id AND a.disabled=0 AND b.disabled=0";
		if($album!='')
			$sql = $sql." and a.album_id = $album";
		
		if($view=='image')
		{
			$sql="select * from easy_photos  where album_id not in(select album_id from easy_album b where b.disabled=1) and disabled=0";
			if($album!='')
				$sql= $sql." and album_id = $album";

		}


		$rows = $wpdb->get_results($sql);	
		$items=count($rows);
		if($items > 0)
		{				
			
			$p = new pagination;
			$p->items($items);
					$p->limit($count); // Limit entries per page
					if($album!='')
						$p->target(get_permalink().'?album='.$album);
					else
						$p->target(get_permalink());
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
					
				}else
				echo "No Images";
				
				
			}
			else
			{
				$page=false;
			}
			
			
			
			?>
			
			<div class="easy-gallery-base">
				<div class="easy-gallery-content">
					<?php

					$sql="";
					$tmp_sql="  SELECT DISTINCT(a.album_id) FROM easy_album a,easy_photos b WHERE a.album_id=b.album_id AND a.disabled=0 AND b.disabled=0 ";
					if($album!='')
						$tmp_sql = $tmp_sql." and a.album_id = $album";
					$tmp_sql = $tmp_sql . " order by album_id $order $limit";
					$rows_tmp = $wpdb->get_results($tmp_sql);
					$data=array();
					foreach($rows_tmp as $obj_temp)
					{
						array_push($data,$obj_temp->album_id);
					}
					$album_list=implode(',',$data);

					if($view=='image')
					{
						$sql="select * from easy_photos  where album_id not in(select album_id from easy_album b where b.disabled=1) and disabled=0 ";
						if($album!='')
							$sql= $sql." and album_id = $album";
						$sql = $sql . " order by  id  $order $limit";		

					}
					else
					{
						$sql="select a.*,b.album_cover,b.name from easy_photos a,easy_album b where a.album_id = b.album_id and b.disabled=0 and a.disabled=0 and b.album_id in($album_list) order  by b.album_id $order";				
					}


					$rows = $wpdb->get_results($sql);	

					$aid="";
					$i=1;
					$img_count=count($rows);

					if($view=='album')
					{
						foreach($rows as $obj){ 
							if($obj->album_id!=$aid && $aid!="")
							{
								echo '</div>';
								$i=1;
							}
							if($i==1)
							{	
								?>
								<div class="album_thumb has-album-name">
									<?php }

									if($i==1)
									{
										?>
										<a href="<?php echo $obj->image_path; ?>" class="<?php echo $theme; ?>" rel="<?php echo $obj->album_id; ?>" title="<?php echo $obj->description; ?>">
											<img title="<?php echo $obj->name; ?>"  src="<?php echo $obj->album_cover; ?>" />
										</a>
										<div class="album-name"><?php if(strlen($obj->name)>30) echo substr($obj->name,0,30).'..'; else echo $obj->name; ?></div>
										<?php
									}
									else
									{
										?>
										<a href="<?php echo $obj->image_path; ?>" class="<?php echo $theme; ?>" rel="<?php echo $obj->album_id; ?>" title="<?php echo $obj->description; ?>">

										</a>
										<?php 
									}
									$i++;

									if($img_count==1)
									{	
										$i=1;
										?>     
									</div>
									<?php }
			//else  if($obj->album_id!=$aid && $aid!="" and  )
	 		 //{	
			//echo $obj->album_id;
			//	 $i=1;
									?>

            <?php //}
            
            $aid=$obj->album_id;
            ?>
            
            <?php }  
            if($img_count > 0)  
            	echo '</div>';
            
          }
          else if($view=='image')
          {
          	foreach($rows as $obj){ 
          		
          		?>
          		<div class="album_thumb"> 
          			<p><a class="fancybox" data-fancybox-group="thumb" href="<?php echo $obj->image_path; ?>">
          				<img src="<?php echo $obj->thumb_path; ?>" /></a>
          			</p>
          		</div>
          		<?php 
          	}}
          	?>
          	<br style="clear:both;" />
          </div>

          <div>

          	<?php if($page&&$items > 0){
          		echo $p->show(); } // Echo out the list of paging. ?>

          	</div>
          </div>

          <?php 
        }

        ?>

        