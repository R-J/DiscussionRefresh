<?php if (!defined('APPLICATION')) exit();

$PluginInfo['DiscussionRefresh'] = array(
   'Name' => 'Discussion Refresh',
   'Description' => 'Shows comments that have been posted to a dsicussion between opening it and pressing preview button',
   'Version' => '0.1',
   'RequiredApplications' => array('Vanilla' => '2.0.18'), 
   'Author' => "Robin"
);

/*
    js: LastCommentID = max(NewComments)
    js: insert html for new comments
*/
class DiscussionRefreshPlugin extends Gdn_Plugin {

   // insert js at the end of discussion controller
   public function DiscussionController_AfterBody_Handler($Sender) {
      echo '<script src="/plugins/DiscussionRefresh/js/discussionrefresh.js"></script>';
   }
   
   // route all requests to /discussion/discussionrefreh/... to function Controller_...
   public function DiscussionController_DiscussionRefresh_Create($Sender, $Args) {
      return $this->Dispatch($Sender);
   }
   
   //get all comments of a discussion and return only the latest
   public function Controller_Index($Sender) {
      $Args = $Sender->RequestArgs;   

      // no request, no result
      if (count($Args) == 0)
         return FALSE;
     
      $DiscussionID = $Args[0];
      $CommentID = $Args[1];

      // check for proper discussionid
      if (!is_numeric($DiscussionID))
         return FALSE;
      
      // check permissions
      $DiscussionModel = new DiscussionModel();
      $Discussion = $DiscussionModel->GetID($DiscussionID);
      $Sender->Permission('Vanilla.Discussions.View', TRUE, 'Category', $Discussion->PermissionCategoryID);

      // if no comment id is given, return all comments
      if (!is_numeric($CommentID))
         $CommentID = 0;

      $CommentModel = new CommentModel();
      $Comments = $CommentModel->Get($DiscussionID);

      // keep only new comments
      $CommentsFiltered = array();
      foreach ($Comments as $Comment) {
         if ($Comment->CommentID > $CommentID) {
            $CommentsFiltered[] = $Comment;
         }
      }
      $Comments->ImportDataset($CommentsFiltered);

      // render all comments
      $Sender->SetData('CommentData', $Comments, TRUE);
      $Sender->Render($Sender->FetchViewLocation('comments', 'discussion'));
   }
}
