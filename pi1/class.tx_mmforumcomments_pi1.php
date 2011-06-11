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
 *   54: class tx_mmforumcomments_pi1 extends tx_mmforum_pi1
 *   67:     function main($content, $conf)
 *  193:     private function newTopicCreationAllowed($key, $conf)
 *  209:     private function displayAnswerButton($topicID='')
 *  245:     private function displayTopicButton($imgpath)
 *  274:     function pi_loadLL()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

 require_once(t3lib_extMgm::extPath('mm_forum') . 'pi1/class.tx_mmforum_pi1.php');

 require_once(t3lib_extMgm::extPath('mm_forum_comments').'lib/class.tx_mmforumcomments_div.php');
 require_once(t3lib_extMgm::extPath('mm_forum_comments').'lib/class.tx_mmforumcomments_createcomments.php');


/**
 * Plugin 'mm_forum comments' for the 'mm_forum_comments' extension.
 *
 * @author	Hauke Hain <hhpreuss@googlemail.com>
 * @package	TYPO3
 * @subpackage	tx_mmforumcomments
 */
class tx_mmforumcomments_pi1 extends tx_mmforum_pi1 {
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
		$this->pi_setPiVarDefaults();
		$pid = tx_mmforumcomments_div::getPageID();
  	$setup = tx_mmforumcomments_div::loadTSSetupForPage($pid);
		$parameters = tx_mmforumcomments_div::getParameter($conf['parameters.']);

		if (!empty($conf['parameters.'][$parameters[2] . '.']['recordsTable'])) {
		  //do not use hooks, because of performance (I only need the page id)
		  $data = tx_mmforumcomments_div::getTypoScriptData($parameters[2], intval($parameters[1])==0 ? $pid : intval($parameters[1]), $conf, $this, false);
      $pid = $data['pid'];
    }

    $topicID = tx_mmforumcomments_div::getTopicID($pid, $parameters);

    /* Create new topic, if needed */
    if ($topicID == 0 && $this->newTopicCreationAllowed($parameters[2], $conf)) {
      tx_mmforumcomments_div::createTopicForRecord($parameters, $conf, $pid, $setup['plugin.']['tx_mmforum.']['storagePID'], $this, true, $data);
      $topicID = tx_mmforumcomments_div::getTopicID($pid, $parameters);
    }

    /* Return nothing if no topic is found. */
    if ($topicID === 0) {
      return $content;
    }

		/* Initialize mm_forum base object */
		$this->init($setup['plugin.']['tx_mmforum_pi1.']);
	  $this->pi_loadLL();
    $this->prefixId = 'tx_mmforum_pi1'; //prefix ID for button creation
  	$this->fid = $this->conf['pid_forum']; //forum page id for button creation
	  if (intval($conf['postPerPage']) > 0) $this->conf['post_limit'] = $conf['postPerPage'];

		/* Show comments */
    if ($conf['hideFirstPost']) {
		  $this->firstPostID = tx_mmforumcomments_div::getFirstTopicPostID($topicID, $this->conf['storagePID']);
    }

    if ($hasComments = (tx_mmforumcomments_div::getFirstTopicPostToShowID($topicID, $this->conf['storagePID'], $this->firstPostID) > 0)) {
		  $this->piVars['tid'] = $topicID; //topic id for list_post
  		//use template with posttable only (no topic title, pagebrowser etc.):
      $this->conf['LIST_POSTS_BEGIN'] = '###LIST_POSTS_BEGIN_MINIMAL###';
      $this->conf['LIST_POSTS_END'] = '###LIST_POSTS_END_MINIMAL###';
      $content = tx_mmforum_postfunctions::list_post($content, $this->conf,
                                                     $conf['postOrderingMode']);
      $content = $this->cObj->stdWrap($content, $conf['template.']['commentsWrap.']);
    } else {
      $content .= $this->cObj->stdWrap($this->pi_getLL('nocomments') .
                  (intval($GLOBALS['TSFE']->fe_user->user['uid']) == 0 ? '<br />' . $this->pi_getLL('noLogin') : ''),
                  $conf['template.']['noCommentsWrap.']);
    }

    $template = $this->cObj->fileResource($conf['template.']['file']);

		if ($hasComments) {
      $topic_replies = intval($this->local_cObj->data['topic_replies']);

      if (!$conf['hideFirstPost']) {
        $topic_replies++;
      }

      //Show comment numbers only if reasonable
  		$markers['###LINKTOTOPIC###'] = $this->displayTopicButton($conf['template.']['commentButtonPath_img'], $conf['template.']['commentButtonNormalStdWrap.']['wrap']);

      if ($topic_replies > 1) {
        $markers['###TOPIC_REPLIES###'] = $topic_replies;
        $markers['###LABEL_SHOWN_COMMENTS###'] = $this->pi_getLL('showncomments');
        $markers['###LABEL_COMMENTS###'] = $this->pi_getLL('comments');

        if ($topic_replies > intval($this->conf['post_limit'])) {
          $markers['###LABEL_COMMENTS###'] = $this->pi_getLL('commentsSingle');
        }
  		} else {
        $markers['###TOPIC_REPLIES###'] = '';
        $markers['###LABEL_SHOWN_COMMENTS###'] = '';
        $markers['###LABEL_COMMENTS###'] = '';
      }

      if ($topic_replies > intval($this->conf['post_limit'])) {
        $markers['###LABEL_TOPIC_REPLIES###'] = $this->pi_getLL('topicreplies');
        $markers['###SHOWN_COMMENTS###'] = $this->conf['post_limit'];
  		} else {
        $markers['###LABEL_TOPIC_REPLIES###'] = '';
        $markers['###SHOWN_COMMENTS###'] = '';
      }
    } else {
      $markers = array(
  					'###LINKTOTOPIC###'          => '',
  					'###LABEL_TOPIC_REPLIES###'  => '',
  					'###TOPIC_REPLIES###'        => '',
  					'###LABEL_SHOWN_COMMENTS###' => '',
  					'###SHOWN_COMMENTS###'       => '',
  					'###LABEL_COMMENTS###'       => '',
  				);
    }

    $markers['###HEADLINE###'] = $this->cObj->stdWrap($this->pi_getLL('title'), $conf['template.']['headlineWrap.']);
  	$markers['###COMMENTS###'] = $content;
  	$markers['###ANSWERBUTTON###'] = $this->displayAnswerButton($topicID);
  	$markers['###NOCOMMENTSSTYLE###'] = $conf['template.']['noCommentsStyle'];

    $content = $this->cObj->substituteMarkerArray($template, $markers);

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Checks if fe plugin is allowed to create a new topic
	 *
	 * @param	string		$key: parameter key of the post vars
	 * @param	array  		$conf: plugin TypoScript setup
	 * @return	boolean	returns fals if topic creation is forbidden by TypoScript def. 
	 */
	private function newTopicCreationAllowed($key, $conf) {
	 $bool = isset($conf['parameters.'][$key . '.']['createNewTopics']) ? $conf['parameters.'][$key . '.']['createNewTopics'] : $conf['createNewTopics'];

   if($bool === true || $bool === false || $bool === null) {
    return true; //return true if $bool isn't set in TypoScript
   } else {
    return (bool)$bool;
   }
	}

/**
 * Creates an answer link
 *
 * @param	integer		  $topicID: UID of the mm_forum topic
 * @return	string		The HTML-Code
 */
	private function displayAnswerButton($topicID='') {
    $topicData = $this->local_cObj->data;

    if(empty($topicData)) {
      if (intval($topicID) === 0) return;
      $topicData = $this->getTopicData($topicID);
    }

    if ((!$topicData['read_flag'] && !$topicData['closed_flag']) || $this->getIsMod($topicData['forum_id']) || $this->getIsAdmin()) {
			if ($this->getMayWrite_topic($topicData['uid'])) {
				$linkParams[$this->prefixId] = array(
					'action' => 'new_post',
					'tid'    => $topicData['uid']
				);
				if ($this->useRealUrl()) {
					$linkParams[$this->prefixId]['fid'] = $topicData['forum_id'];
				}
				$btn = $this->createButton('reply', $linkParams);
			} else {
				$btn = '';
			}
		} else {
			$btn = $this->pi_getLL('topic.adminsOnly');
		}

		return $btn;
  }

/**
 * Creates a button that links to the last post of the topic
 * using mm_forum->createButton
 *
 * @param	string		$imgpath: path to the image (except of buttons/icons/)
 * @param	string		$buttonWrap: wrap for buttons
 * @return	string		The HTML-Code
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 */
	private function displayTopicButton($imgpath, $buttonWrap) {
		$linkParams['tx_mmforum_pi1'] = array (
			'action' => 'list_post',
				 'tid' => $this->local_cObj->data['uid'],
				 'pid' => 'last'
		);

		if ($this->useRealUrl()) {
			$linkParams[$this->prefixId]['fid'] = $this->local_cObj->data['forum_id'];
		}

	if (!empty($imgpath)) {
		//change image setup temporarily
		$tmpimgpath = $this->conf['path_img'];
		$tmpButtonWrap = $this->conf['buttons.']['normal.']['stdWrap.']['wrap'];

		// createButton uses file_exists -> correct the path
		$imgpath = str_replace('EXT:mm_forum_comments/', t3lib_extMgm::siteRelPath('mm_forum_comments'), $imgpath);

		$imgconf = $this->conf['buttons.']['normal.']['1.']['file.']['10.']['file.']['import']; 
		$this->conf['path_img'] = $imgpath;
		$this->conf['buttons.']['normal.']['1.']['file.']['10.']['file.']['import'] = $imgpath . 'buttons/icons/';
	}

	if (!empty($buttonWrap)) {
		$this->conf['buttons.']['normal.']['stdWrap.']['wrap'] = $buttonWrap;
	}

	$btn = $this->createButton('gotoForum', $linkParams, $pid);

	if (!empty($imgpath)) {
		$this->conf['path_img'] = $tmpimgpath;
		$this->conf['buttons.']['normal.']['1.']['file.']['10.']['file.']['import'] = $imgconf;
		$this->conf['buttons.']['normal.']['stdWrap.']['wrap'] = $tmpButtonWrap;
	}

	return $btn;
  }

/**
 * Overwrites pi_loadLL and merges the locallang.xml files
 * of mm_forum_comments and mm_forum for the choosen and alternative language
 *
 * @return	void
 * @author  Hauke Hain <hhpreuss@googlemail.com>
 */
  function pi_loadLL()	{
    parent::pi_loadLL();

 		if (!$this->LLalreadyLoaded) {
 		  $this->LLalreadyLoaded = true;
   		$LOCAL_LANG = t3lib_div::readLLfile(t3lib_extMgm::extPath('mm_forum').'pi1/locallang.xml', $this->LLkey);
   		$this->LOCAL_LANG = array_merge_recursive($LOCAL_LANG,is_array($this->LOCAL_LANG) ? $this->LOCAL_LANG : array());

   		if ($this->altLLkey) {
   			$LOCAL_LANG = t3lib_div::readLLfile($basePath,$this->altLLkey);
   			$this->LOCAL_LANG=array_merge_recursive($LOCAL_LANG,is_array($this->LOCAL_LANG) ? $this->LOCAL_LANG : array());
  		}
    }
  }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/pi1/class.tx_mmforumcomments_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/pi1/class.tx_mmforumcomments_pi1.php']);
}

?>