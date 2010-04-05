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
 *   35: class tx_mmforumcomments_displaycomments
 *
 * TOTAL FUNCTIONS: 0
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
 class tx_mmforumcomments_displaycomments {

 	/**
	 *
	 * Initializes the displaycomments class.
	 *
	 * @author  Hauke Hain <hhpreuss@googlemail.com>
	 * @param   array $conf The configuration array of the calling object.
	 * @return  void
	 *
	 */

/*	public function init($conf, $cObj) {
    $this->conf = $conf;
		$this->cObj = $cObj;
		$this->getPostFunctions();
	}*/

/**
	*
	* Instantiates and returns the mm_forum post functions. The post functions
	* is handled as a singleton instance, i.e. it will only be instantiated
	* ONCE and then reused.
	*
	* @return tx_mmforum_postfunctions An instance of the tx_mmforum_postfunctions
	*                                  class.
	*
	*/
/*	protected function getPostFunctions() {
		if(!$this->postFunctions) {
			$this->postFunctions = t3lib_div::getUserObj('EXT:mm_forum/pi1/class.tx_mmforum_postfunctions.php:tx_mmforum_postfunctions');
		}

    return $this->postFunctions;
	}*/

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	/*public function getCommentsList() {
    return $this->postFunctions->list_post('', $this->conf, '');
  }*/

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/lib/class.tx_mmforumcomments_displaycomments.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/lib/class.tx_mmforumcomments_displaycomments.php']);
}

?>