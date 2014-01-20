<?php if (!defined('APPLICATION')) exit();

$PluginInfo['DiscussionRefresh'] = array(
   'Name' => 'Discussion Refresh',
   'Description' => 'Shows comments that have been posted to a dsicussion between opening it and pressing preview button',
   'Version' => '0.1',
   'RequiredApplications' => array('Vanilla' => '2.0.18'), 
   'HasLocale' => FALSE,
   'Author' => "Robin",
   'RegisterPermissions' => FALSE
);

class DiscussionRefreshPlugin extends Gdn_Plugin {
/*
    php: hook into some discussion event to insert a javascript file
ok    
    js: get the LastCommentID from current form and store it somehow
ok. somehow, by now...    
    js: "hook" (attach an event in js-speak?) to the preview button
    
    js: if preview is pressed, call a php function "GetNewComments(DiscussionID, LastCommentID)" (request /plugin/pluginname/getnewcommentids/DiscussionID/LastCommentID).
    php: GetNewComments(LastCommentID): NewComments = select CommentID from Discussion where DiscussionID = $DiscussionID and LastCommentID > $LastCommentID. Return CommentModel::Get(NewComments) and max(NewComments)
    js: LastCommentID = max(NewComments)
    js: insert html for new comments
*/


   // insert js into discussion controller
   public function DiscussionController_AfterBody_Handler($Sender) {
      echo '<script src="/plugins/DiscussionRefresh/js/discussionrefresh.js"></script>';
   }
   
   // route all requests to /plugin/discussionrefreh/... to function Controller_...
   public function DiscussionController_DiscussionRefresh_Create($Sender, $Args) {
      return $this->Dispatch($Sender);
   }
   
   // default action is to return comments of a given discussion
   public function Controller_Index($Sender) {
      //get all comments of a discussion and return only the latest
      $Args = $Sender->RequestArgs;   

      // no request, no result
      if (count($Args) == 0)
         return FALSE;
     
      $DiscussionID = $Args[0];
      $CommentID = $Args[1];

      // check for proper discussionid
      if (!is_numeric($DiscussionID))
         return FALSE;

      $CommentModel = new CommentModel();
      $Comments = $CommentModel->Get($DiscussionID)->Result();

      // if no comment id is given, return all comments
      if (!is_numeric($CommentID))
         $CommentID = 0;

decho($Comments);
      foreach ($Comments as $Index => $Comment) {
         if ($Comment->CommentID <= $CommentID) {
            unset($Comments[$Index]);
         }
      }
decho($Comments);

      $Sender->SetData('CommentData', $Comments, TRUE);
      //$Sender->DeliveryType = 'VIEW';
      //&DeliveryMethod=JSON
      $Sender->Render($Sender->FetchViewLocation('comments', 'discussion'));
   }
}
