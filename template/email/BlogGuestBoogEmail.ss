<p> There is a new Guest Book entry on the <a href="{$.Link}">{$Parent.Title}</a> page. </p>
<p><strong>Please review the Submission for approval.</strong></p>
<ul>
    <li>{$Submission.Created.Nice}</li> <% if $Submission.AuthorName %>
    <li>{$Submission.AuthorName}</li> <% end_if %> <% if $Submission.Email %>
    <li>{$Submission.Email}</li> <% end_if %> <% if $IsSpam %>
    <li><em>This Submission was automatically detected as spam</em></li> <% end_if %> </ul>
<blockquote>{$Submission.Submission}</blockquote>
<% if $ApproveLink || $HamLink || $SpamLink || $DeleteLink %>
    <ul>
        <% if $ApproveLink %>
            <li>
                <strong>Approve it: </strong><a href="$ApproveLink.ATT">$ApproveLink.XML</a>
            </li>
        <% end_if %>
        <% if $SpamLink %>
            <li><strong>Mark as Spam: </strong><a href="$SpamLink.ATT">$SpamLink.XML</a></li> <% end_if %>
        <% if $HamLink %>
            <li><strong>Mark as not Spam: </strong><a href="$HamLink.ATT">$HamLink.XML</a>
            </li> <% end_if %>
        <% if $DeleteLink %>
            <li><strong>Delete it: </strong><a href="$DeleteLink.ATT">$DeleteLink.XML</a></li> <% end_if %>
    </ul>
<% else %>
    You can view or moderate this Guest Book entry at <a href="{$Submission.Link}">{$Parent.Title}</a>
<% end_if %>