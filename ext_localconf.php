<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}	/*
	 * Add some additional page TSConfig. This is used to dynamically extend the
	 * mm_forum backend module.
	 */
t3lib_extMgm::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mm_forum_comments/res/ts/tx_mmforumcomments_pagetsconfig.ts">');

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_mmforumcomments_pi1.php', '_pi1', 'list_type', 1);

	/*
	 * Use mm_forum hooks.
	 */
/*Cache clearing*//*
$TYPO3_CONF_VARS['EXTCONF']['mm_forum']['postfactory']['insertPost'][] = 'EXT:mm_forum_comments/lib/class.tx_mmforumcomments_hooks.php:&tx_mmforumcomments_hooks';
*/

?>