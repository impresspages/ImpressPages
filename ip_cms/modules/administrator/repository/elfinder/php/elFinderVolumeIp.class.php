<?php

/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */



class elFinderVolumeIp extends elFinderVolumeLocalFileSystem {
    public function nameAccepted($fileName)
    {
        return true;
    }
}
