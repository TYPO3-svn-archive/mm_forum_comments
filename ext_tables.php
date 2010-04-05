<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addStaticFile($_EXTKEY, 'res/ts/static', 'mm_forum_comments');
t3lib_extMgm::addStaticFile($_EXTKEY, 'res/ts/static/news', 'Settings for tt_news');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:mm_forum_comments/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_mmforumcomments_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_mmforumcomments_pi1_wizicon.php';
}


t3lib_extMgm::allowTableOnStandardPages('tx_mmforumcomments_links');

$TCA['tx_mmforumcomments_links'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:mm_forum_comments/locallang_db.xml:tx_mmforumcomments_links',		
		'label'     => 'pid',
		'default_sortby' => 'ORDER BY crdate',	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_mmforumcomments_links.gif',
	),
);
?>