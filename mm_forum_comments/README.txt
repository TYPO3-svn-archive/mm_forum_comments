TypoScript hints:
plugin.tx_mmforumcomments_pi1 {
  subject = COA
  
"subject" and "posttext" must be a COA.
You can use any database column to represent your topic title or the text of 
the first topicpost of the table you declared with "recordsTable".
If you do not have declared "recordsTable", the "pages" table will be used.

postdate = crdate
must be a unix timestamp. You can enter a valid database column of your choosen
table ("recordsTable"). I you leave it blank it will use the current time.

You can change the behaviour of mm_forum_comments with parameters.
mm_forum_comments is able to handle different parameters if you specified them
in the mm_forum_comments TypoScript.

for example the minimal configuration for tt_news:
plugin.tx_mmforumcomments_pi1 {
    tx_ttnews { #1
      uid = tt_news #2
      recordsTable = tt_news #3
    }
  }
}

index.php?id=9&tx_ttnews[tt_news]=2
                #1        #2      #4

#3: The name of the table in the database where #4 is the uid of a datarow.

For each parameter you can redeclare the subject, posttext, pageCommentCategory
and pageTopicAuthor.

Warning: The first set parameter mm_forum_comments find will be used. Others
will be ignored!