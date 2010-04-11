<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Hauke Hain <hhpreuss@googlemail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   38: class tx_mmforumcomments_hooks
 *   48:     public function processPostInsertArray($insertArray, $obj)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
require_once(t3lib_extMgm::extPath('mm_forum_comments').'lib/class.tx_mmforumcomments_div.php');

 class tx_mmforumcomments_hooks {

/**
 * Clears the cache
 *
 * @param	array		$obj The calling object
 * @param	object		$insertArray The data array for inserting into the database
 * @return	array		$insertArray
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 */
	public function processPostInsertArray($insertArray, $obj) {
	  $conf = tx_mmforumcomments_div::getInstallToolSettings();

	  if ($conf['mmforumcomments_clearCache'] &&
        (($pid = tx_mmforumcomments_div::getCommentPID($insertArray['topic_id'],
          'tx_mmforumcomments_links')) > 0)) {
      tx_mmforumcomments_div::clearPageCache($pid);
    }

    return $insertArray;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/lib/class.tx_mmforumcomments_hooks.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/lib/class.tx_mmforumcomments_hooks.php']);
}

?>