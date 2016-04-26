$(document).ready(function () {
    $("#$FormName").submit(function () {
        var validate = GuestBookAjaxFormMainValidator.validate($("#$FormName"), [$Required]);
        if (validate === "Validate") {
            return true;
        }
        return false;
    });
});