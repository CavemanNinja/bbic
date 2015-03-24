<?php 
/**
 * @extension       EVO frontpage module
 * @version         2.4.0
 *
 * @author          Pacificador <minifrontpageevolution@gmail.com>
 * @link            http://www.evofrontpage.com/
 * @copyright       Copyright © 2012 All Rights Reserved
 * @license         Released under GNU/GPL License
 */
 
defined('_JEXEC') or die('Restricted access'); 
?>

<div class="evofrontpage-tabbed-slide-down-up" id="evofrontpage-<?php echo $evofpid; ?>">
<div id="evofptabs"></div>
<div style="table-layout:fixed;width:100%;padding:0 !important; margin:0 !important;" class="evofrontpageid">
	<div class="anim">
	<div class="anim-div" style="position:relative;">
		<?php for($row=1; $row<=$number_of_row;$row++) { ?>
			<?php
			if( $animate_row_count == 1 ){
				echo '<div class="animate" style="padding:0 !important; margin:0 !important;display:table; width:100%;">';
			}
			?>
	
			<div class="evofp-table-row" style="display:table-row;width:<?php echo $column_percentage*$number_of_column; ?>%">
				<?php for($column=1; $column<=$number_of_column;$column++) { ?>
				<div class="evofp-table-cell" style="display:table-cell;width:<?php echo $column_percentage; ?>%;word-wrap:break-word !important;vertical-align:top!important;">
						<?php
						$counting++;
						if($show_others && $show_more_article && $counting == $number_of_row*$number_of_column ){
					echo '<div class="evofp-other-article">';
					echo '<div class="evofp-other-article-inner">';
							echo "<span class='evofp-other-article-title'>".$header_title_links."</span>";
				echo '<ul>';
			for($index = $counting; $index < count($list); $index++){
				$item = $list[$index];
				echo '<li>';
				if ($link_newtab == 1){ echo '<a href="'. $item->link .'" target="_blank">'; }
				if ($link_newtab == 0){ echo '<a href="'. $item->link .'">'; }
					echo $item->title;
						echo '</a>';
				echo '</li>';
			}
				echo '</ul>';
							echo '</div>';
							echo '</div>';
						}else{
echo '<div class="evofp-table-cell-inner">';
							$item_pos = ( $item_direction == 'down' ) ? ( ( ( $column - 1 ) * $number_of_row ) + $row ) - 1: $counting - 1;
			$item = ( !empty( $list[$item_pos] ) ) ? $list[$item_pos] : null;
			if(!empty($item)){
				if(!empty($item->thumb)){
				if (($thumbnail_position == 1) && ($thumb_embed == 1)) {
				if ($thumb_link) {
					if ($link_newtab == 1){ echo '<a href="'. $item->link .'" target="_blank">'; }
					if ($link_newtab == 0){ echo '<a href="'. $item->link .'">'; }}
			echo '<div class="evofp-div-img-'.$alignment.'">';
					echo $item->thumb;
			echo '</div>';
				if ($thumb_link) { echo '</a>'; }
				}}
				
				if (!empty($show_title)) {
					if ($title_link){
					 if ($link_newtab == 1){ echo '<a href="'. $item->link .'" target="_blank">'; }
					 if ($link_newtab == 0){ echo '<a href="'. $item->link .'">'; }}
					echo '<span class="title">';
					echo $item->title;
					echo '</span>';
					if ($title_link){ echo '</a>'; }
}
				if( $show_date OR $show_author ){
					echo '<span class="evofp-date-author">';
					echo showTagFP( $item->date, null, $show_date, false, null, null );
					echo showTagFP( ' - ', null, ($show_date && $show_author), false, null, null );
					echo showTagFP( $item->author, null, $show_author, false, null, null );
					echo '</span>';
				}
				if(!empty($item->thumb)){
				if (($thumbnail_position == 0) && ($thumb_embed == 1)) {
				if ($thumb_link) {
					if ($link_newtab == 1){ echo '<a href="'. $item->link .'" target="_blank">'; }
					if ($link_newtab == 0){ echo '<a href="'. $item->link .'">'; }}
			echo '<div class="evofp-div-img-'.$alignment.'">';
					echo $item->thumb;
			echo '</div>';
				if ($thumb_link) { echo '</a>'; }
				}}
				
				if ($intro_link==1){
					if ($link_newtab == 1){ echo '<a class="evofp-introlink" href="'. $item->link .'" target="_blank">'; }
					if ($link_newtab == 0){ echo '<a class="evofp-introlink" href="'. $item->link .'">'; }}
				echo $item->introtext;
				if ($intro_link==1){ echo '</a>'; }
				echo "<div class='clrfix'></div>";

				if ($cat_title==1){
					if ($link_newtab == 1){ echo '<a class="evofp-categ" href="'.$item->categblog.'" target="_blank">'.$item->categtitle.'</a>'; }
					if ($link_newtab == 0){ echo '<a class="evofp-categ" href="'.$item->categblog.'">'.$item->categtitle.'</a>'; }
				}
				
				if ($show_fulllink==1){		
					if ($link_newtab == 1){ echo '<a class="evofp-readon" href="'.$item->link.'" target="_blank">'.$fulllink.'</a>'; }
					if ($link_newtab == 0){ echo '<a class="evofp-readon" href="'.$item->link.'">'.$fulllink.'</a>'; }
				}
						}
echo '</div>';
						}
						?>
					</div>
				<?php } ?>
			</div>
<div class="evofp-below-row" ></div>
			<?php
			if( $animate_row_count == $number_of_row_animate OR $row == $number_of_row)
			{
				$animate_row_count = 0;
				echo '</div>';
			}
			$animate_row_count++;
		}
		?>
	</div>
	</div>
</div>
</div>