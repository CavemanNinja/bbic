<?php
/**
 * FW Gallery  Picasa Import Plugin 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgFwGalleryPicasaImport extends JPlugin {
	var $picasa = null;
	function plgFwGalleryPicasaImport(&$subject, $config = array())  {
		parent::__construct($subject, $config);
		if (!function_exists('curl_init')) return;
		if (!$this->params) {
			jimport('joomla.html.parameter');
			$this->params = new JRegistry();
		}
	}
	function fwGetAdminForm() {
		$lang = JFactory :: getLanguage();
		$lang->load('plg_fwgallery_picasaimport');
		$app = JFactory :: getApplication();
		if ($app->isAdmin()) {
			if (!function_exists('curl_init')) {
				$error_msg = JText :: _('Install PHP CURL extention to get this plugin working please');
				ob_start();
				include(dirname(__FILE__).'/picasaimport/tmpl/error.php');
				return ob_get_clean();
			}
			jimport('joomla.application.component.model');
			JModelLegacy :: addIncludePath(JPATH_BASE.'/components/com_fwgallery/models/');
			if ($model = JModelLegacy :: getInstance('Files', 'fwGalleryModel')) {
				ob_start();

				$galleries = (array)$model->getProjects();
				$picasa_username = $app->getUserStateFromRequest('fwg_picasa_plg.username', 'picasa_username');
				$picasa_galleries = $this->loadPicasaGalleries();
				include(dirname(__FILE__).'/picasaimport/tmpl/adminform.php');

				return ob_get_clean();
			}
		}
	}
	function loadPicasaGalleries() {
		$result = array();
		$app = JFactory :: getApplication();
		if ($picasa_username = $app->getUserStateFromRequest('fwg_picasa_plg.username', 'picasa_username')) {
			if ($data = $this->getFeed('https://picasaweb.google.com/data/feed/api/user/'.$picasa_username) and preg_match_all('#<entry>(.*?)</entry>#msi', $data, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $match) if (
				 in_array(strtolower($this->getPregSubstr('#<gphoto:access>(.*?)</gphoto:access>#i', $match[1])), array('public', 'private'))
				  and $id = $this->getPregSubstr('#<id>(.*?)</id>#i', $match[1])
				   and $text = $this->getPregSubstr('#<title[^>]*>(.*?)</title>#i', $match[1])
				    and $count = $this->getPregSubstr('#<gphoto:numphotos>(\d+)</gphoto:numphotos>#i', $match[1])
					) {
					 $result[] = JHTML :: _('select.option', $id, $text.($count?(' ('.$count.')'):''), 'id', 'name');
				}
			}
		}
		return $result;
	}
	function getPregSubstr($preg, $str, $number=1) {
		if (preg_match($preg, $str, $match)) return JArrayHelper :: getValue($match, $number);
	}
	function getFeed($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if (preg_match('/^https/i', $url)) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		$result = curl_exec($ch);
		curl_close($ch);
		unset($ch);
		return $result;
	}
	function fwProcess() {
		set_time_limit(0);
		$app = JFactory :: getApplication();
		$session = JFactory :: getSession();
		jimport('joomla.filesystem.file');
		$step = JRequest :: getInt('step');
		$number = JRequest :: getInt('number');
		$msg = '';
		$galleries = $images = array();
		$fwgallery_id = 0;

		if (!$step) {
			if ($picasa_username = $app->getUserStateFromRequest('fwg_picasa_plg.username', 'picasa_username')) {
				if ($galleries = $this->loadPicasaGalleries()) {
?>
<script type="text/javascript">
parent.$('plg-fwg-picasa-gallery').options.length = 0;
<?php
					foreach ($galleries as $gallery) {
?>
var op = parent.document.createElement('option');
op.value = '<?php echo $gallery->id; ?>';
op.text = '<?php echo $gallery->name; ?>';
parent.$('plg-fwg-picasa-gallery').add(op);
<?php
					}
?>
</script>
<?php
				} else $msg = JText :: _('No galleries', true);
 			} else $msg = JText :: _('No username', true);
 			if ($msg) {
?>
<script type="text/javascript">
parent.alert('<?php echo $msg; ?>');
</script>
<?php
 			}
		} elseif ($step == 1) {
			$fwgallery_id = JRequest :: getInt('gallery_id');
			$number = 0;
			if ($gallery_id = str_replace('data/entry/api', 'data/feed/api', JRequest :: getString('picasa_gallery_id'))
			 and $data = $this->getFeed($gallery_id.'?imgmax=1600')
			  and preg_match_all('#<entry>(.*?)</entry>#i', $data, $matches, PREG_SET_ORDER)) {
			  	if (!$fwgallery_id) {
					$gallery_name = $this->getPregSubstr('#<title[^>]*>(.*?)</title>#i', $data);

					$db = JFactory :: getDBO();
					$db->setQuery('SELECT id FROM #__fwg_projects WHERE name = '.$db->quote($gallery_name));
					if (!($fwgallery_id = (int)$db->loadResult())) {
						$gallery = JTable :: getInstance('Project', 'Table');
		    			$post = array(
		    				'name' => $gallery_name,
		    				'created' => date('Y-m-d H:i:s', strtotime($this->getPregSubstr('#<updated>(.*?)</updated>#i', $data)))
		    			);
		    			if ($gallery->bind($post) and $gallery->check() and $gallery->store()) {
		    				$fwgallery_id = $gallery->id;
		    			} else $msg = JText :: _('Error gallery creating: ').$gallery->getError();
					}
			  	}
				$session->set('picimport.gallery_id', $fwgallery_id);

				foreach ($matches as $match) {
					if ($link = $this->getPregSubstr("#<media:content url='([^']+)#i", $match[1]) and $filename = $this->getPregSubstr("#<content type='image[^']*' src='([^']+)'#i", $match[1])) {
						$images[] = array(
							'link'=>$link,
							'filename'=>basename($filename),
							'name'=>$this->getPregSubstr('#<title[^>]*>(.*?)</title>#i', $match[1]),
							'date'=>$this->getPregSubstr('#<updated[^>]*>(.*?)</updated>#i', $match[1]),
						);
					}
				}
				$filename = md5(microtime().rand());
				$session->set('picimport.files', $filename);
				JFile :: write(JPATH_CACHE.'/'.$filename, serialize($images));
			} else $msg = JText :: _('Gallery not selected');
		} else {
			if ($filename = $session->get('picimport.files') and file_exists(JPATH_CACHE.'/'.$filename)) {
				$images = unserialize(JFile :: read(JPATH_CACHE.'/'.$filename));
			} else $msg = JText :: _('Gallery images is not stored');
		}

		if ($step and $images) {
			$fwgallery_id = $session->get('picimport.gallery_id');
			$succ = $session->get('picimport.succ');
			$err = $session->get('picimport.err');
			if (JRequest :: getInt('stop')) {
				$session->set('picimport.gallery_id', 0);
				$session->set('picimport.succ', 0);
				$session->set('picimport.err', 0);
				if ($filename = $session->get('picimport.files') and file_exists(JPATH_CACHE.'/'.$filename)) JFile :: delete(JPATH_CACHE.'/'.$filename);
?>
<script type="text/javascript">
var form = parent.$('fw-picasa-images-form-import');
form.step.value = 1;
parent.$('fwgallery-picasaimport-step-import-notice').innerHTML = '<?php echo JText :: sprintf('Stopped, succesfully imported: %s, errors: %s', (int)$succ, (int)$err); ?>';
parent.$('fwgallery-picasaimport-step-import-1').innerHTML = '';
form.import_button.removeProperty('disabled');
form.stop_button.setStyle('display', 'none');
</script>
<?php
			} elseif ($number >= count($images)) {
				$session->set('picimport.gallery_id', 0);
				$session->set('picimport.succ', 0);
				$session->set('picimport.err', 0);
				if ($filename = $session->get('picimport.files') and file_exists(JPATH_CACHE.'/'.$filename)) JFile :: delete(JPATH_CACHE.'/'.$filename);
?>
<script type="text/javascript">
var form = parent.$('fw-picasa-images-form-import');
form.step.value = 1;
parent.$('fwgallery-picasaimport-step-import-notice').innerHTML = '<?php echo JText :: sprintf('Finished, succesfully imported: %s, errors: %s', (int)$succ, (int)$err); ?>';
parent.$('fwgallery-picasaimport-step-import-1').innerHTML = '';
form.import_button.removeProperty('disabled');
form.stop_button.setStyle('display', 'none');
</script>
<?php
			} else {
				if (!empty($images[$number]) and $filedata = $this->getFeed($images[$number]['link'])) {
		    		$file = JPATH_SITE.'/tmp/'.$images[$number]['filename'];
		    		if (JFile :: write($file, $filedata)) {
						$image = JTable :: getInstance('File', 'Table');
						$_FILES['filename'] = array(
							'name' => basename($file),
							'tmp_name' => $file,
							'error' => 0,
							'size' => filesize($file),
							'type' => 'image/'.JFile :: getExt(basename($file))
						);
						JRequest :: setVar('id', 0);
						JRequest :: setVar('project_id', $fwgallery_id);
						JRequest :: setVar('name', $images[$number]['name']);
                        JRequest :: setVar('created', date('Y-m-d H:i:s', strtotime($images[$number]['date'])));

                        if ($image->bind(JRequest :: get()) and $image->check() and $image->store()) {
                        	$succ++;
                        } else {
                        	$err++;
                        }
                        JFile :: delete($file);
		    		} else {
		    			$err++;
		    		}
		    	} else {
		    		$err++;
				}
				$session->set('picimport.succ', $succ);
				$session->set('picimport.err', $err);
?>
<script type="text/javascript">
var form = parent.$('fw-picasa-images-form-import');
form.step.value = 2;
form.number.value = <?php echo $number + 1; ?>;
parent.$('fwgallery-picasaimport-step-import-notice').innerHTML = '<?php echo JText :: sprintf('Processing %s of %s', ($number + 1), count($images)); ?>';
form.submit();
</script>
<?php
			}
		} else $msg = JText :: _('Gallery images not found');

		die();
	}
}
?>