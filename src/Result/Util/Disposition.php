<?php
namespace Creios\Creiwork\Framework\Result\Util;

/**
 * Class Disposition
 * @package Creios\Creiwork\Framework\Result\Util
 */
class Disposition
{

    const INLINE = 'inline';
    const ATTACHMENT = 'attachment';
    const FORM_DATA = 'form-data';
    const SIGNAL = 'signal';
    const ALERT = 'alert';
    const ICON = 'icon';
    const RENDER = 'render';
    const RECIPIENT_LIST_HISTORY = 'recipient-list-history';
    const SESSION = 'session';
    const AIB = 'aib';
    const EARLY_SESSION = 'early-session';
    const RECIPIENT_LIST = 'recipient-list';
    const NOTIFICATION = 'notification';
    const BY_REFERENCE = 'by-reference';
    const INFO_PACKAGE = 'info-package';
    const RECORDING_SESSION = 'recording-session';

    /** @var string */
    private $type;
    /** @var string */
    private $filename = null;

    /**
     * Disposition constructor.
     * @param string $type
     */
    public function __construct($type = self::INLINE)
    {
        $this->type = $type;
    }

    /**
     * @param string $filename
     * @return $this
     */
    public function withFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }
    
}
