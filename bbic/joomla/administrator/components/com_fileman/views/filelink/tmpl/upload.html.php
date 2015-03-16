<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('_JEXEC') or die; ?>

<?= helper('bootstrap.load', array('class' => array('full_height'))); ?>

<ktml:content>

<script>
window.addEvent('domready', function() {
    var trigger_refresh = false;

    Files.app.addEvent('uploadFile', function() {
        trigger_refresh = true;
    });

    if (window.parent.kQuery && window.parent.kQuery.magnificPopup && window.parent.kQuery.magnificPopup.instance) {
        var instance = window.parent.kQuery.magnificPopup.instance;

        instance.ev.on('mfpClose', function() {
            if (trigger_refresh) {
                window.parent.location.reload();
            }
        });
    }
});
</script>
