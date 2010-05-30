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
 *   53: class tx_mmforumcomments_div
 *   66:     public static function createTopicForRecord(&$parameters, &$conf, $pid, $storagePID, &$pObj, $useTSDataHook=true)
 *   99:     public static function loadTSSetupForPage($pid)
 *  120:     private static function getSingle(&$data, &$tsObjectKey, &$tsObjectConf)
 *  153:     public static function getTSparsedString($tskey, $key, $conf, $data)
 *  168:     public static function prepareString($str)
 *  196:     public static function prepareURL($url)
 *  227:     public function getPageID()
 *  244:     public static function getCommentCategoryUID($key, $conf)
 *  256:     public static function getTopicAuthorUID($key, $conf)
 *  269:     public static function getDate($key, $conf, &$data)
 *  286:     public static function getTypoScriptData($key, $uid, $conf, &$pObj, $useHook=true)
 *  338:     public static function getParameter($paraconf)
 *  372:     public static function getCommentPID($tid, $relationTable=false)
 *  403:     public static function getTopicID($pid, $parameters, $relationTable=false)
 *  442:     public static function getFirstTopicPostID($topicId, $storagePID)
 *  465:     public static function getFirstTopicPostToShowID($topicId, $storagePID, $excludePostID=0)
 *  491:     public static function clearPageCache($pid)
 *  505:     public static function getInstallToolSettings()
 *
 * TOTAL FUNCTIONS: 18
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
 class tx_mmforumcomments_div {
		/**
 * This method uses the mm_forum postfactory interface in order to create
 * a new topic for comments.
 *
 * @param	array		&$parameters The URL parameters.
 * @param	array		&$conf The mm_forum_comments TypoScript setup.
 * @param	integer	$pid page id for the relation table.
 * @param	integer	$storagePID storage page id of mm_forum.
 * @param	$this		$pObj: parent object
 * @param	bool		$useTSDataHook: Whether the TypoScriptDataHook should be used
 * @return	void
 */
	public static function createTopicForRecord(&$parameters, &$conf, $pid, $storagePID, &$pObj, $useTSDataHook=true, $data=false) {
    require_once(t3lib_extMgm::extPath('mm_forum_comments').'lib/class.tx_mmforumcomments_createcomments.php');

    if (!(is_array($data)) || ($useTSDataHook)) {
      $data = tx_mmforumcomments_div::getTypoScriptData($parameters[2], intval($parameters[1])==0 ? $pid : intval($parameters[1]), $conf, $pObj, $useTSDataHook);
    }

  	$commcat = tx_mmforumcomments_div::getCommentCategoryUID($parameters[2], $conf);
  	$commaut = tx_mmforumcomments_div::getTopicAuthorUID($parameters[2], $conf);
  	$subject = tx_mmforumcomments_div::getTSparsedString('subject', $parameters[2], $conf, $data);
  	$posttext = tx_mmforumcomments_div::getTSparsedString('posttext', $parameters[2], $conf, $data);
  	$link = tx_mmforumcomments_div::getTSparsedString('linktopage', $parameters[2], $conf, $data);
  	$date = tx_mmforumcomments_div::getDate($parameters[2], $conf, $data);

    tx_mmforumcomments_createcomments::createTopic($pid, $parameters,
            $commcat, $commaut,
            tx_mmforumcomments_div::prepareString($subject),
            tx_mmforumcomments_div::prepareString($posttext.$link),
            $date, tx_mmforumcomments_div::getRelationTable(),
            $storagePID);
	}

	static public function getRelationTable() {
    return 'tx_mmforumcomments_links';
  }

/**
 * Loads the TypoScript setup for a specific page.
 * This function loads the complete TypoScript setup for a specific
 * page.
 * The t3lib_tsparser_ext class is used for doing this.
 *
 * @param	int		$pid The page UID for which the setup is to be loaded.
 * @return	array		TypoScript setup
 */
	public static function loadTSSetupForPage($pid) {
			$tmpl = t3lib_div::makeInstance('t3lib_tsparser_ext');
			$tmpl->tt_track = 0;
			$tmpl->init();

			$sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
			$rootLine = $sys_page->getRootLine($pid);
			$tmpl->runThroughTemplates($rootLine,0);
			$tmpl->generateConfig();

      return $tmpl->setup;
	}

	/**
	 * Parses data through typoscript.
	 *
	 * @param	array		$data Data which will be passed to the typoscript.
	 * @param	string		$tsObjectKey The typoscript which will be called.
	 * @param	array		$tsObjectConf TS object configuration
	 * @return	string
	 */
	private static function getSingle(&$data, &$tsObjectKey, &$tsObjectConf) {
    $ret = '';

	  if ($GLOBALS['TSFE'] === null) {
	   //parse TypoScript by yourself if you work from the backend
     require_once(t3lib_extMgm::extPath('mm_forum_comments') . 'lib/class.tx_mmforumcomments_typoscript.php');

	   foreach (array_keys($tsObjectConf[$tsObjectKey . '.']) as $key) {
	    // work only with TEXT objects
      if ((strstr($key, '.') && is_array($tsarr = $tsObjectConf[$tsObjectKey . '.'][$key]) && strtoupper($tsObjectConf[$tsObjectKey . '.'][str_replace('.','',$key)]) === 'TEXT') && (tx_mmforumcomments_typoscript::returnResultOfTSif($data, $tsarr))) {
        $ret .= tx_mmforumcomments_typoscript::getTSvalueOfTEXT($data, $tsarr);
      }
     }

    } else {
  		$cObj = t3lib_div::makeInstance('tslib_cObj');
  		$cObj->data = $data;
  		$ret = $cObj->cObjGetSingle($tsObjectConf[$tsObjectKey], $tsObjectConf[$tsObjectKey . '.']);
    }

		return $ret;
	}

/**
 * Returns string created by TypoScript parser (GetSingle)
 *
 * @param	string		$tskey: The TypoScript which will be called
 * @param	string		$key: TypoScript key where to look in the parameter list
 * @param	array		$conf: The TypoScript setup
 * @param	array		$data: The data that is available via TypoScript
 * @return	string		The subject of the comment thread
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 */
  public static function getTSparsedString($tskey, $key, $conf, $data) {
    if (!empty($conf['parameters.'][$key . '.'][$tskey])) {
      $conf = $conf['parameters.'][$key . '.'];
    }

    return tx_mmforumcomments_div::getSingle($data, $tskey, $conf);
  }

/**
 * Replaces &nbsps; and a-tags with BBcode equivalents
 *
 * @param	string		$str: The string that gets prepared
 * @return	string		The prepared string
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 */
  public static function prepareString($str) {
    $str = str_replace('&nbsp;', ' ', $str);

    // BBCode link creation
    $output = array();
		preg_match_all('/(\<a\shref=")(.*?)("{1}\s{0,1}\>{1})(.*?)(\<\/a\>)/', $str, $output);

		if (is_array($output) && sizeof($output) == 6 && is_array($output[0])) {
  		for($i = 0; $i < sizeof($output[0]); ++$i)
      {
    		$url = tx_mmforumcomments_div::prepareURL($output[2][$i]);
    		$linkname = $output[4][$i];
    		$replacement = '[URL="' . $url . '"]' . $linkname . '[/URL]';
    		$str = str_replace($output[0][$i], $replacement, $str);
      }
    }

    return $str;
  }

/**
 * Returns an absolute URL instead of an relative
 * Uses baseURL to do so or php SERVER_NAME is baseURL isn't available.
 *
 * @param	string		$url: The URL that should become an absolute URL
 * @return	string		The absolute URL
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 */
  public static function prepareURL($url) {
    if (strpos($url, 'http://') === false) {
      $baseURL = $GLOBALS['TSFE']->config['config']['baseURL'];

      if (empty($baseURL)) {
        $baseURL = $_SERVER['SERVER_NAME'];
      }

    	if (strrpos($baseURL, '/') != strlen($baseURL)-1) {
        $baseURL .= '/';
      }

      if (strpos($url, $baseURL) === false) {
        $url = $baseURL . $url;
      }

    	if (strpos($url, 'http://') === false) {
        $url = 'http://' . $url;
      }
    }

    return $url;
  }

  /**
 * If a starting points are set the first one is returned, otherwise the
 * id of the current page.
 *
 * @return	integer		page uid
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 */
	public function getPageID() {
    if (empty($this->cObj->data['pages'])) {
      return $GLOBALS['TSFE']->id;
    } else {
      $pids = explode(',', $this->cObj->data['pages']);
      return $pids[0];
    }
  }

/**
 * Returns the UID of the forum where the comment topic is / will be located
 *
 * @param	string		$key: TypoScript key where to look in the parameter list
 * @param	array		$conf: The TS configuration
 * @return	string		forum UID
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 */
  public static function getCommentCategoryUID($key, $conf) {
    return empty($conf['parameters.'][$key . '.']['pageCommentCategory']) ? $conf['pageCommentCategory'] : $conf['parameters.'][$key . '.']['pageCommentCategory'];
  }

/**
 * Returns the UID of the comment topic author
 *
 * @param	string		$key: TypoScript key where to look in the parameter list
 * @param	array		$conf: The TS configuration
 * @return	string		fe_user UID
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 */
  public static function getTopicAuthorUID($key, $conf) {
    return empty($conf['parameters.'][$key . '.']['pageTopicAuthor']) ? $conf['pageTopicAuthor'] : $conf['parameters.'][$key . '.']['pageTopicAuthor'];
  }

/**
 * Returns the posting date for the comment thread
 *
 * @param	string		$key: TypoScript key where to look in the parameter list
 * @param	array		$conf: The TS configuration
 * @param	array		$data: The data that is available via TypoScript
 * @return	integer		Unix timestamp
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 */
  public static function getDate($key, $conf, &$data) {
    $datecolumn = empty($conf['parameters.'][$key . '.']['postdate']) ? $conf['postdate'] : $conf['parameters.'][$key . '.']['postdate'];

    return intval($data[$datecolumn])==0 ? time() : intval($data[$datecolumn]);
  }

/**
 * Returns the columns of a specific datarow to be available in TypoScript
 *
 * @param	string		$key: TypoScript key where to look in the parameter list
 * @param	integer		$uid: The uid of the datatable
 * @param	array		$conf: The TS configuration
 * @param	array		$pObj: Reference to the parent
 * @param	boolean		$useHook: Whether to use the hook or not
 * @return	array		data for TypoScript
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 */
  public static function getTypoScriptData($key, $uid, $conf, &$pObj, $useHook=true) {
    $recordsTable = empty($conf['parameters.'][$key . '.']['recordsTable']) ? $conf['recordsTable'] : $conf['parameters.'][$key . '.']['recordsTable'];

    if (empty($recordsTable)) {
      $recordsTable = 'pages';
    } else {
      $recordsTable = $GLOBALS['TYPO3_DB']->fullQuoteStr($recordsTable,'');
      $recordsTable = substr($recordsTable, 1 , strlen($recordsTable)-2);
    }

    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
 					                                        $recordsTable,
                                                  'uid=' . intval($uid),
                                                  '', '', '1');

		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) == 1) {
		 $data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		 $GLOBALS['TYPO3_DB']->sql_free_result($res);

      if($useHook) {
        // hook to modify the result of the function getTypoScriptData
    		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mm_forum_comments']['getTypoScriptDataHook'])) {
    			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mm_forum_comments']['getTypoScriptDataHook'] as $userFunc) {
    			  $params = array(
    					'data' => &$data,
    					'recordsTable' => &$recordsTable,
    					'key' => &$key,
    					'uid' => &$uid,
    					'conf' => &$conf,
    				);
    				t3lib_div::callUserFunction($userFunc, $params, $pObj);
    			}
    		}
      }

		 return $data;
		}

    return array();
  }

	/**
	 * Returns search parameter (WHERE clause)
	 *
	 * @param	array		$paraconf: The parameter segment of the TS plugin setup
	 *                  TypoScipt or the parameter name and unique parameter
	 *                  value in the linktable of the extension
	 * @return	array		returns nothing if no parameters are configured in
	 * @author  Hauke Hain <hhpreuss@googlemail.com>
	 */
	public static function getParameter($paraconf) {
    if (is_array($paraconf) === false) {
      return array();
    }

    // hook to return own parameter
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mm_forum_comments']['overwriteParameter'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mm_forum_comments']['overwriteParameter'] as $userFunc) {
				$return = t3lib_div::callUserFunction($userFunc, $paraconf, $this);
			}
			return $return;
		}

    foreach($paraconf as $key => $value) {
      $key = substr($key, 0, strlen($key)-1);
      $uidkey = $value['uid'];
      $gp = t3lib_div::_GP($key);

      if (!empty($gp[$uidkey])) {
        return array($key . '->' . $uidkey, $gp[$uidkey], $key);
      }
    }

    return array();
  }

	/**
	 * Returns the page ID of the comment page
	 *
	 * @param	integer		$tid: ID of the exsting topic
	 * @param	[type]		$relationTable: ...
	 * @return	integer		page ID
	 * @author  Hauke Hain <hhpreuss@googlemail.com>
	 */
	public static function getCommentPID($tid, $relationTable=false) {
	  if($relationTable===false) {
      $relationTable = tx_mmforumcomments_div::getRelationTable();
    }

	  $PID = 0;
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid',
  				 $relationTable, 'fid=' . intval($tid),
  				 '', '', '1');

		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) == 1) {
		  $PID = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$PID = intval($PID['pid']);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}

    return $PID;
  }

	/**
	 * Returns the ID of the exsting topic (or zero if none is found)
	 *
	 *
	 * @param	integer		$pid: ID of the page where the comments are located
	 * @param	array		$parameters: 0: The name of the parameter; 1: the unique
	 *                                id (value) of the parameter
	 * @param	string		$relationTable: Table name
	 * @return	integer		ID of the exsting topic
	 * @author  Hauke Hain <hhpreuss@googlemail.com>
	 */
	public static function getTopicID($pid, $parameters, $relationTable=false) {
	  if($relationTable===false) {
      $relationTable = tx_mmforumcomments_div::getRelationTable();
    }

    if (intval($pid) > 0) {
      $where = '';

      if (intval($parameters[1]) > 0) {
        $where = ' AND parameter LIKE \'' . $parameters[0] .
                 '\' AND parameteruid = ' . $parameters[1];
      } else {
        $where = ' AND parameteruid = 0';
      }

      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('fid',
  					 $relationTable, 'pid=' . intval($pid) . $where,
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
	 * Returns the ID of the first topicpost (or zero if none is found)
	 *
	 * @param	integer		$topicId: ID of the topic
	 * @param	array		$storagePID: ID of the page where the forendata is located
	 * @return	integer		ID of the first topicpost
	 * @author  Hauke Hain <hhpreuss@googlemail.com>
	 */
	public static function getFirstTopicPostID($topicId, $storagePID) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
  		'topic_first_post_id',
  		'tx_mmforum_topics',
  		'deleted = 0 AND hidden = 0 AND uid = ' . intval($topicId) . ' AND pid = ' . intval($storagePID),
  		'', '', '1' );

		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return intval($row['topic_first_post_id']);
  }

	/**
	 * Returns the ID of the first topicpost which is shown on the page
	 * (or zero if none is found)
	 *
	 * @param	integer		$topicId: ID of the topic
	 * @param	array		$storagePID: ID of the page where the forendata is located
	 * @param	integer		$excludePostID: ID of the post that should be ignored
	 * @return	integer		ID of the first shown topicpost
	 * @author  Hauke Hain <hhpreuss@googlemail.com>
	 */
	public static function getFirstTopicPostToShowID($topicId, $storagePID, $excludePostID=0) {
	  $where = '';

    if (intval($excludePostID) > 0) {
      $where = ' AND uid <> ' . intval($excludePostID);
    }

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'tx_mmforum_posts',
			'deleted = 0 AND hidden = 0 AND topic_id = ' . intval($topicId) .
      ' AND pid = ' . intval($storagePID) .	$where
		);

		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return intval($row['uid']);
  }

	/**
	 * Clears the cache of a specific page
	 *
	 * @param	integer		$pid: ID of the page
	 * @return	void
	 */
  public static function clearPageCache($pid) {
    if (intval($pid) > 0) {
      $TCE = t3lib_div::makeInstance('t3lib_TCEmain');
      $TCE->admin = 1;
      $TCE->clear_cacheCmd($pid);
    }
  }

	/**
	 * Loads the tx_mmforum_config.ts (default values of the mm_forum install
	 * tool)
	 *
	 * @return	array		parsed TypoScript setup
	 */
  public static function getInstallToolSettings() {
		$filename = str_replace('ext/mm_forum_comments/', 'tx_mmforum_config.ts',
                            t3lib_extMgm::extPath('mm_forum_comments'));
		$ret = array();

		if(file_exists($filename)) {
      $conf = file_get_contents($filename);
      $parser  = new t3lib_TSparser();
  		$parser->parse($conf);
  		$ret = $parser->setup['plugin.']['tx_mmforum.'];
    }

		return $ret;
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/lib/class.tx_mmforumcomments_div.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/lib/class.tx_mmforumcomments_div.php']);
}

?>