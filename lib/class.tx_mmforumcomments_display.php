<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2009 Hauke Hain <hhpreuss@googlemail.com>
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
 *   50: class tx_hppage2forum_display
 *   68:     public function displayForumTopic($topicId, $storagePID, $limit, $pagebrowser = false, $templateFile = 'EXT:mm_forum/res/tmpl/default/forum/list_post.html', $imgPath = 'EXT:mm_forum/res/img/default/', $locallangFile = 'EXT:mm_forum/pi1/locallang.xml', $showXforUserName = 'username', $showRealName = false, $dateFormat = 'd.m.Y', $timeFormat = ', H:i')
 *  238:     public function displayForumLink($topicId, $forumUID, $forumPID, $useRealUrl = false, $imgPath = 'EXT:mm_forum/res/img/default/', $locallangFile = 'EXT:mm_forum/pi1/locallang.xml')
 *  263:     public function displayAnswerLink($topicId, $forumPID, $useRealUrl = false, $imgPath = 'EXT:mm_forum/res/img/default/', $locallangFile = 'EXT:mm_forum/pi1/locallang.xml')
 *  308:     public function displayLoginInfo($locallangFile = 'EXT:mm_forum/pi1/locallang.xml')
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

	require_once(t3lib_extMgm::extPath('mm_forum') . 'pi1/class.tx_mmforum_pi1.php');
	require_once(t3lib_extMgm::extPath('mm_forum') . 'includes/cache/class.tx_mmforum_cache.php');

	/**
	 * Class which provides functions for displaying the forum posts.
	 *
	 * @author Hauke Hain <hhpreuss@googlemail.com>
	 * @package TYPO3
	 * @subpackage tx_hppage2forum
	 */
	class tx_hppage2forum_display {

/**
 * Lists all posts in a certain topic.
 *
 * @param	integer		$topicId: The topic ID
 * @param	integer		$storagePID: The page ID where the forum data is stored
 * @param	integer		$limit: The number of post which should be displayed
 * @param	boolean		$pagebrowser: Show a pagebroser, so you can see $limit posts per page?
 * @param	string		$templatePath: Path to the template
 * @param	string		$locallangFile: Path and file name of the locallang.xml
 * @param	string		$imgPath: Path to the image directory of mm_forum
 * @param	string		$showXforUserName: The colum name of 'fe_users' which should be displayed instead of 'username'
 * @param	boolean		$showRealName: Set to true of you want to show the real name ('name' colum of fe_users) of the user below the username
 * @param	string		$dateFormat: date() format for date
 * @param	string		$timeFormat: date() format for time (appended to $dateFormat)
 * @return	string		The forum posts
 */
		public function displayForumTopic($topicId, $storagePID, $forumPID, $limit, $pagebrowser = false, $templateFile = 'EXT:mm_forum/res/tmpl/default/forum/list_post.html', $imgPath = 'EXT:mm_forum/res/img/default/', $locallangFile = 'EXT:mm_forum/pi1/locallang.xml', $showXforUserName = 'username', $showRealName = false, $dateFormat = 'd.m.Y', $timeFormat = ', H:i') {
			if ($templateFile) {
				$templateFile = $this->cObj->fileResource($templateFile);
			} else {
				return 'No template found.';
			}

			if (($topicId = intval($topicId)) > 0) {
				$feUserId = intval($GLOBALS['TSFE']->fe_user->user['uid']);
			} else {
				return 'Missing topic ID.';
			}

			// Initialize mm_forum
			$this->forum = t3lib_div::makeInstance('tx_mmforum_pi1');
			// Initialize cache object from mm_forum
			$this->forum->cache = & tx_mmforum_cache::getGlobalCacheObject();

			// Check authorization
			if (!$this->forum->getMayRead_topic($topicId)) {
				return $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':topic-noAccess');
			}

			$topicData = $this->forum->getTopicData($topicId);

			// Determine firt page uid
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'topic_first_post_id',
				'tx_mmforum_topics',
				'deleted = 0 AND hidden = 0 AND uid = ' . $topicId . ' AND pid = ' . intval($storagePID),
				'', '', '1' );

			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			$firstPostId = $row['topic_first_post_id'];

			$postList = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
				'tx_mmforum_posts',
				'deleted = 0 AND hidden = 0 AND uid <> ' . $firstPostId . ' AND topic_id = ' . $topicId . ' AND pid = ' . intval($storagePID),
				'',
				'post_time DESC',
				$limit );

			if (($GLOBALS['TYPO3_DB']->sql_num_rows($postList) == 0)) {
				return ''; //Return nothing, because ther is nothing...
			}

			$templateItem = trim($this->cObj->getSubpart($templateFile, '###LIST_POSTS###'));

			$i = 1;
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($postList)) {
				// USER Info begin
				$threadauthor = ($topicData['topic_replies'] > 0 ? $topicData['topic_poster'] : false);
				$userData = (!is_array($row['poster_id']) ? tx_mmforum_tools::get_userdata($row['poster_id']) : $row['poster_id']);
				$userContent = '';
				$templateItemUser = $this->cObj->getSubpart($templateFile, '###USERINFO###');

				if ($templateItemUser) {
					$markerUser = array(
					'###LLL_DELETED###' => $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':user-deleted'),
						'###USERNAME###' => '<p><strong>' . $userData[$showXforUserName] . '</strong></p>',
						'###USERREALNAME###' => $showRealName ? $userData['name'] :	'',
						'###USERRANKS###' => '',
						'###TOPICCREATOR###' => '',
						'###AVATAR###' => '', #$this->forum->getUserAvatar($userData),
					  '###LLL_REGSINCE###' => $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':user-regSince'),
						'###LLL_POSTCOUNT###' => $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':user-posts'),
						'###REGSINCE###' => date($dateFormat, $userData['crdate']),
						'###POSTCOUNT###' => intval($userData['tx_mmforum_posts']),
						'####USERINFO_REGULAR###' => '',
						'###USER_RATING###' => '' );

					if ($userData === false) {
						$templateItemUser = $this->cObj->substituteSubpart($templateItemUser, '###USERINFO_REGULAR###', '');
					} else {
						$templateItemUser = $this->cObj->substituteSubpart($templateItemUser, '###USERINFO_DELETED###', '');
					}

					$userContent .= $this->cObj->substituteMarkerArray($templateItemUser, $markerUser);

				} else {
					if ($userData === false) {
						return '<p><strong>' . $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':user-deleted') . '</strong></p>';
					}
					$userContent = '<strong>' . $userData[$showXforUserName] . '</strong><br />';
					$userContent .= $showRealName ? $userData['name'] :
					 '';
					$userContent .= $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':user-regSince') . ': ' . date($dateFormat, $userData['crdate']) . '<br />' . $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':user-posts') . ': ' . $userData['tx_mmforum_posts'];
				}
				// USER Info end

				// create profile link
				if (!($userData === false)) {
  				$linkParams[$this->forum->prefixId] = array(
  				'action' => 'forum_view_profil',
  					'user_id' => $userData['uid'] );
  				$userlink = $this->pi_getPageLink($forumPID, '', $linkParams);
  
  				//create profile button
  				//TODO: Use TS to create button like in mm_forum->need mm_forum TS array
  				$profilebutton = '<div class="buttons clearfix"><a class="profile.png" href="' . $userlink . '"><img width="16" height="16" alt="' . $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':button-profile') . '" src="' . $imgPath . 'buttons/icons/profile.png"/>' . $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':button-profile') . '</a></div>';
  			} else {
          $profilebutton = '';
        }

				// POSTTEXT begin
				$resPostText = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid, post_text, tstamp, cache_tstamp, cache_text',
					'tx_mmforum_posts_text',
					'deleted="0" AND post_id= ' . intval($row['uid'])
				);
				list($text_uid, $posttext, $tstamp, $cache_tstamp, $cache_text) = $GLOBALS['TYPO3_DB']->sql_fetch_row($resPostText);
				$GLOBALS['TYPO3_DB']->sql_free_result($resPostText);

				if ($tstamp > $cache_tstamp || $cache_tstamp == 0) {
					$posttext = $this->forum->bb2text($posttext, $this->conf);
					$updateArray = array(
					'cache_tstamp' => time(),
						'cache_text' => $posttext );
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_mmforum_posts_text', 'uid=' . $text_uid, $updateArray);
				} else {
					$posttext = $cache_text;
				}
				// POSTTEXT end

				$extra = array('even' => ($i++%2) == 0);
				list($userData) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'fe_users', 'uid = ' . $row['poster_id']);

				$marker = array(
				'###LABEL_AUTHOR###' => '',
					'###LABEL_MESSAGE###' => '',
					'###ATTACHMENTS###' => '',
					'###POSTOPTIONS###' => '',
					'###POSTMENU###' => '',
					'###MESSAGEMENU###' => '',
					'###POSTANCHOR###' => '',
					'###USERSIGNATURE###' => '',
					'###POSTRATING###' => '',
					'####ATTACHMENT_SECTION###' => '',
					'###PROFILEMENU###' => $profilebutton,
					'###POSTUSER###' => $userContent,
					'###POSTTEXT###' => $posttext,
					'###PROFILELINK###' =>  $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':hook.writtenBy') . ' <a href="' . $userlink . '">' . $userData[$showXforUserName] . '</a>',
					'###POSTDATE###' => $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':post-writtenOn').' '.date($dateFormat . $timeFormat, $row['post_time']),
					'###EVEN_ODD###' => $extra['even'] ? 'even' : 'odd',
					'###GUESTNAME###' => '' );

				// TODO: pagebrowser!
				// TODO: Remove my very own markers
				$marker['###LABEL_POSTOPTIONS###'] = '';
				$marker['###POSTED###'] = $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':post-writtenOn') . ' ' . date($dateFormat . $timeFormat, $row['post_time']);		
				if (($row['poster_id'] == 0) and isset($row['tx_hpmmforumfakeguestposts_postername'])) {
          $marker['###PROFILELINK###'] =  $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':hook.writtenBy') . ' ' . $row['tx_hpmmforumfakeguestposts_postername'];
          $marker['###GUESTNAME###'] = '<strong>' . $row['tx_hpmmforumfakeguestposts_postername'] . '</strong><br />';
        }

				$result .= $this->cObj->substituteMarkerArray($templateItem, $marker);

			}
			$GLOBALS['TYPO3_DB']->sql_free_result($postList);

			return $result;
		}

/**
 * Returns a link with an image to the last page of the forum topic
 *
 * @param	integer		$topicId: The topic ID
 * @param	integer		$forumUID: The UID of the forum where the topics should be created in
 * @param	integer		$forumPID: The page ID of the forum
 * @param	boolean		$useRealUrl: true/false (for creating human readable links)
 * @param	string		$imgPath: Path to the image directory of mm_forum
 * @param	string		$locallangFile: Path and file name of the locallang.xml
 * @return	string		The HTML-Code
 */
		public function displayForumLink($topicId, $forumUID, $forumPID, $useRealUrl = false, $imgPath = 'EXT:mm_forum/res/img/default/', $locallangFile = 'EXT:mm_forum/pi1/locallang.xml') {
			$linkparams['tx_mmforum_pi1'] = array (
			'action' => 'list_post',
				'tid' => $topicId,
				'pid' => 'last' );
			if ($useRealUrl) {
				$linkparams['tx_mmforum_pi1']['fid'] = $forumUID;
			}

			$imgTitle = $imgAlt = $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':topic-gotoLastPost');
			$imgTag = '<img src="' . $imgPath . 'jump_to.gif" style="border:none;" ' . 'alt="' . $imgAlt . '" title="' . $imgTitle . '" />';

			return $this->pi_linkToPage($imgTag . $imgTitle, $forumPID, '', $linkparams);
		}

/**
 * Creates an answer link
 *
 * @param	integer		$topicId: The topic ID
 * @param	integer		$forumPID: The page ID of the forum
 * @param	boolean		$useRealUrl: true/false (for creating human readable links)
 * @param	string		$imgPath: Path to the image directory of mm_forum
 * @param	string		$locallangFile: Path and file name of the locallang.xml
 * @return	string		The HTML-Code
 */
		public function displayAnswerLink($topicId, $forumPID, $useRealUrl = false, $imgPath = 'EXT:mm_forum/res/img/default/', $locallangFile = 'EXT:mm_forum/pi1/locallang.xml') {
			// load ts config mm_forum
			$tsPath = PATH_typo3conf.'tx_mmforum_config.ts';
			if (file_exists($tsPath)) $tsContent = file_get_contents($tsPath);
			$parser = t3lib_div::makeInstance('t3lib_TSparser');
			$parser->parse($tsContent);
			$config = $parser->setup;
			$user = $config['plugin.']['tx_mmforum.']['userGroup'];
			$admin = $config['plugin.']['tx_mmforum.']['adminGroup'];

			// Initialize mm_forum
			$this->forum = t3lib_div::makeInstance('tx_mmforum_pi1');
			// Initialize cache object from mm_forum
			#$this->forum->cache =& tx_mmforum_cache::getGlobalCacheObject();

			$topicData = $this->forum->getTopicData($topicId);
			$return = '';

			//TODO: Add mod check so they may get a link
			if ((!$topicData['read_flag'] && !$topicData['closed_flag']) and $GLOBALS['TSFE']->fe_user->user['usergroup']) {
				foreach (explode(',', $GLOBALS['TSFE']->fe_user->user['usergroup']) as $value) {
					if (($value == $user) or ($value == $admin)) {
						$linkparams[$this->forum->prefixId] = array(
						'action' => 'new_post',
							'tid' => intval($topicId)
						);
						if ($useRealUrl) {
							$linkparams[$this->forum->prefixId]['fid'] = $topicData['forum_id'];
						}
						$imgTitle = $imgAlt = $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':button-reply');
						$imgTag = '<img src="' . $imgPath . 'buttons/icons/reply.png" style="border:none;" ' . 'alt="' . $imgAlt . '" title="' . $imgTitle . '" />';
						return $this->pi_linkToPage($imgTag . $imgTitle, $forumPID, '', $linkparams);
					}
				}
			}

			return '';
		}

		/**
 * [Describe function...]
 *
 * @param	[type]		$locallangFile: ...
 * @return	[type]		...
 */
		public function displayLoginInfo($locallangFile = 'EXT:mm_forum/pi1/locallang.xml') {
			if (!$GLOBALS['TSFE']->fe_user->user['uid']) {
				return $GLOBALS['TSFE']->sL('LLL:' . $locallangFile . ':newPost-noLogin');
			}
			return '';
		}
	}

?>