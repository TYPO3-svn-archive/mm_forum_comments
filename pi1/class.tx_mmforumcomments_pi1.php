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

		$this->relationTable = 'tx_mmforumcomments_links';
		$pid = $this->getPageID();
		$this->loadTSSetupForPage($pid);
		$parameters = $this->getParameter();
		$commcat = $this->getCommentCategory($parameters[2]);
		$commaut = $this->getTopicAuthor($parameters[2]);
		$subject = $this->getSubject($parameters[2]);
		$posttext = $this->getPosttext($parameters[2]);
		$date = $this->getDate($parameters[2]);
		$topicID = $this->getTopicID($pid, $parameters, $commcat, $commaut, $subject
               $posttext, $date);

    if ($topicID > 0) {
      //show comments
    } else {
      #$this->createTopic($pid, $parameters);
    }
	debugster($this->setup['plugin.']['tx_mmforumcomments_pi1.']);
	#debugster($this->setup['plugin.']['tx_mmforum_pi1.']['pid_forum']);
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
 * Creates a new commenting topic.
 * This method uses the mm_forum postfactory
 * interface in order to create the new topic.
 *
 * @param   int     $pid:     The UID of the page the comments are shown
 * @param   array   $para:    0: The name of the parameter; 1: the unique
 *                               id (value) of the parameter
 * @param   int     $parauid: The UID (value) of the URL parameter
 * @param   int     $fid:     The UID of the forum the new topic is to be created in
 * @param   int     $aid:     The UID of the fe_user creating this topic
 * @param   string  $subject: The topic's subject
 * @param   string  $text:    The topic's first post's text
 * @param   int     $date:    The date of topic creation as unix timestamp
 * @return	void
 */
	protected function createTopic($pid, $para, $fid, $aid, $subject, $text, $date) {
  	$tid = $this->getPostFactory()->create_topic($fid, $aid, $subject, $text,
            $date,
				    dechex(ip2long(t3lib_div::getIndpEnv('REMOTE_ADDR'))), array(),
				    0, false, false, false
			     );

    // Insert relation
		$insertArray_page2forum = array('pid' => $pid,	'fid' => $tid,
		                                'parameter' => $para[0],
		                                'parameteruid' => $para[1]
                                   );

    $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->relationTable, $insertArray_page2forum);
	}

  /**
    * If a starting points are set the first one is returned, otherwise the
    * id of the current page.    
    *
    * @return   integer   page uid
    */
	private function getPageID() {
    if (empty($this->cObj->data['pages'])) {
      return $GLOBALS['TSFE']->id;
    } else {
      $pids = explode(',', $this->cObj->data['pages']);
      return $pids[0];
    }
  }
  
  function getCommentCategory($key) {
    return empty($this->conf['parameters.'][$key . '.']['pageCommentCategory']) ? $this->setup['plugin.']['tx_mmforumcomments_pi1.']['pageCommentCategory'] : $this->conf['parameters.'][$key . '.']['pageCommentCategory'];
  }
  
  function getTopicAuthor($key) {
    return empty($this->conf['parameters.'][$key . '.']['pageTopicAuthor']) ? $this->setup['plugin.']['tx_mmforumcomments_pi1.']['pageTopicAuthor'] : $this->conf['parameters.'][$key . '.']['pageTopicAuthor'];
  }

	/**
	 * Returns search parameter
	 *
	 * @return	array / void	returns nothing if no parameters are configured in
	 *                        TypoScipt or the parameter name and unique parameter
	 *                        value in the linktable of the extension   	 
	 */
	function getParameter() {
    if (is_array($this->conf['parameters.']) === false) {
      return;
    }

    foreach($this->conf['parameters.'] as $key => $value) {
      $key = substr($key, 0, strlen($key)-1);
      $uidkey = $value['uid'];
      $gp = t3lib_div::_GP($key);

      if (!empty($gp[$uidkey])) {
        return array($key . '->' . $uidkey, $gp[$uidkey], $key);
      }
    }
  }

	/**
	 * Returns the ID of the exsting topic (or zero if none is found)
	 *
	 * @param	integer		$pid: ID of the page where the comments are located
	 * @param	array 		$parameters: 0: The name of the parameter; 1: the unique
	 *                               id (value) of the parameter 	 
	 * @return	integer	ID of the exsting topic
	 */
	function getTopicID($pid, $parameters) {
    if (intval($pid) > 0) {
      $where = '';

      if (intval($parameters[1]) > 0) {
        $where = ' AND parameter LIKE \'' . $parameters[0] .
                 '\' AND parameteruid = ' . $parameters[1];
      } else {
        $where = ' AND parameteruid = 0';
      }

      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('fid',
  					 $this->relationTable, 'pid=' . intval($pid) . $where,
  					 '', '', '1');

			if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) == 1) {
			 $topicID = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			 $topicID = intval($topicID['fid']);
			 $GLOBALS['TYPO3_DB']->sql_free_result($res);

			 return $topicID;
			}
    }

    return 0;
  }

/**
 * Instantiates and returns the mm_forum post factory. The post factory
 * is handled as a singleton instance, i.e. it will only be instantiated
 * ONCE and then reused.
 *
 *
 * @return	tx_mmforum_postfactory		An instance of the tx_mmforum_postfactory
 *                                    class. 
 */
	protected function getPostFactory() {
		if(!$this->postFactory) {
			$this->postFactory = t3lib_div::getUserObj('EXT:mm_forum/pi1/class.tx_mmforum_postfactory.php:tx_mmforum_postfactory');
			$this->postFactory->init(array('storagePID' => $this->setup['plugin.']['tx_mmforum.']['storagePID']));
		} return $this->postFactory;
	}

  /**
		 *
		 * Loads the TypoScript setup for a specific page.
		 * This function loads the complete TypoScript setup for a specific
		 * page -- usually the page the tt_news record is saved on. The setup
		 * is needed in order to determine the mm_forum storage PID.
		 * The t3lib_tsparser_ext class is used for doing this.
		 *
		 * @param  int $pid The page UID for which the setup is to be loaded.
		 * @return void
		 *
		 */

	protected function loadTSSetupForPage($pid) {
		if(!$this->setup) {
			$tmpl = t3lib_div::makeInstance("t3lib_tsparser_ext");
			$tmpl->tt_track = 0;
			$tmpl->init();

			$sys_page = t3lib_div::makeInstance("t3lib_pageSelect");
			$rootLine = $sys_page->getRootLine($pid);
			$tmpl->runThroughTemplates($rootLine,0);
			$tmpl->generateConfig();
			$this->setup = $tmpl->setup;
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/pi1/class.tx_mmforumcomments_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/pi1/class.tx_mmforumcomments_pi1.php']);
}

?>