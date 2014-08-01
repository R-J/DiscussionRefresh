<?php defined('APPLICATION') or die('This script should not be accessed directly');

$PluginInfo['DiscussionRefresh'] = array(
    'Name' => 'Discussion Refresh',
    'Description' => 'Loads new comments when either "Preview", "Edit", "Save Draft" or "Post Comment" button is pressed.',
    'Version' => '0.2',
    'RequiredApplications' => array('Vanilla' => '2.1'),
    'MobileFriendly' => true,
    'Author' => 'Robin Jurinka',
    'License' => 'MIT'
);

/**
 * Loads new comments when "Preview", "Edit", "Save Draft" or "Post Comment" button is pressed.
 *
 * @package DiscussionRefresh
 * @author Robin Jurinka
 * @license MIT
 */
class DiscussionRefreshPlugin extends Gdn_Plugin {
    /**
     * Appends JavaScript to a discussion.
     *
     * @param object $Sender DiscussionController.
     * @package DiscussionRefresh
     * @since 0.1
     */
    public function discussionController_render_before($Sender) {
        $Sender->AddJsFile('discussionrefresh.js', 'plugins/DiscussionRefresh');
    }

    /**
     * Return view for new comments and LastCommentID for further reference.
     *
     * @param object $Sender DiscussionController.
     * @param array $Args DiscussionID and LastCommentID.
     * @package DiscussionRefresh
     * @since 0.1
     */
    public function discussionController_discussionRefresh_create($Sender, $Args) {
        $DiscussionID = (int)$Args[0];
        $LastCommentID = (int)$Args[1];
        $Page = (int)$Args[2];

        if ($DiscussionID <= 0 || $LastCommentID < 0 || $Page < 1) {
            return;
        }

        // don't forget permission check!
        $DiscussionModel = new DiscussionModel();
        $Discussion = $DiscussionModel->GetID($DiscussionID);
        $Sender->Permission('Vanilla.Discussions.View', true, 'Category', $Discussion->PermissionCategoryID);

        // nothing to do if there are no new comments
        if ($Discussion->LastCommentID <= $LastCommentID) {
            return;
        }

        // build comment views
        $HtmlOut = '';
        $ItemCount = 0;
        $Session = Gdn::Session();
        // include view for comments
        include_once(Gdn::Controller()->FetchViewLocation('helper_functions', 'Discussion', 'Vanilla'));

        $CommentModel = new CommentModel();

        if (Gdn::Cache()->Type() != 'ct_null') {
            // if forum is using a cache get comments page by page with PageWhere
            $Limit = Gdn::Config('Vanilla.Comments.PerPage', 30);
            list($Offset, $Limit) = OffsetLimit($Page, $Limit);

            $Comments = array();
            do {
                // Get only "one page" of comments because that is being cached
                $PageComments = $CommentModel->Get($DiscussionID, $Limit, $Offset)->ResultArray();
                if ($PageComments) {
                    // add that up to one single array
                    $Comments = array_merge($Comments, $PageComments);
                }
                $Offset += $Limit;
            } while (count($PageComments) == $Limit);
        } else {
            // no cache, so fetch all
            $Comments = $CommentModel->Get($DiscussionID)->ResultArray();
        }

        foreach ($Comments as $Comment) {
            $CommentID = $Comment['CommentID'];
            ++$ItemCount;
            if ($CommentID > $LastCommentID) {
                ob_start();
                WriteComment($Comment, $this, $Session, $ItemCount);
                $HtmlOut .= ob_get_clean();
            }
        }

        // update UserDiscussion table
        $CountComments = $Discussion->CountComments;
        $CommentModel->SetWatch($Discussion, $CountComments, $CountComments, $CountComments);

        echo(json_encode(array('LastCommentID' => $CommentID, 'UnreadComments' => $HtmlOut)));
    }
}
