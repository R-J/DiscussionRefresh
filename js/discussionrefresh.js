$(document).ready(function(){
    $('.CommentButton, a.PreviewButton, a.WriteButton, a.DraftButton').livequery('click', function() {
        var discussionID = document.getElementById('Form_DiscussionID').getAttribute('value');
        var elLastCommentID = document.getElementById('LastCommentID');
        var lastCommentID = elLastCommentID.getAttribute('value');

        $.ajax({
            url: gdn.url('/discussion/discussionrefresh/' + discussionID + '/' + lastCommentID),
            dataType: 'json',
            cache: false,
            success: function(comments){
                if (comments) {
                    // insert new comments and take care for not mixing up already posted comments and current comment
                    if (lastCommentID == 0) {
                        // When there are no comments yet, simply put the comments at the top
                        $(comments.UnreadComments).prependTo('ul.Comments');
                    } else {
                        // insert comments after lastCommentID
                        $(comments.UnreadComments).insertAfter('#Comment_' + lastCommentID);
                    }
                    $('ul.Comments li:first').effect("highlight", {}, "slow");
                    // set new value for current LastCommentID if it has not already been set by the current comment
                    if (elLastCommentID.getAttribute('value') < comments.LastCommentID) {
                        elLastCommentID.setAttribute('value', comments.LastCommentID);
                    }
                }
            }
        });
    });
});
