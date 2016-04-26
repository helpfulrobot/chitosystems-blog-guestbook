$(document).ready(function () {
    $("#$FormName").on('submit',function () {
        var validate = GuestBookAjaxFormMainValidator.validate($("#$FormName"), [$Required]);
        if (validate === "Validate") {
            return true;
        }
        return false;
    });
});