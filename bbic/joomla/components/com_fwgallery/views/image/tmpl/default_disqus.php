<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');

$item_uri = JURI :: getInstance()->toString();
$disqus_domain =  str_replace(array('http://','.disqus.com/','.disqus.com'), '', $this->params->get('disqus_domain'));
if ($disqus_domain) {
?>
<div id="disqus_thread"></div>
<script type="text/javascript">
//<![CDATA[
var disqus_url= "<?php echo $item_uri; ?>";
var disqus_identifier = "<?php echo substr(md5($disqus_domain),0,10); ?>_id<?php echo $this->row->id; ?>";
<?php if (strpos($item_uri, 'http://localhost/') === 0) : ?>
var disqus_developer = true;
<?php endif; ?>
//]]>
</script>
<script type="text/javascript" src="http://disqus.com/forums/<?php echo $disqus_domain; ?>/embed.js"></script>
<noscript>
	<a href="http://<?php echo $disqus_domain; ?>.disqus.com/?url=ref"><?php echo JText::_("FWG_VIEW_THE_DISCUSSION_THREAD"); ?></a>
</noscript>
<?php
} else {
?>
Please enter your Disqus subdomain in order to use the Disqus Comment System!
If you don't have a Disqus.com account <a target="_blank" href="http://disqus.com/comments/register/">register for one here</a>
<?php
}
