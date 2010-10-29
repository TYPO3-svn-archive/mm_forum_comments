
#
# Table structure for table 'tx_mmforumcomments_links'
#
CREATE TABLE tx_mmforumcomments_links (
	pid int(11) DEFAULT '0' NOT NULL,
	fid int(11) DEFAULT '0' NOT NULL,
	parameter tinytext,
	parameteruid int(11) DEFAULT '0' NOT NULL
	
	KEY parent (pid),
	KEY tx_mmforumnews_topic (fid),
	KEY searchTopicTerm1 (pid,parameter(32),parameteruid),
	KEY searchTopicTerm2 (pid,parameteruid)
);