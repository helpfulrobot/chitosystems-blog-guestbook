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
        "GuestBookLinking" => "BlogPost",
    );

    private static $summary_fields = array(
        'Title',
        'Email',
        'Author',
        'Created'
    );

    private static $better_buttons_actions = array(
        'ApprovePost',
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

    //This action will allow the admin to set this items as feaured or not
    public function updateBetterButtonsActions($actions)
    {
        $actions->push(
            BetterButtonCustomAction::create('ApprovePost', 'Approve')
                ->setRedirectType(BetterButtonCustomAction::REFRESH)
                ->setSuccessMessage('This Post Has Been Approved')
        );
        return $actions;
    }

    //markAsFeatured
    public function ApprovePost()
    {
        $this->Moderated = true;
        $this->write();

        $parent = $this->getParent();
        if ($parent->GuestBookID) {
            $GuestBook = $parent->GuestBook();
            $GuestBookPageChildClass = $this->getGuestBookPageChildClass($GuestBook->ClassName);
            if ($GuestBookPageChildClass) {
                $GuestBook = $GuestBookPageChildClass::create();
                $GuestBook->Title = $this->Title;
                $GuestBook->AuthorNames = $this->Author;
                $GuestBook->Content = $this->Content;
                $GuestBook->PublishDate = SS_Datetime::now()->getValue();

                $GuestBook->write();
                $GuestBook->doRestoreToStage();
                $GuestBook->writeToStage('Stage');
                $this->GuestBookLinkingID = $this->ID;
            }
        }

        $this->write();

    }


    private function getGuestBookPageChildClass($ClassName)
    {
        return Config::inst()->get($ClassName, 'allowed_children');
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->BlogGuestBookPage();
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
        //$htmlEditorConfig = HtmlEditorConfig::get_active();
        //$purifier = new HtmlPurifierSanitiser($htmlEditorConfig);
        //return $purifier->sanitise($dirtyHtml);
    }

}