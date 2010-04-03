mod.web_txmmforumM1 {

	defaultConfigFiles.mm_forum_comments = EXT:mm_forum_comments/ext_typoscript_constants.txt

	essentialConfiguration {
		mmforumcomments_pageCommentCategory = 1
		mmforumcomments_pageTopicAuthor     = 1
	}

	submodules {
		installation {
			categories {
				comments = MMFORUM_CONF_CATEGORY
				comments {
					icon  = EXT:mm_forum_comments/res/img/mod/mmforum-conf.png
					name  = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.category_title_short
					title = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.category_title

					items {
						mmforumcomments_pageCommentCategory = MMFORUM_CONF_ITEM
						mmforumcomments_pageCommentCategory {
							type = special
							type.handler = EXT:mm_forum_comments/lib/class.tx_mmforumcomments_modinstall.php:tx_mmforumcomments_ModInstall->getForumSelector

							label       = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.pageCommentCategory.title
							description = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.pageCommentCategory.desc
						}

						mmforumcomments_pageTopicAuthor = MMFORUM_CONF_ITEM
						mmforumcomments_pageTopicAuthor {
							type = group
							type.table = fe_users

							label       = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.pageTopicAuthor.title
							description = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.pageTopicAuthor.desc
						}
					}
				}
			}
		}
	}

}