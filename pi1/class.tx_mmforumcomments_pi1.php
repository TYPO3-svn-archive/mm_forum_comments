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
 *   51: class tx_mmforumcomments_pi1 extends tslib_pibase
 *   64:     function main($content, $conf)
 *  101:     protected function createTopic($fid, $aid)
 *  118:     function getParameter()
 *  141:     function getTopicID($id, $parameters)
 *  177:     protected function getPostFactory()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

require_once(t3lib_extMgm::extPath('mm_forum_comments').'lib/class.tx_mmforumcomments_div.php');
require_once(t3lib_extMgm::extPath('mm_forum_comments').'lib/class.tx_mmforumcomments_createcomments.php');


/**
 * Plugin 'mm_forum comments' for the 'mm_forum_comments' extension.
 *
 * @author	Hauke Hain <hhpreuss@googlemail.com>
 * @package	TYPO3
 * @subpackage	tx_mmforumcomments
 */
class tx_mmforumcomments_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_mmforumcomments_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_mmforumcomments_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'mm_forum_comments';	// The extension key.
	var $pi_checkCHash = true;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The		content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		$relationTable = 'tx_mmforumcomments_links';
		$pid = tx_mmforumcomments_div::getPageID();

  	$setup = tx_mmforumcomments_div::loadTSSetupForPage($pid);
		$parameters = tx_mmforumcomments_div::getParameter($this->conf['parameters.']);

		$topicID = tx_mmforumcomments_div::getTopicID($pid, $parameters, $relationTable);

    if ($topicID == 0) { //create new topic
      $data = tx_mmforumcomments_div::getTypoScriptData($parameters[2], intval($parameters[1])==0 ? $pid : intval($parameters[1]), $this->conf);

  		$commcat = tx_mmforumcomments_div::getCommentCategoryUID($parameters[2], $this->conf);
  		$commaut = tx_mmforumcomments_div::getTopicAuthorUID($parameters[2], $this->conf);
  		$subject = tx_mmforumcomments_div::getTSparsedString('subject', $parameters[2], $this->conf, $data);
  		$posttext = tx_mmforumcomments_div::getTSparsedString('posttext', $parameters[2], $this->conf, $data);
  		$link = tx_mmforumcomments_div::getTSparsedString('linktopage', $parameters[2], $this->conf, $data);
  		$date = tx_mmforumcomments_div::getDate($parameters[2], $this->conf, $data);

      tx_mmforumcomments_createcomments::createTopic($pid, $parameters,
              $commcat, $commaut,
              tx_mmforumcomments_div::prepareString($subject),
              tx_mmforumcomments_div::prepareString($posttext.$link),
              $date, $relationTable,
              $setup['plugin.']['tx_mmforum.']['storagePID']);

      $topicID = tx_mmforumcomments_div::getTopicID($pid, $parameters, $relationTable);
    }

    #$this->getDisplayComments($setup['plugin.']['tx_mmforum_pi1.']);
    #$content = $this->displayComments->getCommentsList();

		$content='
			<strong>This is a few paragraphs:</strong><br />
			<p>This is line 1</p>
			<p>This is line 2</p>

			<h3>This is a form:</h3>
			<form action="'.$this->pi_getPageLink($GLOBALS['TSFE']->id).'" method="POST">
				<input type="text" name="'.$this->prefixId.'[input_field]" value="'.htmlspecialchars($this->piVars['input_field']).'">
				<input type="submit" name="'.$this->prefixId.'[submit_button]" value="'.htmlspecialchars($this->pi_getLL('submit_button_label')).'">
			</form>
			<br />
			<p>You can click here to '.$this->pi_linkToPage('get to this page again',$GLOBALS['TSFE']->id).'</p>
		';

		return $this->pi_wrapInBaseClass($content);
	}
  
/**
	*
	* Instantiates and returns the tx_mmforumcomments_displaycomments. It is
	* handled as a singleton instance, i.e. it will only be instantiated
	* ONCE and then reused.
	*
	* @param   array $conf The configuration array of the mm_forum.
	* @return tx_mmforumcomments_displaycomments An instance of the 
	*                                            tx_mmforumcomments_displaycomments
	*                                            class.
	*
	*/

/*	protected function getDisplayComments($conf) {
		if(!$this->displayComments) {
			$this->displayComments = t3lib_div::getUserObj('EXT:mm_forum_comments/lib/class.tx_mmforumcomments_displaycomments.php:tx_mmforumcomments_displaycomments');
			$this->displayComments->init($conf, $this->cObj);
		} return $this->displayComments;
	}*/
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/pi1/class.tx_mmforumcomments_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/pi1/class.tx_mmforumcomments_pi1.php']);
}

?>