

var map = null;

function initMap() {

    //resizefunction();
    $("#contact-form").find("input,textarea,select").jqBootstrapValidation({
        preventSubmit: true,
        submitError: function ($form, event, errors) {
        },
        submitSuccess: function ($form, event) {

            $.ajax({
                cache: false,
                url: "/pages/contact-submit",
                data: $form.serialize(),
                type: "post",
                success: function (result) {
                    
                    if (result.returnVal == "0") {
                        $('#name').val("");
                        $('#email').val("");
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

    if (map == null) {
        setTimeout(function () {
            map = new NosiLeafletMap('map', {
                map_id: 'map',
                map_center_lat: 40.981145399978686,
                map_center_long: 29.082334041595455,
                map_zoom: 17,
                region_searchbox: false,
                drawnItems: false
            });
            map.load_map();


            map.add_marker({
                lat: 40.981145399978686,
                lng: 29.082334041595455,
                unique_id: 1,
                marker_type: '0',
                //number: '<i class=\"icon-flag\"></i>',
                //areaIcon:true,
                prm_object: null,
                popup: "Sezinsoft Bilişim Teknolojileri",
                isWidth: 48,
                isHeight: 64,
                draggable: false,
                iconUrl: '/asset/img/routewix_marker.png',
                unique_code: 1
            });
        }, 50);
    }
}

$(document).ready(function () {
    initMap();
});