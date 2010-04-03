<?php

########################################################################
# Extension Manager/Repository config file for ext "mm_forum_comments".
#
# Auto generated 03-04-2010 17:39
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'mm_forum posts as comments',
	'description' => 'mm_forum posts/topics are used as page comments.',
	'category' => 'plugin',
	'author' => 'Hauke Hain',
	'author_email' => 'hhpreuss@googlemail.com',
	'shy' => '',
	'dependencies' => 'cms,mm_forum',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'mm_forum' => '1.9.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:17:{s:9:"ChangeLog";s:4:"a439";s:10:"README.txt";s:4:"ee2d";s:8:"TODO.txt";s:4:"824d";s:12:"ext_icon.gif";s:4:"167d";s:17:"ext_localconf.php";s:4:"6816";s:14:"ext_tables.php";s:4:"1371";s:14:"ext_tables.sql";s:4:"d847";s:33:"icon_tx_mmforumcomments_links.gif";s:4:"475a";s:13:"locallang.xml";s:4:"8f99";s:16:"locallang_db.xml";s:4:"415e";s:7:"tca.php";s:4:"39d5";s:19:"doc/wizard_form.dat";s:4:"1471";s:20:"doc/wizard_form.html";s:4:"243e";s:36:"pi1/class.tx_mmforumcomments_pi1.php";s:4:"501b";s:44:"pi1/class.tx_mmforumcomments_pi1_wizicon.php";s:4:"f503";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"9bc4";}',
	'suggests' => array(
	),
);

?>