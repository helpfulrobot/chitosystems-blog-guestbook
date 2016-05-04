<?php

/**
 * Class BlogGuestBookNotifierExtension
 */
class BlogGuestBookNotifierExtension extends Extension
{
    public function onAfterSubmission(BlogGuestBookSubmission $submission)
    {

        $parent = $submission->getParent();

        // Ask parent to submit all recipients
        $oRecipients = $parent->EmailRecipients();
        foreach ($oRecipients as $oRecipient) {
            $this->notifyCommentRecipient($submission, $parent, $oRecipient);
        }
    }

    /**
     * Send comment notification to a given recipient
     *
     * @param BlogGuestBookSubmission $submission
     * @param DataObject $parent Object with the {@see CommentNotifiable} extension applied
     * @param Member|string $recipient Either a member object or an email address to which notifications should be sent
     */
    public function notifyCommentRecipient($submission, $parent, $recipient)
    {
        $subject = $parent->NotificationSubject;
        $sender = $submission->Email;
        $template = "BlogGuestBoogEmail";
        // Validate email
        // Important in case of the owner being a default-admin or a username with no contact email
        $to = $recipient->EmailAddress;
        if (!$this->isValidEmail($to)) return;
        // Prepare the email
        $email = new Email();
        $email->setSubject($subject);
        $email->setFrom($sender);
        $email->setTo($to);
        $email->setTemplate($template);
        $email->populateTemplate(array(
            'Parent' => $parent,
            'Submission' => $submission,
            'Recipient' => $recipient
        ));
        if ($recipient instanceof Member) {
            $email->populateTemplate(array(
                'ApproveLink' => $submission->ApproveLink($recipient),
                'HamLink' => $submission->HamLink($recipient),
                'SpamLink' => $submission->SpamLink($recipient),
                'DeleteLink' => $submission->DeleteLink($recipient),
            ));
        }
        
        return $email->send();
    }

    /**
     * Validates for RFC 2822 compliant email adresses.
     *
     * @see http://www.regular-expressions.info/email.html
     * @see http://www.ietf.org/rfc/rfc2822.txt
     *
     * @param string $email
     * @return boolean
     */
    public function isValidEmail($email)
    {
        if (!$email) return false;
        $pcrePattern = '^[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*'
            . '@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$';
        // PHP uses forward slash (/) to delimit start/end of pattern, so it must be escaped
        $pregSafePattern = str_replace('/', '\\/', $pcrePattern);
        return preg_match('/' . $pregSafePattern . '/i', $email);
    }

}