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

						mmforumcomments_hideFirstPost = MMFORUM_CONF_ITEM
						mmforumcomments_hideFirstPost {
							type = checkbox
							label       = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.hideFirstPost.title
							description = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.hideFirstPost.desc
						}

						mmforumcomments_postOrderingMode = MMFORUM_CONF_ITEM
						mmforumcomments_postOrderingMode {
						type = select
							type.options.ASC  = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.postOrderingMode.asc
							type.options.DESC = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.postOrderingMode.desc
							label             = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.postOrderingMode.title
						}

						mmforumcomments_postperpage = MMFORUM_CONF_ITEM
						mmforumcomments_postperpage {
							type = int
							label       = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.postperpage.title
							description = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.postperpage.desc
						}

						mmforumcomments_clearCache = MMFORUM_CONF_ITEM
						mmforumcomments_clearCache {
							type = checkbox
							label       = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.clearCache.title
							description = LLL:EXT:mm_forum_comments/res/lang/locallang.xml:mod.clearCache.desc
						}
					}
				}
			}
		}
	}

}