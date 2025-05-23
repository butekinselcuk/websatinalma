
function MessageBox(contentId, className, message)
{
    var _url = "/pages/pageMessage";
    var _data = {
        message: message,
        className: className
    };
    $.ajax({
        cache: false,
        url: _url,
        data: _data,
        type: "post",
        success: function (result) {
            $("#"+contentId).empty().append(result);
        }, error: function (e) {
            //OpenMessageShow("Sonuç", e.responseText);
        }
    });
}