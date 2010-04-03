<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_mmforumcomments_links'] = array (
	'ctrl' => $TCA['tx_mmforumcomments_links']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'fid,parameter'
	),
	'feInterface' => $TCA['tx_mmforumcomments_links']['feInterface'],
	'columns' => array (
		'fid' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:mm_forum_comments/locallang_db.xml:tx_mmforumcomments_links.fid',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_mmforum_topics',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'parameter' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:mm_forum_comments/locallang_db.xml:tx_mmforumcomments_links.parameter',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
			)
		),
		'parameteruid' => array (
			'exclude' => 1,		
			'label' => 'LLL:EXT:mm_forum_comments/locallang_db.xml:tx_mmforumcomments_links.parameteruid',		
			'config' => array (
				'type' => 'input',	
				'size'     => '4',
        'max'      => '4',
        'eval'     => 'int',
        'checkbox' => '0',
			),
        'default' => 0
		),
	),
	'types' => array (
		'0' => array('showitem' => 'fid;;;;1-1-1, parameter, parameteruid')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>