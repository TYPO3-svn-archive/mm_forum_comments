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
 * Hint: use extdeveval to insert/update function index above.*
 *
 *   45: class tx_mmforumcomments_div
 *   56:     public static function loadTSSetupForPage($pid)
 *   77:     private static function getSingle($data, $tsObjectKey, $tsObjectConf)
 *   93:     public static function getTSparsedString($tskey, $key, $conf, $data)
 *  107:     public function getPageID()
 *  123:     public static function getCommentCategoryUID($key, $conf)
 *  134:     public static function getTopicAuthorUID($key, $conf)
 *  146:     public static function getDate($key, $conf, &$data)
 *  160:     public static function getTypoScriptData($key, $uid, $conf)
 *  194:     public static function getParameter($paraconf)
 *  220:     public static function getTopicID($pid, $parameters, $relationTable)
 *
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
 class tx_mmforumcomments_div {

/**
 *
 * Loads the TypoScript setup for a specific page.
 * This function loads the complete TypoScript setup for a specific
 * page.
 * The t3lib_tsparser_ext class is used for doing this.
 *
 * @param  int    $pid The page UID for which the setup is to be loaded.
 * @return array  TypoScript setup
 *
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
	 * @param array $data Data which will be passed to the typoscript.
	 * @param string $tsObjectKey The typoscript which will be called.
	 * @param array $tsObjectConf TS object configuration
	 * @return string
	 */
	private static function getSingle($data, $tsObjectKey, $tsObjectConf) {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->data = $data;

		return $cObj->cObjGetSingle($tsObjectConf[$tsObjectKey], $tsObjectConf[$tsObjectKey . '.']);
	}

/**
  * Returns string created by TypoScript parser (GetSingle)
  *
  * @author  Hauke Hain <hhpreuss@googlemail.com>
  * @param   string      $tskey: The TypoScript which will be called
  * @param   string      $key: TypoScript key where to look in the parameter list
  * @param   array       $conf: The TypoScript setup
  * @param   array       $data: The data that is available via TypoScript
  * @return  string      The subject of the comment thread
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
  * @author  Hauke Hain <hhpreuss@googlemail.com>
  * @param   string      $str: The string that gets prepared
  * @return  string      The prepared string
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
    		$replacement = '[URL=' . $url . ']' . $linkname . '[/URL]';
    		$str = str_replace($output[0][$i], $replacement, $str);
      }
    }

    return $str;
  }

/**
  * Returns an absolute URL instead of an relative
  * Uses baseURL to do so or php SERVER_NAME is baseURL isn't available.
  *
  * @author  Hauke Hain <hhpreuss@googlemail.com>
  * @param   string      $url: The URL that should become an absolute URL
  * @return  string      The absolute URL
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
    * @author  Hauke Hain <hhpreuss@googlemail.com>
    * @return   integer   page uid
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
  * @author  Hauke Hain <hhpreuss@googlemail.com>
  * @param   string      $key: TypoScript key where to look in the parameter list
  * @param   array       $conf: The TS configuration
  * @return  string      forum UID
  */
  public static function getCommentCategoryUID($key, $conf) {
    return empty($conf['parameters.'][$key . '.']['pageCommentCategory']) ? $conf['pageCommentCategory'] : $conf['parameters.'][$key . '.']['pageCommentCategory'];
  }

/**
  * Returns the UID of the comment topic author
  *
  * @author  Hauke Hain <hhpreuss@googlemail.com>
  * @param   string      $key: TypoScript key where to look in the parameter list
  * @param   array       $conf: The TS configuration
  * @return  string      fe_user UID
  */
  public static function getTopicAuthorUID($key, $conf) {
    return empty($conf['parameters.'][$key . '.']['pageTopicAuthor']) ? $conf['pageTopicAuthor'] : $conf['parameters.'][$key . '.']['pageTopicAuthor'];
  }

/**
  * Returns the posting date for the comment thread 
  *
  * @author  Hauke Hain <hhpreuss@googlemail.com>
  * @param   string      $key: TypoScript key where to look in the parameter list
  * @param   array       $conf: The TS configuration
  * @param   array       $data: The data that is available via TypoScript
  * @return  integer     Unix timestamp
  */
  public static function getDate($key, $conf, &$data) {
    $datecolumn = empty($conf['parameters.'][$key . '.']['postdate']) ? $conf['postdate'] : $conf['parameters.'][$key . '.']['postdate'];
  
    return intval($data[$datecolumn])==0 ? time() : intval($data[$datecolumn]);
  }

/**
  * Returns the columns of a specific datarow to be available in TypoScript
  *
  * @author  Hauke Hain <hhpreuss@googlemail.com>
  * @param   string      $key: TypoScript key where to look in the parameter list
  * @param   integer     $uid: The uid of the datatable
  * @param   array       $conf: The TS configuration
  * @return  array       data for TypoScript
  */
  public static function getTypoScriptData($key, $uid, $conf) {
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

		 return $data;
		}

    return array();
  }

	/**
	 * Returns search parameter (WHERE clause)
	 *
   * @author  Hauke Hain <hhpreuss@googlemail.com>
	 * @param	  array		$paraconf: The parameter segment of the TS plugin setup
	 * @return	array   returns nothing if no parameters are configured in
	 *                  TypoScipt or the parameter name and unique parameter
	 *                  value in the linktable of the extension   	 
	 */
	public static function getParameter($paraconf) {
    if (is_array($paraconf) === false) {
      return array();
    }

    foreach($paraconf as $key => $value) {
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
   * @author  Hauke Hain <hhpreuss@googlemail.com>
	 * @param	integer		$pid: ID of the page where the comments are located
	 * @param	array 		$parameters: 0: The name of the parameter; 1: the unique
	 *                               id (value) of the parameter
	 * @param	string		$relationTable: Table name
	 * @return	integer	ID of the exsting topic
	 */
	public static function getTopicID($pid, $parameters, $relationTable) {
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
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/lib/class.tx_mmforumcomments_div.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/lib/class.tx_mmforumcomments_div.php']);
}

?>