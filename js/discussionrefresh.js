// Get info from source
var discussionID = document.getElementById('Form_DiscussionID').getAttribute('value');
var lastCommentID = document.getElementById('LastCommentID').getAttribute('value');

$('a.PreviewButton').click(function() {
   alert(discussionID);

   // progress bar 
   // $('.MessageForm').find('div.Tabs ul:first').after('<span class="TinyProgress">&#160;</span>');
   // $('a.PreviewButton').parents('div.Comment').find('div.Meta span:last').after('<span class="TinyProgress">&#160;</span>');

   var action = '/plugin/discussionrefresh/' + '/' + discussionID + '/' + lastCommentID;
// $Sender->DeliveryType=VIEW&DeliveryMethod=JSON   
   alert(action); 
         $.ajax({
         url: action,
         dataType: 'json',
         error: function(XMLHttpRequest, textStatus, errorThrown) {
            // Remove any old popups
            $('.Popup,.Overlay').remove();
            var msg;
            if (XMLHttpRequest.responseText)
               try {
                  var data = $.parseJSON(XMLHttpRequest.responseText);
                  if (data.Exception)
                     msg = data.Exception;
                  else
                     msg = 'Unkown error.';
               } catch (ex) {
                  msg = XMLHttpRequest.responseText;
               }
            else {
               if(textStatus == 'timeout')
                  msg = 'Your request took too long to complete and timed out. Please try again.';
               else
                  msg = textStatus;
            }
            msg = '<h1>Error</h1><p class="Wrap">' + msg + '</div>';


            $.popup({}, msg);
         },
         success: function(json) {
            alert('success');
/*         
            json = $.postParseJson(json);
            
            var processedTargets = false;
            // If there are targets, process them
            if (json.Targets && json.Targets.length > 0) {
               for(i = 0; i < json.Targets.length; i++) {
                  if (json.Targets[i].Type != "Ajax") {
                     json.Targets[i].Data = json.Data;
                     processedTargets = true;
                     break;
                   }
               }
               gdn.processTargets(json.Targets);
            }

            // If there is a redirect url, go to it
            if (json.RedirectUrl != null && jQuery.trim(json.RedirectUrl) != '') {
               resetCommentForm(btn);
               clearCommentForm(btn);
               window.location.replace(json.RedirectUrl);
               return false;
            }

            // Remove any old popups if not saving as a draft
            if (!draft && json.FormSaved == true)
               $('div.Popup,.Overlay').remove();

            var commentID = json.CommentID;
            
            // Assign the comment id to the form if it was defined
            if (commentID != null && commentID != '') {
               $(inpCommentID).val(commentID);
            }

            if (json.DraftID != null && json.DraftID != '')
               $(inpDraftID).val(json.DraftID);

            if (json.MyDrafts != null) {
               if (json.CountDrafts != null && json.CountDrafts > 0)
                  json.MyDrafts += '<span>'+json.CountDrafts+'</span>';
                  
               $('ul#Menu li.MyDrafts a').html(json.MyDrafts);
            }

            // Remove any old errors from the form
            $(frm).find('div.Errors').remove();
            if (json.FormSaved == false) {
               $(frm).prepend(json.ErrorMessages);
               json.ErrorMessages = null;
            } else if (preview) {
               $(frm).trigger('PreviewLoaded', [frm]);
               $(parent).find('li.Active').removeClass('Active');
               $(btn).parents('li').addClass('Active');
               $(frm).find('#Form_Body').after(json.Data);
               $(frm).find('#Form_Body').hide();
               
            } else if (!draft) {
               // Clean up the form
               if (processedTargets)
                  btn = $('div.CommentForm :submit');

               resetCommentForm(btn);
               clearCommentForm(btn);

               // If editing an existing comment, replace the appropriate row
               var existingCommentRow = $('#Comment_' + commentID);
               if (processedTargets) {
                  // Don't do anything with the data b/c it's already been handled by processTargets
               } else if (existingCommentRow.length > 0) {
                  existingCommentRow.after(json.Data).remove();
                  $('#Comment_' + commentID).effect("highlight", {}, "slow");
               } else {
                  gdn.definition('LastCommentID', commentID, true);
                  // If adding a new comment, show all new comments since the page last loaded, including the new one.
                  if (gdn.definition('PrependNewComments') == '1') {
                     $(json.Data).prependTo('ul.Discussion');
                     $('ul.Discussion li:first').effect("highlight", {}, "slow");
                  } else {
                     $(json.Data).appendTo('ul.Discussion');
                     $('ul.Discussion li:last').effect("highlight", {}, "slow");
                  }
               }
               // Remove any "More" pager links (because it is typically replaced with the latest comment by this function)
               if (gdn.definition('PrependNewComments') != '1') // If prepending the latest comment, don't remove the pager.
                  $('#PagerMore').remove();

               // Let listeners know that the comment was added.
               $(document).trigger('CommentAdded');
               $(frm).triggerHandler('complete');
            }
            gdn.inform(json);
            return false;
*/         
         },
         complete: function(XMLHttpRequest, textStatus) {
            // Remove any spinners, and re-enable buttons.
            // $('span.TinyProgress').remove();
         }
      });

});
