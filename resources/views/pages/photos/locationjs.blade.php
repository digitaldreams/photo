<script type="text/javascript">
    var jq = $;

    function placeAutoComplete(inputId, dropdown, country) {
        var btn = jq("#address");
        jq("body").on('keyup', inputId, function () {
            var queryString = jq(this).val();
            btn = jq(this);
            var service = new google.maps.places.AutocompleteService();
            //componentRestrictions: {country: 'bd'}
            service.getPlacePredictions({
                input: queryString,
            }, function (data, status) {
                var htmlSuggestion = '';
                for (var i = 0; i < data.length; i++) {
                    htmlSuggestion += '<li class="list-group-item" data-lat="" data-long="" data-place_id="'+data[i]['place_id']+'">' + data[i]['description'] + '</li>';
                }
                jq(dropdown).html(htmlSuggestion);
            })
        })//End of on keyup function
        jq("body").on('click', dropdown + " li", function (e) {
            var newPlace = jq(this).text();
            jq(btn).val(newPlace);
            jq('#api_place_id').val(jq(this).data('place_id'));
            jq(dropdown).empty();
        });
        jq(inputId).on('blur', function (e) {
            e.preventDefault();
            if (jq(this).val() == "") {
                jq(dropdown).empty();
            }
        })
    }

    placeAutoComplete("#address", '#locationDropdown')
</script>