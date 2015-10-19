<?php
    $easy_album="
                    CREATE TABLE IF NOT EXISTS `easy_album` (
                    `album_id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `name` varchar(200) NOT NULL,
                    `description` text,
                    `album_cover` varchar(200) DEFAULT NULL,
                    `create_date` date DEFAULT NULL,
                    `create_time` varchar(50) DEFAULT NULL,
                    `disabled` smallint(1) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`album_id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
                ";
    $easy_photo="                    
                    CREATE TABLE IF NOT EXISTS `easy_photos` (
                      `id` bigint(20) NOT NULL AUTO_INCREMENT,
                      `album_id` bigint(20) NOT NULL,
                      `description` text,
                      `image_path` varchar(300) NOT NULL,
                      `thumb_path` varchar(350) NOT NULL,
                      `post_date` date DEFAULT NULL,
                      `post_time` varchar(50) DEFAULT NULL,
                      `disabled` smallint(1) NOT NULL DEFAULT '0',
                      PRIMARY KEY (`id`),
                      KEY `album_id` (`album_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
                ";
    $easy_extra="
                    ALTER TABLE `easy_photos`
                    ADD CONSTRAINT `easy_photos_ibfk_1` 
                    FOREIGN KEY (`album_id`) 
                    REFERENCES `easy_album` (`album_id`) 
                    ON DELETE CASCADE ON UPDATE CASCADE;
                ";
     global $wpdb;
      
       $wpdb->query(  $easy_album );
       $wpdb->query(  $easy_photo );
       $wpdb->query(  $easy_extra );

?>
