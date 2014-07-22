<?php defined('APPLICATION') or die();

$PluginInfo['DiscussionRefresh'] = array(
    'Name' => 'Discussion Refresh',
    'Description' => 'Loads new comments when either "Preview", "Edit", "Save Draft" or "Post Comment" button is pressed.',
    'Version' => '0.1',
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
        $DiscussionID = $Args[0] + 0;
        $LastCommentID = $Args[1] + 0;

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
        $Comments = $CommentModel->Get($DiscussionID);

        foreach ($Comments as $Comment) {
            $CommentID = $Comment->CommentID;
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
