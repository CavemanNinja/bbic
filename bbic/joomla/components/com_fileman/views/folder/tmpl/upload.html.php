<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('_JEXEC') or die; ?>

<?= helper('behavior.modal'); ?>

<p class="fileman_upload">
    <a class="btn koowa-modal mfp-iframe" href="<?= route('view=filelink&layout=upload&tmpl=koowa&folder='.$folder->path); ?>">
        <?= translate('Upload Files') ?>
    </a>
</p>