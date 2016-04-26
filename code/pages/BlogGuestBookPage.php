<?php

class BlogGuestBookPage extends Page
{
    private static $db = array(
        "NotificationSubject" =>"Varchar(255)",
    );
    private static $has_one = array(
        "GuestBook" => "Blog",
    );
    private static $has_many = array(
        "GuestBookSubmissions" => "BlogGuestBookSubmission",
    );

    private static $allowed_children = array();

    public function getCMSFields()
    {
        $f = parent::getCMSFields();

        $Blogs = Blog::get();
        $BlogMap = $Blogs ? $Blogs->map() : [];

        $f->addFieldToTab('Root.GuestBook', DropdownField::create("GuestBookID", "GuestBook")
            ->setEmptyString("--select one--")
            ->setSource($BlogMap));

        $GridFieldConfig = GridFieldConfig_RecordEditor::create();
        $GridField = new GridField('GuestBookSubmissions', 'GuestBookSubmissions', $this->GuestBookSubmissions(), $GridFieldConfig);
        $f->addFieldToTab('Root.GuestBookSubmissions', $GridField);

        return $f;
    }

}

class BlogGuestBookPage_Controller extends Page_Controller
{

    /**
     * @var array
     */
    private static $allowed_actions = array("BlogGuestBookForm");

    /**
     * @var int
     */
    private $limit = 100;


    public function init()
    {
        parent:: init();
        Requirements::javascript(BLOG_GUESTBOOK_DIR."/js/GuestBookAjaxFormMainValidator.js");
        Requirements::css(BLOG_GUESTBOOK_DIR . "/css/blog-guest-book.css");

    }

    /**
     * @return mixed
     */
    function BlogGuestBookForm()
    {
        return BlogGuestBookForm::create($this, __FUNCTION__);
    }
}
