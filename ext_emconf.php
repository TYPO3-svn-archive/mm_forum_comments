<?php

########################################################################
# Extension Manager/Repository config file for ext "mm_forum_comments".
#
# Auto generated 13-06-2011 18:22
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'mm_forum posts as comments',
	'description' => 'mm_forum posts/topics are used as page comments.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.2.1',
	'dependencies' => 'cms,mm_forum',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Hauke Hain',
	'author_email' => 'hhpreuss@googlemail.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'typo3' => '4.4.0-0.0.0',
			'mm_forum' => '1.9.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:27:{s:9:"ChangeLog";s:4:"ddd9";s:10:"README.txt";s:4:"46ba";s:12:"ext_icon.gif";s:4:"167d";s:17:"ext_localconf.php";s:4:"79b2";s:14:"ext_tables.php";s:4:"5ab6";s:14:"ext_tables.sql";s:4:"23ad";s:33:"icon_tx_mmforumcomments_links.gif";s:4:"475a";s:13:"locallang.xml";s:4:"8f99";s:16:"locallang_db.xml";s:4:"2bb2";s:7:"tca.php";s:4:"74f0";s:14:"doc/manual.sxw";s:4:"1acb";s:47:"lib/class.tx_mmforumcomments_createcomments.php";s:4:"7662";s:36:"lib/class.tx_mmforumcomments_div.php";s:4:"6449";s:38:"lib/class.tx_mmforumcomments_hooks.php";s:4:"1bcf";s:43:"lib/class.tx_mmforumcomments_modinstall.php";s:4:"10b2";s:43:"lib/class.tx_mmforumcomments_typoscript.php";s:4:"98cc";s:36:"pi1/class.tx_mmforumcomments_pi1.php";s:4:"c7fb";s:44:"pi1/class.tx_mmforumcomments_pi1_wizicon.php";s:4:"f503";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"b04b";s:35:"res/img/buttons/icons/gotoForum.png";s:4:"6eef";s:28:"res/img/mod/mmforum-conf.png";s:4:"de58";s:22:"res/lang/locallang.xml";s:4:"a797";s:22:"res/tmpl/comments.html";s:4:"1481";s:41:"res/ts/tx_mmforumcomments_pagetsconfig.ts";s:4:"4e5b";s:23:"res/ts/static/setup.txt";s:4:"18b2";s:28:"res/ts/static/news/setup.txt";s:4:"e214";}',
	'suggests' => array(
	),
);

?>