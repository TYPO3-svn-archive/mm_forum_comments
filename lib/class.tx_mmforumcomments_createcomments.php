<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2011 Hauke Hain <hhpreuss@googlemail.com>
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
 *   37: class tx_mmforumcomments_createcomments
 *   57:     public static function createTopic($pid, $para, $fid, $aid, $subject, $text, $date, $relationTable, $forumStoragePID)
 *   81:     private static function getPostFactory($forumStoragePID)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
 class tx_mmforumcomments_createcomments {

/**
 * Creates a new commenting topic.
 * This method uses the mm_forum postfactory to create the new topic.
 *
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 * @param   int     $pid:     The UID of the page the comments are shown
 * @param   array   $para:    0: The name of the parameter; 1: the unique
 *                               id (value) of the parameter
 * @param   int     $parauid: The UID (value) of the URL parameter
 * @param   int     $fid:     The UID of the forum the new topic is to be created in
 * @param   int     $aid:     The UID of the fe_user creating this topic
 * @param   string  $subject: The topic's subject
 * @param   string  $text:    The topic's first post's text
 * @param   int     $date:    The date of topic creation as unix timestamp
 * @param	  string	$relationTable: Table name
 * @param   int     $forumStoragePID: The page ID where the forum data is stored
 * @return	int/boolean       The topic's UID or FALSE (on error)
 */
	public static function createTopic($pid, $para, $fid, $aid, $subject, $text, $date, $relationTable, $forumStoragePID) {
  	$tid = tx_mmforumcomments_createcomments::getPostFactory($forumStoragePID)->create_topic(
            $fid, $aid, $subject, $text, $date,
				    dechex(ip2long(t3lib_div::getIndpEnv('REMOTE_ADDR'))), array(),
				    0, false, false, false
			     );

    // Insert relation
		$insertArray = array('pid' => $pid,
                         'fid' => $tid,
		                     'parameter' => $para[0],
		                     'parameteruid' => intval($para[1])
                        );

    $GLOBALS['TYPO3_DB']->exec_INSERTquery($relationTable, $insertArray, true);

    return $tid;
	}

/**
 * Creates a new comment within topic.
 * This method uses the mm_forum postfactory to create the new topic.
 *
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 * @param   int     $tid:     The UID of the topic the new post
 * @param   int     $aid:     The UID of the fe_user creating this topic
 * @param   string  $text:    The post text
 * @param   int     $date:    The date of topic creation as unix timestamp
 * @param   int     $forumStoragePID: The page ID where the forum data is stored 
 * @param	  string	$aName:   The name of the comment author
 * @return	int/boolean       The post's UID or FALSE (on error)
 */
	public static function createPost($tid, $aid, $text, $date, $forumStoragePID, $aName) {
	  require_once(t3lib_extMgm::extPath('mm_forum') . 'pi4/class.tx_mmforum_indexing.php');
  	$postId = tx_mmforumcomments_createcomments::getPostFactory($forumStoragePID)->create_post(
            $tid, $aid, $text, $date,
				    dechex(ip2long(t3lib_div::getIndpEnv('REMOTE_ADDR')))
			     );

    if($postId!=false) {
      // Save original t3blog comments author name in mm_forum post 
  		$updateArray = array('tx_mmforumcomments_authorname ' => $aName);
      $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_mmforum_posts',
                                             'uid = ' . $postId, $updateArray);
    }

    return $postId;
	}

/**
 * Instantiates and returns the mm_forum post factory.
 *
 * @param   int     $forumStoragePID: The page ID where the forum data is stored
 * @return	tx_mmforum_postfactory		An instance of the tx_mmforum_postfactory
 *                                    class. 
 */
	private static function getPostFactory($forumStoragePID) {
		$postFactory = t3lib_div::getUserObj('EXT:mm_forum/pi1/class.tx_mmforum_postfactory.php:tx_mmforum_postfactory');
		$postFactory->init(array('storagePID' => $forumStoragePID));

    return $postFactory;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/lib/class.tx_mmforumcomments_createcomments.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/lib/class.tx_mmforumcomments_createcomments.php']);
}

?>