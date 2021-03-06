<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * File Database Row
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityFile extends ComFilesModelEntityNode
{
	public static $image_extensions = array('jpg', 'jpeg', 'gif', 'png', 'tiff', 'tif', 'xbm', 'bmp');

	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        $this->addBehavior('com:files.database.behavior.thumbnail');
	}

	public function save()
	{
		$context = $this->getContext();
		$context->result = false;

		$is_new = $this->isNew();

		if ($this->invokeCommand('before.save', $context) !== false)
		{
			$context->result = $this->_adapter->write(!empty($this->contents) ? $this->contents : $this->file);
			$this->invokeCommand('after.save', $context);
        }

		if ($context->result === false) {
			$this->setStatus(KDatabase::STATUS_FAILED);
		} else {
            $this->setStatus($is_new ? KDatabase::STATUS_CREATED : KDatabase::STATUS_UPDATED);
        }

		return $context->result;
	}


    public function getPropertyFilename()
    {
        return ltrim(pathinfo(' '.strtr($this->name, array('/' => '/ ')), PATHINFO_FILENAME));
    }

    public function getPropertySize()
    {
        if($metadata = $this->_adapter->getMetadata())
        {
            if(isset($metadata['size'])) {
                return $metadata['size'];
            }
        }

        return false;
    }

    public function getPropertyExtension()
    {
        if($metadata = $this->_adapter->getMetadata())
        {
            if(isset($metadata['extension'])) {
                return $metadata['extension'];
            }
        }

        return false;
    }

    public function getPropertyModifiedDate()
    {
        if($metadata = $this->_adapter->getMetadata())
        {
            if(isset($metadata['modified_date'])) {
                return $metadata['modified_date'];
            }
        }

        return false;
    }

    public function getPropertyMimetype()
    {
        if($metadata = $this->_adapter->getMetadata())
        {
            if(isset($metadata['mimetype'])) {
                return $metadata['mimetype'];
            }
        }

        return false;
    }

    public function getPropertyWidth()
    {
        if($this->isImage())
        {
            $size = $this->_adapter->getImageSize();

            if ($size !== false) {
                return $size[0];
            }
        }

        return false;
    }

    public function getPropertyHeight()
    {
        if($this->isImage())
        {
            $size = $this->_adapter->getImageSize();

            if ($size !== false) {
                return $size[1];
            }
        }

        return false;
    }

    public function getPropertyMetadata()
    {
        return $this->_adapter->getMetadata();
    }

    public function toArray()
    {
        $data = parent::toArray();

        unset($data['file']);
		unset($data['contents']);

		$data['metadata'] = $this->metadata;

		if ($this->isImage()) {
			$data['type'] = 'image';
		}

        return $data;
    }

	public function isImage()
	{
		return in_array(strtolower($this->extension), self::$image_extensions);
	}
}
