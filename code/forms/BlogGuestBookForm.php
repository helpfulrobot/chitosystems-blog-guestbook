<?php

class BlogGuestBookForm extends Form
{
    /**
     * @param Controller $controller
     * @param String $name
     */
    function __construct(Controller $controller, $name)
    {
        UtilityExtra::includeTinymce();
        $f = new FieldList();
        $f->push(BootstrapTextField::create('Title'));
        $f->push(BootstrapTextField::create('Author'));
        $f->push(BootstrapEmailField::create('Email'));
        $f->push(TextareaField::create('Content')->addExtraClass('full-width mceEditor'));

        $actions = new FieldList(
            $btn = new FormAction('doSubmit', 'Submit')
        );
        $btn->addExtraClass("btn btn-default");

        $aRequiredFields = array();
        $aRequiredFields[] = "Title";
        $aRequiredFields[] = "Email";
        $aRequiredFields[] = "Author";
        $aRequiredFields[] = "Content";
        $requiredFields = new RequiredFields();

        parent::__construct($controller, $name, $f, $actions, $requiredFields);
        $this->addExtraClass('form-horizontal ' . get_class($this));
        $this->loadValidationScripts($this, $aRequiredFields);
    }

    function forTemplate()
    {
        return $this->renderWith(array($this->class, 'Form'));
    }

    /**
     * @param array $raw_data
     * @param Form $form
     * @return bool|string
     */
    function doSubmit(array $raw_data, Form $form)
    {
        $controller = $form->getController();
        $data = Convert::raw2sql($raw_data);
        if (strlen($data['time']) > 0) {
            return false;
        }
        $parent = $controller;

        $submission = BlogGuestBookSubmission::create();
        $form->saveInto($submission);
        $submission->BlogGuestBookPageID = $controller->ID;
        $submission->write();

        $response = $controller->OnCompleteMessage;
        $recipients = $controller->EmailRecipients();
        if (count($recipients)) {
            foreach ($recipients as $recipient) {
                $From = $data['Email'];
                $Subject = sprintf("%s: %s", $controller->SiteConfig()->Title, $controller->EmailSubject);
                $To = $recipient->EmailAddress;
                $email = new Email($From, $To, $Subject);
                $email->setTemplate('SendGuestBookSubmission');
                $email->populateTemplate($data);
                $email->send();
            }
        }
        return sprintf("<div class=\"alert alert-success\">%s</div>", $response);
    }


    /**
     *
     * @param type $form
     * @param type $aRequiredFields
     */
    function loadValidationScripts($form, $aRequiredFields)
    {
        $aRequired = array();
        foreach ($aRequiredFields as $field) {
            $aRequired [] = sprintf("'%s_%s'", $form->FormName(), $field);
        }
        $vars = array(
            'FormName' => $form->FormName(),
            'Required' => implode(',', $aRequired),
            'EmailFieldId' => sprintf("#%s_%s", $form->FormName(), 'Email')
        );
        Requirements::javascriptTemplate(PROJECT . '/js/GuestBookFormValidation.js', $vars);
    }

}
