$(document).ready(function(){
    $('.CommentButton, a.PreviewButton, a.WriteButton, a.DraftButton').livequery('click', function() {
        // Get LastCommentID
        var elLastCommentID = document.getElementById('LastCommentID');
        if (elLastCommentID) {
            var lastCommentID = elLastCommentID.getAttribute('value');
        } else {
            // if it is not there, there are already more pages to display on screen => return
            return;
        }

        var discussionID = document.getElementById('Form_DiscussionID').getAttribute('value');

        // Get page number from canonical link
        var canonicalLink = document.querySelector("link[rel='canonical']").getAttribute("href");
        var patt = /.*\/p(\d+)$/;
        var pageCount = patt.exec(canonicalLink);
        if (pageCount) {
            var page = pageCount[1];
        } else {
            // if there is none, assume we are on page 1
            page = 1;
        }

        $.ajax({
            url: gdn.url('/discussion/discussionrefresh/' + discussionID + '/' + lastCommentID + '/' + page),
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
