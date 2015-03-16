<?php /**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?php echo $this->import('scripts.html');?>

<script>
    Files.sitebase = '<?php echo $sitebase; ?>';
    Files.token = '<?php echo $token; ?>';

    window.addEvent('domready', function() {
        var config = <?php echo json_encode(KObjectConfig::unbox($this->parameters()->config)); ?>,
            options = {
                cookie: {
                    path: '<?php echo $this->object('request')->getSiteUrl()?>'
                },
                state: {
                    defaults: {
                        limit: <?php echo (int) $this->parameters()->limit; ?>,
                        offset: <?php echo (int) $this->parameters()->offset; ?>,
                        types: <?php echo json_encode($this->parameters()->types); ?>
                    }
                },
                root_text: <?php echo json_encode($this->translate('Root folder')) ?>,
                types: <?php echo json_encode($this->parameters()->types); ?>,
                container: <?php echo json_encode($container ? $container->toArray() : null); ?>,
                thumbnails: <?php echo json_encode($container ? $container->getParameters()->thumbnails : true); ?>
            };
        options = Object.append(options, config);

        Files.app = new Files.App(options);
    });
</script>


<div id="files-app" class="com_files">
	<?php echo $this->import('templates_icons.html'); ?>
	<?php echo $this->import('templates_details.html'); ?>

	<div id="files-sidebar">
        <h3><?php echo $this->translate('Folders'); ?></h3>
		<div id="files-tree"></div>
	</div>

    <div id="files-canvas">
        <div class="path" style="height: 24px;">
            <div id="files-pathway"></div>
            <div class="files-layout-controls btn-group" data-toggle="buttons-radio">
                <button class="btn files-layout-switcher" data-layout="icons" title="<?php echo $this->translate('Show files as icons'); ?>">
                    <i class="icon-th icon-grid-view-2"></i>
                </button>
                <button class="btn files-layout-switcher" data-layout="details" title="<?php echo $this->translate('Show files in a list'); ?>">
                    <i class="icon-list"></i>
                </button>
            </div>
        </div>
        <div class="view">
            <div id="files-grid"></div>
        </div>
        <table class="table">
            <tfoot>
            <tr><td>
                <?php echo $this->helper('paginator.pagination') ?>
            </td></tr>
            </tfoot>
        </table>

        <?php echo $this->import('uploader.html');?>
    </div>
    <div style="clear: both"></div>
</div>

<div id="files-new-folder-modal" class="koowa mfp-hide" style="max-width: 600px; position: relative; width: auto; margin: 20px auto;">
    <form class="files-modal well">
        <div style="text-align: center;">
            <h3 style=" float: none">
                <?php echo $this->translate('Create a new folder in {folder}', array(
                    'folder' => '<span class="upload-files-to"></span>'
                )) ?>
            </h3>
        </div>
        <div class="input-append">
            <input class="span5 focus" type="text" id="files-new-folder-input" placeholder="<?php echo $this->translate('Enter a folder name') ?>" />
            <button id="files-new-folder-create" class="btn btn-primary" disabled><?php echo $this->translate('Create'); ?></button>
        </div>
    </form>
</div>
