<?php

/**
 * Class BlogGuestBookSubmission
 *
 */
class BlogGuestBookSubmission extends DataObject
{
    private static $default_sort = "Created Desc";

    private static $db = array(
        'Title' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'Author' => 'Varchar(255)',
        'Content' => 'HTMLText',
        'Moderated' => 'Boolean(0)',
        'IsSpam' => 'Boolean(0)',

    );
    private static $defaults = array(
        'Moderated' => 0,
        'IsSpam' => 0,
    );
    private static $has_one = array(
        "BlogGuestBookPage" => "BlogGuestBookPage",
    );

    private static $summary_fields = array(
        'Title',
        'Email',
        'Author',
        'Created'
    );

    function getCMSFields()
    {
        $f = parent::getCMSFields();
        $f->removeByName(array("BlogGuestBookPageID"));

        return $f;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // Sanitize HTML, because its expected to be passed to the template unescaped later
        //$this->Content = $this->purifyHtml($this->Content);

    }

    /**
     * @return Comment_SecurityToken
     */
    public function getSecurityToken()
    {
        return Injector::inst()->createWithArgs('GuestBook_SecurityToken', array($this));
    }

    /**
     * Link to approve this comment
     *
     * @param Member $member
     *
     * @return string
     */
    public function ApproveLink($member = null)
    {
        if ($this->canEdit($member) && !$this->Moderated) {
            return $this->actionLink('approve', $member);
        }
    }

    /**
     * @param $dirtyHtml
     * @return String
     */
    public function purifyHtml($dirtyHtml)
    {
        $htmlEditorConfig = HtmlEditorConfig::get_active();
        $purifier = new HtmlPurifierSanitiser($htmlEditorConfig);
        return $purifier->sanitise($dirtyHtml);
    }

}