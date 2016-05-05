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
        'Date' => 'Date',
        'Content' => 'Text',
        'Moderated' => 'Boolean(0)',
        'IsApproved' => 'Boolean(0)',
        'IsSpam' => 'Boolean(0)',

    );
    private static $defaults = array(
        'Moderated' => 0,
        'IsSpam' => 0,
    );
    private static $has_one = array(
        "BlogGuestBookPage" => "BlogGuestBookPage",
        "GuestBookLinking" => "BlogPost",
        "Image" => "Image",
    );

    private static $summary_fields = array(
        'Title',
        'Email',
        'Author',
        'Moderated',
        'IsSpam',
        'IsApproved',
        'Created'
    );

    private static $better_buttons_actions = array(
        'approve',
        'deny',

    );

    function getCMSFields()
    {
        $f = parent::getCMSFields();
        $f->removeByName(array("BlogGuestBookPageID", "GuestBookLinkingID", "Moderated", "IsSpam", "IsApproved"));

        return $f;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // Sanitize HTML, because its expected to be passed to the template unescaped later
        //$this->Content = $this->purifyHtml($this->Content);

    }

    public function getBetterButtonsActions()
    {
        $fields = parent::getBetterButtonsActions();
        if ($this->IsApproved) {
            $fields->push(
                BetterButtonCustomAction::create('deny', 'Deny')
                    ->setRedirectType(BetterButtonCustomAction::REFRESH)
            );
        } else {
            $fields->push(
                BetterButtonCustomAction::create('approve', 'Approve')
                    ->setRedirectType(BetterButtonCustomAction::REFRESH)
            );
        }

        if ($this->IsApproved && $this->IsApproved) {
            $fields->push(
                new BetterButtonLink_TargetWindow(
                    'View Post on site',
                    $this->getPublishedBlogLink()
                )
            );
            $fields->push(
                new BetterButtonLink(
                    'View In CMS',
                    $this->getCMSEditLink()
                )
            );
        }

        return $fields;
    }


    function getPublishedBlogLink()
    {

        return $this->GuestBookLinking()->Link();
    }

    function getCMSEditLink()
    {
        return Controller::join_links("admin/pages/edit/show/", $this->GuestBookLinking()->ID, "/");
    }


    /**
     * //This action will allow the admin to set this items as feaured or not
     * public function updateBetterButtonsActions($actions)
     * {
     * $actions->push(
     * BetterButtonCustomAction::create('ApprovePost', 'Approve')
     * ->setRedirectType(BetterButtonCustomAction::REFRESH)
     * ->setSuccessMessage('This Post Has Been Approved')
     * );
     * return $actions;
     * }
     *
     * */

    //markAsFeatured
    public function approve()
    {
        $this->Moderated = true;
        $this->IsApproved = true;
        $this->write();

        $parent = $this->getParent();

        //debug::show($parent->ClassName);
        if ($parent->GuestBookID) {

            $GuestBookPage = $parent->GuestBook();
            $oBlogParent = $GuestBookPage->Level(1);

            //$GuestBookPageChildClass = $this->getGuestBookPageChildClass($GuestBook);

            $GuestBook = new BlogPost();
            $GuestBook->Title = $this->Title;
            $GuestBook->AuthorNames = $this->Author;
            $GuestBook->Content = $this->Content;
            $GuestBook->ShowInMenu = $this->Content;
            $GuestBook->PublishDate = SS_Datetime::now()->getValue();
            $GuestBook->ParentID = $oBlogParent->ID;
            $GuestBook->FeaturedImageID = $this->ImageID;
            $GuestBook->doPublish();
            $GuestBook->write();
            $GuestBook->doRestoreToStage();
            $GuestBook->writeToStage("Stage", "Live");
            $this->GuestBookLinkingID = $GuestBook->ID;
        }

        $this->write();
        return 'Submission published';
    }

    public function deny()
    {


        $this->IsApproved = false;
        $this->IsSpam = true;
        $this->Moderated = true;
        $this->write();

        return 'Denied for publication';
    }


    private function getGuestBookPageChildClass($GuestBook)
    {
        $oParent = $GuestBook->Level(1);
        if ($oParent->ClassName === 'NewsHolder') {
            return "NewsPage";
        } else {
            return "BlogPost";
        }
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