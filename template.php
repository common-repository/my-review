<div class="myreview">
<?php if (!empty($review->simple_text)): ?>
<div>
<?php echo $review->simple_text ?>
</div>
<?php endif; ?>

<?php if (!empty($review->good) || !empty($review->bad)): ?>
<table style="margin:auto">
<tr>
<td class="myreviews_good">
<h3 class="myreview_h3 good" style="padding:5px;margin:0;font-weight:bold"><?php _e('The Good', 'my-review') ?></h3>
<div class="myreviews_content">
<?php echo $review->good; ?>
</div>
</td>
<td class="myreviews_bad">
<h3 class="myreview_h3 bad" style="padding:5px;margin:0;font-weight:bold"><?php _e('The Bad', 'my-review') ?></h3>
<div class="myreviews_content">
<?php echo $review->bad; ?>
</div>
</td>
</tr>
</table>
<?php endif; ?>
<h3 class="rw_indepth_head myreview_h3" style="margin:1.2em 0 0 0"><?php _e('Full Review', 'my-review') ?></h3>
<div class="rw_full">
<?php echo $review->cleanPost(); ?>
</div>
<?php
if (count($review->score)>0):

$score=$review->score;
if (isset($score['_final_']))
{
	$total=$score['_final_'];
	unset($score['_final_']);
}
?>
<h3 class="myreview_h3" style="margin:1.2em 0 0 0"><?php _e('Rating', 'my-review') ?> <span class="myreview_h3_span">(<?php _e('out of 10', 'my-review'); ?>)</span></h3>

<table width="100%" style="margin-top:5px" cellspacing="0" cellpadding="2px">
<tbody>
<?php foreach($score as $name=>$value){ ?>
<tr>
<td class="rw_score_td rw_score_name">
<h4 class="rw_score_name_h4" style="margin:0;color: #336633;"><?php echo $name; ?></h4>
<div class="rw_score_name_div">
<?php echo $value['description']; ?>
</div>
</td> 
<td class="rw_score_td rw_score_rating">
<?php echo $value['rating']; ?>
</td>
</tr>
<?php } ?>
<tr>
<td class="rw_score_td rw_score_name rw_final_score_td">
<h4 class="rw_score_name_h4 rw_final_score_h4" style="margin:0;"><?php _e('Total', 'my-review') ?></h4>
</td>
<td class="rw_score_td rw_score_rating rw_final_score_td">
<span><?php echo $total; ?></span>
</td>
</tr>
</tbody>
</table>
<?php endif; ?>
</div>