<script type="text/javascript">
    var jq = $;

    function placeAutoComplete(inputId, dropdown, country) {
        var btn = jq("#address");
        jq("body").on('keyup', inputId, function () {
            var queryString = jq(this).val();
            btn = jq(this);
            var service = new google.maps.places.AutocompleteService();
            //componentRestrictions: {country: 'bd'}
            var options = [];
            service.getPlacePredictions({
                input: queryString,
                options
            }, function (data, status) {
                var htmlSuggestion = '';
                for (var i = 0; i < data.length; i++) {
                    htmlSuggestion += '<li class="list-group-item" data-lat="" data-long="" data-place_id="' + data[i]['place_id'] + '">' + data[i]['description'] + '</li>';
                }
                jq(dropdown).html(htmlSuggestion);
            })
        })//End of on keyup function
        jq("body").on('click', dropdown + " li", function (e) {
            var newPlace = jq(this).text();
            jq(btn).val(newPlace);
            var placeId = jq(this).data('place_id');
            if (placeId.length > 1) {
                var geocoder = new google.maps.Geocoder;
                geocoder.geocode({'placeId': placeId}, function (results, status) {
                    if (status === 'OK') {
                        if (results[0]) {
                            var data = getLocationDetails(results[0]);
                            jq("#place_api_data").val(JSON.stringify(data));
                        } else {
                            console.log('No results found');
                        }
                    } else {
                        console.log('Geocoder failed due to: ' + status);
                    }
                });
            }

            jq(dropdown).empty();
        });
        jq(inputId).on('blur', function (e) {
            e.preventDefault();
            if (jq(this).val() == "") {
                jq(dropdown).empty();
            }
        })
    }

    function getLocationDetails(result) {
        return {
            'latitude': result.geometry.location.lat(),
            'longitude': result.geometry.location.lng(),
            'address': result.formatted_address,
            'city': getAddressComponent(result.address_components, 'locality'),
            'state': getAddressComponent(result.address_components, 'administrative_area_level_1'),
            'country': getAddressComponent(result.address_components, 'country'),
            'place_id': result.place_id
        }
    }

    function getAddressComponent(components, type) {
        for (var t = 0; t < components.length; t++) {
            var comp = components[t];
            if (comp.types.indexOf(type) !== -1) {
                return comp.long_name;
            }
        }
        return false;
    }
    function readURL(input,img) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#'+img).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    placeAutoComplete("#address", '#locationDropdown')
</script>