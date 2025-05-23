
$(document).ready(function () {
    $("#demo-form").find("input,textarea,select").jqBootstrapValidation({
        preventSubmit: true,
        submitError: function ($form, event, errors) {
        },
        submitSuccess: function ($form, event) {

            $.ajax({
                cache: false,
                url: "/pages/demo-submit",
                data: $form.serialize(),
                type: "post",
                success: function (result) {

                    if (result.returnVal == "0") {
                        $('#company').val("");
                        $('#user').val("");
                        $('#phone').val("");
                        $('#email').val("");
                        $('#passenger').val("");
                        $('#message').text("");
                        MessageBox("page-message", "has-error-false", result.returnText);
                    }
                    else {
                        MessageBox("page-message", "has-error-true", result.returnText);
                    }

                }, error: function (e) {
                    MessageBox("page-message", "has-error-true", e.responseText);
                    return false;
                }
            });
            event.preventDefault();
        },
        filter: function () {
            return $(this).is(":visible");
        }
    });
});