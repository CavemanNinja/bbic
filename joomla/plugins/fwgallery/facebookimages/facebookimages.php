<?php
/**
 * FW Gallery  Facebook Images Plugin 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgFwGalleryFacebookImages extends JPlugin {

	var $app_id = null;
	var $app_secret = null;
	var $facebook = null;
	var	$redirect_uri = null;
	var $access_token = null;

	function __construct(&$subject, $config = array())  {
		parent::__construct($subject, $config);

		if (!function_exists('curl_init')) return;
		$this->app_id = '494136163952408';
		$this->app_secret = '16da58a1ff148d1e501db383e3c3de6b';

		$lang = JFactory :: getLanguage();
		$lang->load('plg_fwgallery_facebookimages', JPATH_ADMINISTRATOR);

		if (!$this->params) {
			$this->params = new JRegistry();
		}
		$app = JFactory :: getApplication();
		if ($app->isAdmin()) {
			$this->access_token = $this->params->get('access_token');
			$this->redirect_uri = urlencode('http://fastw3b.net/fwgallery/auth.php?return='
				.urlencode(base64_encode(JURI :: base(false).'index.php?option=com_fwgallery&view=plugins')));
		} else {
			$session = JFactory :: getSession();
			$this->access_token = $session->get('fbimport.access_token');
			$this->redirect_uri = urlencode('http://fastw3b.net/fwgallery/auth.php?return='
				.urlencode(base64_encode(JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager&layout=default_image', false, 2))));
		}
	}
	function fwGetSiteImagesForm() {
		if (!function_exists('curl_init')) {
			$error_msg = JText :: _('FWGPF_INSTALL_PHP_CURL');
			ob_start();
			include(dirname(__FILE__).'/facebookimages/tmpl/error.php');
			return ob_get_clean();
		}
		jimport('joomla.application.component.model');
		JModelLegacy :: addIncludePath(JPATH_BASE.'/components/com_fwgallery/models/');
		if ($model = JModelLegacy :: getInstance('Frontendmanager', 'fwGalleryModel')) {
			$error_msg = $this->checkForFacebookSessionData();
			$session = JFactory :: getSession();
			/* if user not logged in */
			$user = array();
			if ($this->access_token) {
			    $user = $this->graphReqest('/me/');
			}
			ob_start();
			if (!empty($user['id'])) {
				$session->set('fbimport.images', null);
				$session->set('fbexport.gallery', 0);

				$galleries = (array)$model->getCategories();
				$facebook_galleries = $this->loadFacebookGalleries();
				include(dirname(__FILE__).'/facebookimages/tmpl/siteform.php');
			} else {
				$session->set('fbimport.state', md5(uniqid(rand(), TRUE))); // CSRF protection
			    $link = "https://www.facebook.com/dialog/oauth?client_id="
				    . $this->app_id . "&redirect_uri=" . $this->redirect_uri . "&state="
				    . $session->get('fbimport.state') . "&scope=user_photos,publish_stream,offline_access";
				include(dirname(__FILE__).'/facebookimages/tmpl/login.php');
			}
			return ob_get_clean();
		}
	}
	function fwGetAdminForm() {
		if (JFactory :: getApplication()->isAdmin()) {
			if (!function_exists('curl_init')) {
				$error_msg = JText :: _('FWGPF_INSTALL_PHP_CURL');
				ob_start();
				include(dirname(__FILE__).'/facebookimages/tmpl/error.php');
				return ob_get_clean();
			}
			jimport('joomla.application.component.model');
			JModelLegacy :: addIncludePath(JPATH_BASE.'/components/com_fwgallery/models/');
			if ($model = JModelLegacy :: getInstance('Files', 'fwGalleryModel')) {
				$error_msg = $this->checkForFacebookSessionData();
				$session = JFactory :: getSession();

				/* if user not logged in */
				$user = array();
				if ($this->access_token) {
					if ('fan_page' == $this->params->get('page_type')) {
					    if ($fanpage = $this->params->get('fan_page')) $user = $this->graphReqest('/'.$fanpage);
					    else $error_msg = JText :: _('FWGPF_FAN_PAGE_NAME_IS_EMPTY');
					} else {
					    $user = $this->graphReqest('/me/');
					}
				}
				ob_start();
				if (!empty($user['id'])) {
					$session->set('fbimport.images', null);
					$session->set('fbexport.gallery', 0);

					$galleries = (array)$model->getProjects();
					$facebook_galleries = $this->loadFacebookGalleries();
					include(dirname(__FILE__).'/facebookimages/tmpl/adminform.php');
				} else {
					$session->set('fbimport.state', md5(uniqid(rand(), TRUE))); // CSRF protection
				    $link = "https://www.facebook.com/dialog/oauth?client_id="
					    . $this->app_id . "&redirect_uri=" . $this->redirect_uri . "&state="
					    . $session->get('fbimport.state') . "&scope=user_photos,publish_stream,offline_access,manage_pages";
					include(dirname(__FILE__).'/facebookimages/tmpl/login.php');
				}
				return ob_get_clean();
			}
		}
	}
	function checkForFacebookSessionData() {
		$msg = '';
		$code = JArrayHelper :: getValue($_GET, 'code');
		$state = JArrayHelper :: getValue($_GET, 'state');
		$session = JFactory :: getSession();

		if($code and $state and $session_state = $session->get('fbimport.state') and ($session_state === $state)) {
			$token_url = "https://graph.facebook.com/oauth/access_token?"
				. "client_id=" . $this->app_id . "&redirect_uri=" . $this->redirect_uri
				. "&client_secret=" . $this->app_secret . "&code=" . $code;

			$response = $this->request($token_url);

			$params = null;
			parse_str($response, $params);
			$access_token = JArrayHelper :: getValue($params, 'access_token');
			$expires = JArrayHelper :: getValue($params, 'expires');
			$app = JFactory :: getApplication();
			if ($access_token) {
				if ($app->isAdmin()) {
					$this->params->set('access_token', $access_token);
					$this->params->set('expires', $expires);
					$db = JFactory :: getDBO();
					$db->setQuery('UPDATE #__extensions SET params = '.$db->quote($this->params->toString()).' WHERE `type` = \'plugin\' AND folder = \'fwgallery\' AND element = \'facebookimages\'');
					$db->query();
					// redirect back to the plugin page
					$app->redirect('index.php?option=com_fwgallery&view=plugins', JText :: _('FWGPF_FACEBOOK_ACCESS_DATA_STORED'));
				} else {
					$session = JFactory :: getSession();
					$session->set('fbimport.access_token', $access_token);
					$session->set('fbimport.expires', $expires);
					$app->redirect(JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager&layout=default_image', false), JText :: _('FWGPF_FACEBOOK_ACCESS_DATA_STORED'));
				}
			}
		}
		return $msg;
	}
	function loadFacebookGalleries() {
		$result = array();
		$id = 0;
		if (JFactory :: getApplication()->isAdmin() and $fanpage = $this->params->get('fan_page')) {
		    $user = $this->graphReqest('/'.$fanpage);
			$id = JArrayHelper :: getValue($user, 'id');
		} else {
		    $user = $this->graphReqest('/me/');
			$id = JArrayHelper :: getValue($user, 'id');
		}

		if ($id) {
		    $albums = $this->graphReqest('/'.$id.'/albums', 'get', array('limit'=>500));
			if (!empty($albums['data']) and is_array($albums['data'])) foreach ($albums['data'] as $album) {
				$row = new stdclass;
				$row->id = JArrayHelper :: getValue($album, 'id');
				$row->name = JArrayHelper :: getValue($album, 'name').' ('.(int)JArrayHelper :: getValue($album, 'count').')';
				$result[] = $row;
			}
		}
		return $result;
	}
	function fwProcess() {
		set_time_limit(0);
		if (JRequest :: getCmd('direction') == 'import')
			$this->_importImages();
		elseif (JRequest :: getCmd('direction') == 'logout')
			$this->_resetLogin();
		else
			$this->_exportImages();
		die();
	}
	function _resetLogin() {
		$app = JFactory :: getApplication();
		if ($app->isAdmin()) {
			$this->params->set('access_token', '');
			$this->params->set('expires', '');
			$db = JFactory :: getDBO();
			$db->setQuery('UPDATE #__extensions SET params = '.$db->quote($this->params->toString()).' WHERE `type` = \'plugin\' AND folder = \'fwgallery\' AND element = \'facebookimages\'');
			$db->query();
		} else {
			$session = JFactory :: getSession();
			$session->set('fbimport.access_token', '');
			$session->set('fbimport.expires', '');
		}
?>
<script type="text/javascript">
var glue = (location.search == '')?'?':'&';
location = location.toString().replace(/#.*$/,'') + glue + 'fblogout=1';
</script>
<?php
	}
	function _importImages() {
		$msg = '';
		$path = JPATH_SITE.'/tmp/';
		if ($id = JRequest :: getString('facebook_gallery_id') and $this->access_token) {
			jimport('joomla.filesystem.file');
			$session = JFactory :: getSession();
			if ($images_filename = $session->get('fbimport.images')
			 and $fwgallery_id = $session->get('fbimport.gallery')
			  and file_exists($path.$images_filename)
			   and $images = unserialize(JFile :: read($path.$images_filename))) {
				$succ = (int)$session->get('fbimport.success');
				$err = (int)$session->get('fbimport.error');
				$err_descr = (array)$session->get('fbimport.errordescr');
				$number = JRequest :: getInt('number');
                if (JRequest :: getInt('stop')) {
					$session->set('fbimport.success', 0);
					$session->set('fbimport.error', 0);
					$session->set('fbimport.errordescr', array());
					$session->set('fbimport.gallery', 0);
					if ($images_filename = $session->get('fbimport.images') and file_exists($path.$images_filename)) JFile :: delete($path.$images_filename);
					$session->set('fbimport.images', null);

					$msg = JText :: sprintf('FWGPF_IMPORTING_STOPPED', $succ, $err);
					if ($err_descr) foreach ($err_descr as $err) {
						$msg .= '<br/><span class="fwg-error">'.$err.'</span>';
					}
?>
<script type="text/javascript">
document.getElement('#fwgallery-facebookimages-step-import-notice').setStyle('display', 'none');
document.getElement('#fwgallery-facebookimages-step-import-1').innerHTML = ' - <?php echo addcslashes($msg, "'") ?>';
var form = document.getElement('#fw-facebook-images-form-import');
$(form.stop_button).setStyle('display', 'none');
$(form.import_button).removeAttribute('disabled');
</script>
<?php
die();
                } elseif (!empty($images['data'][$number])) {
					$image_data = $images['data'][$number];
			    	if ($link = JArrayHelper :: getValue($image_data, 'source') and $filedata = $this->request($link)) {
			    		$file = JPATH_SITE.'/tmp/'.basename($link);
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
							JRequest :: setVar('name', JArrayHelper :: getValue($image_data, 'name', ucfirst(strtolower(str_replace('_', ' ', JFile :: stripExt(basename($file)))))));
	                        JRequest :: setVar('created', date('Y-m-d H:i:s', strtotime(JArrayHelper :: getValue($image_data, 'created_time'))));

	                        if ($image->bind(JRequest :: get()) and $image->check() and $image->store()) {
	                        	$succ++;
	                        } else {
								$err_descr[] = JText :: _('FWGPF_ERROR_STORING_IMAGE').':'.$image->getError();
	                        	$err++;
	                        }
	                        JFile :: delete($file);
			    		} else {
							$err_descr[] = JText :: sprintf('FWGPF_ERROR_WRITING_DOWNLOADED_FILE', $link, $file);
			    			$err++;
			    		}
			    	} else {
						$err_descr[] = JText :: sprintf('FWGPF_ERROR_DOWNLOADING_FILE', $link);
			    		$err++;
			    	}
				} else {
					$err_descr[] = JText :: _('FWGPF_IMAGE_NUMBER_OUT_OF_RANGE');
					$err++;
				}
	    		$number++;
?>
<script type="text/javascript">
<?php
	    		if (count($images['data']) == $number) {
					$session->set('fbimport.success', 0);
					$session->set('fbimport.error', 0);
					$session->set('fbimport.errordescr', array());
					$session->set('fbimport.gallery', 0);
					if ($images_filename = $session->get('fbimport.images') and file_exists($path.$images_filename)) JFile :: delete($path.$images_filename);
					$session->set('fbimport.images', null);

					$msg = JText :: sprintf('FWGPF_IMPORTING_FINISHED', $succ, $err);
					if ($err_descr) foreach ($err_descr as $err) {
						$msg .= '<br/><span class="fwg-error">'.$err.'</span>';
					}
?>
document.getElement('#fwgallery-facebookimages-step-import-notice').setStyle('display', 'none');
document.getElement('#fwgallery-facebookimages-step-import-1').innerHTML = ' - <?php echo addcslashes($msg, "'") ?>';
var form = document.getElement('#fw-facebook-images-form-import');
$(form.stop_button).setStyle('display', 'none');
$(form.import_button).removeAttribute('disabled');
<?php
	    		} else {
					$session->set('fbimport.success', $succ);
					$session->set('fbimport.error', $err);
					$session->set('fbimport.errordescr', $err_descr);
?>
document.getElement('#fwgallery-facebookimages-step-import-1').innerHTML = ' - <?php echo addcslashes(JText :: sprintf('FWGPF_PROCESSING_IMAGE', $number + 1, count($images['data'])), "'") ?> <img src="<?php echo JURI :: root(true); ?>/plugins/fwgallery/facebookimages/facebookimages/assets/images/pleasewait.gif" alt="<?php echo JText :: _('FWGPF_PLEASE_WAIT', true); ?>" />';
document.getElement('#fw-facebook-images-form-import').number.value = <?php echo $number; ?>;
document.getElement('#fw-facebook-images-form-import').set('send', {
	evalScripts: true
}).send();
<?php
	    		}
?>
</script>
<?php
			} else {
				if (JRequest :: getInt('stop')) {
					$session->set('fbimport.success', 0);
					$session->set('fbimport.error', 0);
					$session->set('fbimport.errordescr', array());
					$session->set('fbimport.gallery', 0);
					if ($images_filename = $session->get('fbimport.images') and file_exists($path.$images_filename)) JFile :: delete($path.$images_filename);
					$session->set('fbimport.images', null);

					$msg = JText :: sprintf('FWGPF_IMPORTING_STOPPED', 0, 0);
?>
<script type="text/javascript">
document.getElement('#fwgallery-facebookimages-step-import-notice').setStyle('display', 'none');
document.getElement('#fwgallery-facebookimages-step-import-1').innerHTML = ' - <?php echo addcslashes($msg, "'") ?>';
var form = document.getElement('#fw-facebook-images-form-import');
$(form.stop_button).setStyle('display', 'none');
$(form.import_button).removeAttribute('disabled');
</script>
<?php
					die();
				}
		    	$fwgallery_id = JRequest :: getInt('gallery_id');
		    	$album = array();
				try {
		    		$album = $this->graphReqest('/'.$id);
				} catch (FacebookApiException $e) {
				    error_log($e);
				}

		    	$qty = JArrayHelper :: getValue($album, 'count');
		    	if (!$fwgallery_id) {
		    		if (!empty($album['name'])) {
						$db = JFactory :: getDBO();
						$db->setQuery('SELECT id FROM #__fwg_projects WHERE name = '.$db->quote($album['name']));
						if (!($fwgallery_id = (int)$db->loadResult())) {
							$gallery = JTable :: getInstance('Project', 'Table');
			    			$post = array(
			    				'name' => $album['name'],
			    				'created' => date('Y-m-d H:i:s', strtotime($album['created_time']))
			    			);
			    			if ($gallery->bind($post) and $gallery->check() and $gallery->store()) {
			    				$fwgallery_id = $gallery->id;
			    			} else $msg = JText :: _('FWGPF_ERROR_GALLERY_CREATING').' '.$gallery->getError();
						}
		    		}
		    	}

		    	$count = 0;
		    	$limit = 20;
		    	$buff = array();
				$images = array(
					'data' => array()
				);
				try {
					do {
					    $buff = $this->graphReqest('/'.$id.'/photos', 'get', 'limit='.$limit.'&offset='.($count * $limit));
					    $valid_response = !empty($buff['data']) and is_array($buff['data']);
					    if ($valid_response) $images['data'] = array_merge($images['data'], $buff['data']);
						$count++;
					} while ($valid_response);
				} catch (FacebookApiException $e) {
				    error_log($e);
				}
	    		if ($fwgallery_id) {
	    			$images_filename = md5(microtime().rand());
	    			if (file_exists($path)) {
	    				if (is_writable($path) and JFile :: write($path.$images_filename, serialize($images))) {
							$session->set('fbimport.images', $images_filename);
							$session->set('fbimport.gallery', $fwgallery_id);
							$session->set('fbimport.success', 0);
							$session->set('fbimport.error', 0);
							$session->set('fbimport.errordescr', array());
	    				} else $msg = JText :: _('FWGPF_TMP_FOLDER_IS_NOT_WRITEABLE');
	    			} else $msg = JText :: _('FWGPF_TMP_FOLDER_DOES_NOT_EXISTS');

	    		} else $msg = JText :: _('FWGPF_LOCAL_GALLERY_NOT_CREATED');

				if (!$images) $msg = JText :: _('FWGPF_NO_IMAGES_TO_IMPORT');
?>
<script type="text/javascript">
<?php
				if ($msg) {
?>
document.getElement('#fwgallery-facebookimages-step-import-1').innerHTML = ' - <?php echo addcslashes($msg, "'") ?>';
var form = document.getElement('#fw-facebook-images-form-import');
$(form.import_button).removeAttribute('disabled');
$(form.stop_button).setStyle('display', 'none');
<?php
				} else {
?>
document.getElement('#fwgallery-facebookimages-step-import-1').innerHTML = ' - <?php echo addcslashes(JText :: sprintf('FWGPF_PROCESSING_IMAGE', 1, count($images['data'])), "'"); ?> <img src="<?php echo JURI :: root(true); ?>/plugins/fwgallery/facebookimages/facebookimages/assets/images/pleasewait.gif" alt="<?php echo JText :: _('FWGPF_PLEASE_WAIT', true); ?>" />';
document.getElement('#fw-facebook-images-form-import').step.value = 2;
document.getElement('#fw-facebook-images-form-import').number.value = 0;
document.getElement('#fw-facebook-images-form-import').set('send', {
	evalScripts: true
}).send();
<?php
				}
?>
</script>
<?php
			}
		} else {
?>
<script type="text/javascript">
document.getElement('#fwgallery-facebookimages-step-import-1').innerHTML = ' - <?php echo JText :: _('FWGPF_FACEBOOK_SESSION_HAS_BEEN_EXPIRED', true); ?>';
var form = document.getElement('#fw-facebook-images-form-import');
$(form.import_button).removeAttribute('disabled');
</script>
<?php
		}
	}
	function _exportImages() {
		$msg = '';
		if ($id = JRequest :: getInt('gallery_id') and $this->access_token) {
			$db = JFactory :: getDBO();
			$db->setQuery('
SELECT
	f.filename,
	f.name,
	f.created,
	p.user_id AS _user_id
FROM
	#__fwg_projects AS p,
	#__fwg_files AS f
WHERE
	f.project_id = '.(int)$id.'
	AND
	f.published = 1
	AND
	f.project_id = p.id
ORDER BY
	f.ordering');
			$images = $db->loadObjectList();

			$session = JFactory :: getSession();
			$fbgallery_id = $session->get('fbexport.gallery');
			if ($fbgallery_id) {
				$number = JRequest :: getInt('number');
				$succ = (int)$session->get('fbexport.success');
				$err = (int)$session->get('fbexport.error');
				$err_descr = (array)$session->get('fbexport.errordescr');
                if (JRequest :: getInt('stop')) {
					$session->set('fbexport.success', 0);
					$session->set('fbexport.error', 0);
					$session->set('fbexport.errordescr', array());
					$session->set('fbexport.gallery', 0);

					$msg = JText :: sprintf('FWGPF_EXPORTING_STOPPED', $succ, $err);
					if ($err_descr) foreach ($err_descr as $err) {
						$msg .= '<br/><span class="fwg-error">'.$err.'</span>';
					}
?>
<script type="text/javascript">
document.getElement('#fwgallery-facebookimages-step-export-notice').setStyle('display', 'none');
document.getElement('#fwgallery-facebookimages-step-export-1').innerHTML = ' - <?php echo addcslashes($msg, "'") ?>';
var form = document.getElement('#fw-facebook-images-form-export');
$(form.stop_button).setStyle('display', 'none');
$(form.export_button).removeAttribute('disabled');
</script>
<?php
die();
                } elseif (!empty($images[$number])) {
					$image = $images[$number];
					if (JFHelper :: isFileExists($image)) {
						/* importing an image */
						$photo_details = array(
							'name'=> $image->name,
							'image'=> '@'.JPATH_SITE.'/'.JFHelper :: getFileFilename($image)
						);
						if (JFactory :: getApplication()->isAdmin() and $fanpage = $this->params->get('fan_page')) {
						    $buff = $this->graphReqest('/'.$fanpage.'/?fields=access_token');
						    $photo_details['access_token'] = JArrayHelper :: getValue($buff, 'access_token');
						}
						$result = $this->graphReqest("/{$fbgallery_id}/photos", 'post', $photo_details);
						if ($result) {
							$succ++;
						} else {
							$err_descr[] = JText :: sprintf('FWGPF_ERROR_UPLOADING_PHOTO', $image->name);
							$err++;
						}
					} else {
						$err_descr[] = JText :: sprintf('FWGPF_FILE_NOT_FOUND', $image->filename);
						$err++;
					}
				} else {
					$err_descr[] = JText :: _('FWGPF_IMAGE_NUMBER_OUT_OF_RANGE');
					$err++;
				}
				$number++;
?>
<script type="text/javascript">
<?php
				if ($number == count($images)) {
					$session->set('fbexport.success', 0);
					$session->set('fbexport.error', 0);
					$session->set('fbexport.errorerror', array());
					$session->set('fbexport.gallery', 0);

					$msg = JText :: sprintf('FWGPF_EXPORTING_FINISHED', $succ, $err);
					if ($err_descr) foreach ($err_descr as $err) {
						$msg .= '<br/><span class="fwg-error">'.$err.'</span>';
					}
?>
document.getElement('#fwgallery-facebookimages-step-export-notice').setStyle('display', '');
document.getElement('#fwgallery-facebookimages-step-export-1').innerHTML = ' - <?php echo addcslashes($msg, "'") ?>';
var form = document.getElement('#fw-facebook-images-form-export');
$(form.stop_button).setStyle('display', 'none');
$(form.export_button).removeAttribute('disabled');
<?php
				} else {
					$session->set('fbexport.success', $succ);
					$session->set('fbexport.error', $err);
					$session->set('fbexport.errordescr', $err_descr);
?>
document.getElement('#fwgallery-facebookimages-step-export-1').innerHTML = ' - <?php echo addcslashes(JText :: sprintf('FWGPF_PROCESSING_IMAGE', $number + 1, count($images)), "'") ?> <img src="<?php echo JURI :: root(true); ?>/plugins/fwgallery/facebookimages/facebookimages/assets/images/pleasewait.gif" alt="<?php echo JText :: _('FWGPF_PLEASE_WAIT', true); ?>" />';
document.getElement('#fw-facebook-images-form-export').number.value = <?php echo $number; ?>;
document.getElement('#fw-facebook-images-form-export').set('send', {
	evalScripts: true
}).send();
<?php
				}
?>
</script>
<?php
			} else {
                $fbgallery_id = JRequest :: getString('facebook_gallery_id');
                if (!$fbgallery_id) {
                    $db->setQuery('SELECT name FROM #__fwg_projects WHERE id = '.(int)$id);
                    $gallery_name = trim($db->loadResult());
                    if ($gallery_name and $galleries = $this->loadFacebookGalleries()) {
                        /* look for a gallery and create new, if not found */
                        foreach ($galleries as $gallery) {
                        	if (preg_replace('/\s+\(\d*\)$/', '', $gallery->name) == $gallery_name) {
	                        	echo 'found!';
	                            $fbgallery_id = $gallery->id;
                        	}
                        }
                    }
                    /* create new facebook gallery */
                    if (!$fbgallery_id) {
                        try {
							$user = array();
							$params = array(
                                'name' => $gallery_name
                            );
							if (JFactory :: getApplication()->isAdmin() and $fanpage = $this->params->get('fan_page')) {
							    $user = $this->graphReqest('/'.$fanpage);
							    $buff = $this->graphReqest('/'.$fanpage.'/?fields=access_token');
							    $params['access_token'] = JArrayHelper :: getValue($buff, 'access_token');
							} else {
							    $user = $this->graphReqest('/me/');
							}

                            if ($album = $this->graphReqest('/'.JArrayHelper :: getValue($user, 'id').'/albums', 'post', $params)) {
								/* found that facebook needs some time to populate data */
                            	sleep(2);
                                if ($galleries = $this->loadFacebookGalleries()) {
                                    /* look for a gallery and create new, if not found */
                                    foreach ($galleries as $gallery) if (preg_replace('/\s+\(\d*\)$/', '', $gallery->name) == $gallery_name) {
                                        $fbgallery_id = $gallery->id;
                                    }
                                }

                            }
                        } catch (FacebookApiException $e) {
                            error_log($e);
                        }
                    }
                }
				if ($fbgallery_id) $session->set('fbexport.gallery', $fbgallery_id);
				else $msg = JText :: _('FWGPF_FACEBOOK_GALLERY_ID_NOT_FOUND');

				if (!$images) $msg = JText :: _('FWGPF_NO_IMAGES_TO_EXPORT');
?>
<script type="text/javascript">
<?php
				if ($msg) {
?>
document.getElement('#fwgallery-facebookimages-step-export-1').innerHTML = ' - <?php echo addcslashes($msg, "'") ?>';
var form = document.getElement('#fw-facebook-images-form-export');
$(form.export_button).removeAttribute('disabled');
$(form.stop_button).setStyle('display', 'none');
<?php
				} else {
?>
document.getElement('#fwgallery-facebookimages-step-export-1').innerHTML = ' - <?php echo addcslashes(JText :: sprintf('FWGPF_PROCESSING_IMAGE', 1, count($images)), "'") ?> <img src="<?php echo JURI :: root(true); ?>/plugins/fwgallery/facebookimages/facebookimages/assets/images/pleasewait.gif" alt="<?php echo JText :: _('FWGPF_PLEASE_WAIT', true); ?>" />';
document.getElement('#fw-facebook-images-form-export').number.value = 0;
document.getElement('#fw-facebook-images-form-export').set('send', {
	evalScripts: true
}).send();
<?php
				}
?>
</script>
<?php
			}
		} else {
?>
<script type="text/javascript">
document.getElement('#fwgallery-facebookimages-step-export-1').innerHTML = ' - <?php echo JText :: _('FWGPF_FACEBOOK_SESSION_HAS_BEEN_EXPIRED', true); ?>';
var form = document.getElement('#fw-facebook-images-form-export');
$(form.export_button).removeAttribute('disabled');
</script>
<?php
		}
	}
	function graphReqest($path, $method='get', $data='') {
		$url = 'https://graph.facebook.com'.$path.((strpos($path, '?') === false)?'?':'&').'access_token='.$this->access_token;

		return json_decode($this->request($url, $method, $data), true);
	}
	function request($url, $method='get', $data='') {
		if (function_exists('curl_init')) {
			$ch = curl_init();
			if ($method == 'get' and $data) {
				if (is_array($data)) {
					foreach ($data as $key=>$val) {
						$url .= '&'.$key.'='.urlencode($val);
					}
				} elseif (is_string($data)) {
					$url .= '&'.$data;
				}
				$data = '';
			} elseif ($method == 'post') {
				curl_setopt ($ch, CURLOPT_POST, true);
				curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
			}

			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

			if (strpos($url, 'https') !== false) {
			    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
			}

			$result = curl_exec ($ch);
			curl_close($ch);

			return $result;
		}
	}
}
?>