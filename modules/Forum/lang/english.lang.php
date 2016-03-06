<?php

define("_INDEXFORUM","Forum Index");
define("_FTODAY","today,");
define("_FYESTERDAY","yesterday,");
define("_PAGES","Pages");
define("_TOPICMODIFIED","Topic was successfully modified.");
define("_SMILEY","Smilies");
define("_NOTITLE","Please enter a title!");
define("_NOTEXT","Please enter a text!");
define("_MOVETOPIC","Move topic to");
define("_CANCEL","Cancel");
define("_FSEARCHRESULT","Search Results");
define("_SEARCHING","Search");
define("_FSEARCHFOUND","matches for");
define("_FNOSEARCHFOUND","No matches were found for");
define("_FNOLASTVISITMESS","No new posts since your last visit");
define("_FNOSEARCHRESULT","No matches were found for your search criteria");
define("_KEYWORDS","Keywords");
define("_MATCHOR","Search for any terms of these terms");
define("_MATCHAND","Search for all terms");
define("_MATCHEXACT","Search expression");
define("_BOTH","Both");
define("_SEARCHINTO","Search into");
define("_NBANSWERS","Number of results");
define("_NOWORDSTOSEARCH","You must enter a word or an expression to search");
define("_3CHARSMIN","You must enter at least 3 characters");
define("_VISITFORUMS","Visit Forums");
define("_LISTSMILIES","Smilies List");
define("_UPLOADFAILED","Uploading of file failed!!!");
define("_NOTEXTRESUME","No resume available...");
define("_MODOS","Moderators");
define("_ADDMODO","Add a moderator");
define("_DELOLDMESSAGES","Delete old forum threads since the:");
define("_PERMALINK","Permalink");

// Main
define("_SEEMODO","See profile of ");

// Viewforum
define('_BAD_FORUM_ID', 'This ID is not valid.');
define('_TOPIC', 'Topic');
define('_CREATED_BY', 'Created by');

// Admin
define("_NONAME","Please enter a name!");
define("_INCORRECT_ORDER","The order does not consist of all digits !");
define("_INCORRECT_RANK_MESSAGE","The rank of the message threshold does not consist of all digits !");



// core.php
//define("_MODO","Moderator");
//define("_MODERATEUR","Moderator");

//define("_FORUMS","Forums");
//define("_FORUM","Forum");

return array(
    // modules/Forum/poll.php
    // modules/Forum/post.php
    // modules/Forum/viewtopic.php
    'TOPIC_NO_EXIST'    => 'Sorry, this topic does not exist or was removed',
    // modules/Forum/poll.php
    // modules/Forum/post.php
    // modules/Forum/viewforum.php
    // modules/Forum/viewtopic.php
    'FORUM_NO_EXIST'    => 'Sorry, this forum does not exist or was removed',
    // modules/Forum/main.php
    // modules/Forum/post.php
    // modules/Forum/viewforum.php
    // modules/Forum/viewtopic.php
    'NO_ACCESS_FORUM_CATEGORY' => 'Sorry, you have no permission to access this forum category',
    // modules/Forum/post.php
    // modules/Forum/viewforum.php
    // modules/Forum/viewtopic.php
    'NO_ACCESS_FORUM' => 'Sorry, you have no permission to access this forum',
    // modules/Forum/post.php
    // views/frontend/modules/Forum/post.php
    'MESSAGE'           => 'Post',
    // modules/Forum/index.php
    'CONFIRM_DELETE_FILE' => 'Remove this file?"',
    'FILE_DELETED'      => 'Attached file was successfully removed.',
    'NOTIFY_IS_ON'      => 'Notification was successfully activated.',
    'NOTIFY_IS_OFF'     => 'Notification was successfully deactivated.',
    'CONFIRM_DELETE_TOPIC' => 'Remove this topic?',
    'TOPIC_DELETED'     => 'Topic was successfully removed.',
    'TOPIC_MOVED'       => 'Topic was successfully moved.',
    'TOPIC_LOCKED'      => 'Topic was successfully closed.',
    'TOPIC_UNLOCKED'    => 'Topic was successfully re-opened.',
    'TOPIC_MODIFIED'    => 'Topic was successfully modified',
    'MESSAGES_MARK'     => 'All posts have now been marked as read',
    // modules/Forum/main.php
    'FORUM_CATEGORY_NO_EXIST' => 'Sorry, this forum category does not exist or was removed',
    'YEARS_OLD'         => 'years old',
    'NO_BIRTHDAY'       => 'no members have a birthday.',
    'ONE_BIRTHDAY'      => 'there is one member having a birthday:',
    'MANY_BIRTHDAY'     => 'there is %d members having a birthday:',
    // modules/Forum/poll.php
    'CONFIRM_DELETE_POLL' => 'Remove this poll?',
    'OPTION'            => 'Option',
    '2_OPTION_MIN'      => 'You must enter at least 2 options!',
    'FORUM_POLL_ADDED'  => 'Poll was successfully added.',
    'FORUM_POLL_MODIFIED' => 'Poll was successfully modified.',
    'FORUM_POLL_DELETED' => 'Poll was successfully removed.',
    'FORUM_POLL_NO_EXIST' => 'Sorry, this forum poll does not exist or was removed',
    'VOTE_SUCCES'       => 'Vote was successfully added.',
    'ALREADY_VOTE'      => 'Sorry, you have already voted!!!',
    'BAD_VOTE_LEVEL'    => 'Sorry, you don\'t have the permission to vote!',
    'ONLY_MEMBERS_VOTE' => 'Sorry, only website members are allowed to vote!',
    'NO_OPTION'         => 'You haven\'t selected any option',
    // modules/Forum/post.php
    'POST_EDIT'         => 'Edit post',
    'POST_NEW_TOPIC'    => 'Post new topic',
    'POST_REPLY'        => 'Post reply',
    'NO_FLOOD'          => 'You have already submitted a post very recently, please wait a bit...',
    'FIELD_EMPTY'       => 'You forgot to fill in a field.',
    'MESSAGE_SEND'      => 'Thank you, your post has been submitted.',
    'EDIT_BY'           => 'Edited by',
    'MESSAGE_MODIFIED'  => 'Post was successfully updated.',
    'EMAIL_REPLY_NOTIFY' => 'There has been a reply to this topic:',
    
    
    'CONFIRM_DELETE_POST' => 'Remove this post?',
    'FORUM_POST_DELETED' => 'Post was successfully removed.',
    // modules/Forum/viewtopic.php
    'IS_ONLINE'         => 'Online !',
    'REGISTERED'        => 'Joined',
    'IP'                => 'Ip',
    'LAST_THREAD'       => 'Previous topic',
    'NEXT_THREAD'       => 'Next topic',
    
    // modules/Forum/config/forumPoll.php
    'QUESTION'          => 'Question',
    'ADD_THIS_POLL'     => 'Add this poll',
    'MODIF_THIS_POLL'   => 'Modify this poll',
    
    
    // modules/Forum/backend/config/prune.php
    'FORUM'             => array('Forum', 'Forums'),
    
    // modules/Forum/backend/category.php
    // modules/Forum/backend/index.php
    // modules/Forum/backend/prune.php
    // modules/Forum/backend/rank.php
    // modules/Forum/backend/setting.php
    'ADMIN_FORUM'       => 'Forum Administration',
    // modules/Forum/backend/index.php
    'ADD_FORUM'         => 'Add a Forum',
    'EDIT_THIS_FORUM'   => 'Edit this Forum',
    'DELETE_THIS_FORUM' => 'Remove this Forum',
    'NO_FORUM_IN_DB'    => 'No forum in the database',
    'FORUM_ADDED'       => 'Forum was successfully added.',
    'FORUM_MODIFIED'    => 'Forum was successfully modified.',
    'FORUM_DELETED'     => 'Forum was successfully removed.',
    'ACTION_ADD_FORUM'  => 'has added the forum',
    'ACTION_EDIT_FORUM' => 'has modified the forum',
    'ACTION_DELETE_FORUM' => 'has deleted the forum',
    'DELETE_THIS_MODERATOR' => 'Remove this moderator',
    'ACTION_DELETE_MODERATOR' => 'has deleted the moderator',
    'MODERATOR_DELETED' => 'Moderator was successfully removed.',
    // modules/Forum/backend/category.php
    'ACTION_ADD_FORUM_CATEGORY' => 'has added the forum category', // _ACTIONADDCATFO
    'ACTION_EDIT_FORUM_CATEGORY' => 'has modified the forum category', // _ACTIONMODIFCATFO
    'ACTION_DELETE_FORUM_CATEGORY' => 'has deleted the forum category', // _ACTIONDELCATFO
    // modules/Forum/backend/moderator.php
    'MODERATOR_MANAGEMENT'    => 'Moderator management',
    'ADD_MODERATOR'           => 'Add moderator',
    'EDIT_THIS_MODERATOR'     => 'Edit this moderator',
    'DELETE_THIS_MODERATOR'   => 'Remove this moderator',
    'NO_MODERATOR_IN_DB'      => 'No moderator in database',
    'ADD_THIS_MODERATOR'      => 'Create moderator',
    'MODIFY_THIS_MODERATOR'   => 'Modify this moderator',
    'MODERATOR_ADDED'         => 'Moderator was successfully added.',
    'MODERATOR_MODIFIED'      => 'Moderator was successfully modified.',
    'MODERATOR_DELETED'       => 'Moderator was successfully removed.',
    'ACTION_ADD_MODERATOR'    => 'have added the moderator',
    'ACTION_EDIT_MODERATOR'   => 'have modified the moderator',
    'ACTION_DELETE_MODERATOR' => 'have deleted the moderator',
    
    // modules/Forum/backend/rank.php
    'ACTION_ADD_FORUM_RANK'  => 'has added the forum rank',
    'ACTION_EDIT_FORUM_RANK' => 'has modified the forum rank',
    'ACTION_DELETE_FORUM_RANK' => 'has deleted the forum rank',
    // modules/Forum/backend/prune.php
    'NO_DAY'            => 'Please enter the number of days!',
    'PRUNE'             => 'Forum Pruning',
    'INCORRECT_PRUNE_DAY'=> 'The number of days does not consist of all digits !',
    'ACTION_PRUNE_FORUM' => 'has pruned the forum',
    'FORUM_PRUNE'       => 'Forum Pruning was successful.',
    // modules/Forum/backend/config/forum.php
    'LEVEL_ACCES'       => 'Access Level',
    'LEVEL_POST'        => 'Post Level',
    'LEVEL_POLL'        => 'Poll Level',
    'LEVEL_VOTE'        => 'Vote Level',
    'MODERATOR'         => 'Moderator',
    'ADD_THIS_FORUM'    => 'Add this Forum',
    'MODIFY_THIS_FORUM' => 'Modify this Forum',
    // modules/Forum/backend/config/forumCategory.php
    'NOTIFY_FORUM_IMAGE_SIZE' => 'In order to have a good display on your website, ensure to adjust image width with these of your template.<br />Images will be resized to the max width of your website.',
    // modules/Forum/backend/config/forumRank.php
    'MESSAGES'          => 'Posts',
    // modules/Forum/backend/config/moderator.php
    'NICKNAME'          => 'Nickname',
    // modules/Forum/backend/config/prune.php
    'NUMBER_OF_DAY'     => 'Numbers of days',
    // modules/Forum/backend/config/setting.php
    'FORUM_TITLE'       => 'Forum\'s Title',
    'FORUM_DESCRIPTION' => 'Forum\'s Description',
    'USE_RANK_TEAM'     => 'Use Team Rank',
    'DISPLAY_FORUM_IMAGE' => 'Diplay images for each forums',
    'DISPLAY_CATEGORY_IMAGE' => 'Replace categories titles by an image when it is possible',
    'DISPLAY_BIRTHDAY'  => 'Display members birthday on forum homepage',
    'DISPLAY_GAMER_DETAILS' => 'Display user\'s games and preferences ',
    'DISPLAY_USER_DETAILS' => 'Display team user\'s rank colors and legend',
    'DISPLAY_LABELS'    => 'Display CSS labels instead of images (attached files, poll and pined topic',
    'DISPLAY_MODERATORS' => 'Display list of moderators on the main page of forum',
    'NUMBER_THREAD'     => 'Number of threads per page',
    'NUMBER_POST'       => 'Number of posts per page',
    'TOPIC_HOT'         => 'Posts for Popular Threshold',
    'POST_FLOOD'        => 'Minimum time, in seconds, between 2 messages (against flood)',
    'MAX_SURVEY_FIELD'  => 'Max number of poll options',
    'JOINED_FILES'      => 'Enable file attachment',
    'FILE_LEVEL'        => 'Level required to attach a file',
    'MAX_SIZE_FILE'     => 'Maximum attached file size (in KB)',
    // views/frontend/modules/Forum/main.php
    // views/frontend/modules/Forum/searchForm.php
    // views/frontend/modules/Forum/searchResult.php
    'SEARCH'            => 'Search',
    // views/frontend/modules/Forum/block.php
    // views/frontend/modules/Forum/main.php
    // views/frontend/modules/Forum/viewForum.php
    'LAST_POST'         => 'Last Post',
    'VIEW_LATEST_POST'  => 'View latest post',
    // views/frontend/modules/Forum/block.php
    // views/frontend/modules/Forum/searchForm.php
    // views/frontend/modules/Forum/searchResult.php
    // views/frontend/modules/Forum/viewForum.php
    'SUBJECTS'          => 'Topics',
    // views/frontend/modules/Forum/post.php
    // views/frontend/modules/Forum/viewForum.php
    'POLL'              => 'Poll',
    'ATTACH_FILE'       => 'Attach a file',
    // views/frontend/modules/Forum/post.php
    // views/frontend/modules/Forum/viewtopic.php
    'POSTED_ON'         => 'Posted',
    // views/frontend/modules/Forum/block.php
    // views/frontend/modules/Forum/viewForum.php
    'ANSWERS'           => 'replies',
    'VIEWS'             => 'views',
    // views/frontend/modules/Forum/post.php
    // views/frontend/modules/Forum/viewForum.php
    'ANNOUNCEMENT'      => 'Announcement',
    // views/frontend/modules/Forum/editPoll.php
    'POST_SURVEY'       => 'Post a poll',
    // views/frontend/modules/Forum/main.php
    'ADVANCED_SEARCH'   => 'Advanced Search',
    'TODAY_IS'          => 'Today is',
    'YOUR_LAST_VISIT'   => 'Your last visit',
    'TOPICS'            => 'topics',
    'NO_POST'           => 'No Posts',
    'TOTAL_MEMBERS_POSTS' => 'Our members have posted a total of %s post(s).',
    'WE_HAVE_N_REGISTERED_MEMBERS' => 'We have %d registered members.',
    'LAST_USER_IS'      => 'Last registered user is ',
    'FORUM_ONLINE_LEGEND' => 'There are %d visitor(s), %d member(s) and %d administrator(s) online.',
    'MEMBERS_ONLINE'    => 'Members online',
    'RANK_LEGEND'       => 'Ranks legend',
    'TODAY'             => 'Today',
    'MARK_READ'         => 'Mark all posts as read',
    'VIEW_LAST_VISIT_MESS' => 'View all new posts since last visit',
    'NEW_POST_LAST_VISIT' => 'New posts',
    'NO_POST_LAST_VISIT' => 'No new posts',
    // views/frontend/modules/Forum/post.php
    'NICKNAME'          => 'Nickname',
    // TODO : Use login & logout in main translation file
    'FLOGOUT'           => 'logout',
    'FLOGIN'            => 'login',
    'OPTIONS'           => 'Options',
    'USER_SIGNATURE'    => 'Use signature',
    'EMAIL_NOTIFY'      => 'Receive email notification',
    'DISPLAY_EDIT_TEXT' => 'Display editing text',
    'NUMBER_OPTIONS'    => 'Number of options',
    'MAXIMUM'           => 'Maximum',
    'MO'                => 'MB',
    'KO'                => 'KB',
    'MAXIMUM_FILE_SIZE' => 'Maximum file size',
    'PREVIOUS_MESSAGES' => 'Previous post(s)',
    // views/frontend/modules/Forum/viewForum.php
    'NEW'               => 'New',
    'MARK_SUBJECT_READ' => 'Mark all topics as read',
    'NO_POST_FORUM'     => 'There are no posts in this forum',
    'CREATED_BY'        => 'Created by',
    'POST_NEW'          => 'New posts',
    'NO_POST_NEW'       => 'No new posts',
    'POST_NEW_CLOSE'    => 'New posts [ Locked ]',
    'SUBJECT_CLOSE'     => 'Topic locked',
    'POST_NEW_HOT'      => 'New posts [ Popular ]',
    'NO_POST_NEW_HOT'   => 'No new posts [ Popular ]',
    'JUMP_TO'           => 'Jump to',
    'SELECT_FORUM'      => 'Select a forum',
    'SEE_THE_TOPIC'     => 'Display topics from previous',
    'THE_FIRST'         => 'All Topics',
    'ONE_DAY'           => '1 Day',
    'ONE_WEEK'          => '1 Week',
    'ONE_MONTH'         => '1 Month',
    'SIX_MONTH'         => '6 Months',
    'ONE_YEAR'          => '1 Year',
    // views/frontend/modules/Forum/viewtopic.php
    'NEW_TOPIC'         => 'New topic',
    'REPLY'             => 'Reply',
    'EDIT_POLL'         => 'Edit this poll',
    'DELETE_POLL'       => 'Delete this poll',
    'TOTAL_VOTE'        => 'Total votes',
    'BACK_TO_TOP'       => 'Back to top',
    'PERMALINK_TITLE'   => 'Permanent link to this message',
    'FAVORITE_GAME'     => 'Favorite game',
    'REPLY_QUOTE'       => 'Reply with quote',
    'EDIT_MESSAGE'      => 'Edit this post',
    'DELETE_MESSAGE'    => 'Remove this post',
    'DOWNLOAD_FILE'     => 'Download attached file',
    'DELETE_FILE'       => 'Remove the attached file',
    'SEE_PROFIL'        => 'View profile',
    'SEND_PM'           => 'Send private message',
    'NOTIFY_ON'         => 'Subscribe to this topic',
    'NOTIFY_OFF'        => 'Unsubscribe from this topic',
    'TOPIC_UNLOCK'      => 'Open this topic',
    'TOPIC_LOCK'        => 'Close this topic',
    'TOPIC_DOWN'        => 'Unsticky Thread',
    'TOPIC_UP'          => 'Sticky Thread',
    'TOPIC_DELETE'      => 'Remove this topic',
    'TOPIC_MOVE'        => 'Move this topic',
    
    
);

?>