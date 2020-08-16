<script type="text/javascript">
    $("body").bind("paste", function (e) {
        var pastedData = e.originalEvent.clipboardData.getData('text');

        if (confirm('Are you sure you want to download image from ' + pastedData)) {
            pasteUrl(pastedData, '{{route('photo::photos.downloadUrl')}}', '');
        }

    });


    function pasteUrl(pastedData, url, caption) {
        pastedData = pastedData.split(/[?#]/)[0];
        var ext = pastedData.split('.').pop();
        var allowedExt = ["jpg", "jpeg", "png", 'gif', 'webp'];
        if (allowedExt.indexOf(ext) !== -1) {
            $("#downloading-status").text('Downloading....');
            $.get(url, {
                'url': pastedData,
                'caption': caption,
            }).then(function (response) {
                if (response.success) {
                    window.location.href = response.url;
                } else {
                    alert(response.message);
                }
            });
        } else {
            alert(ext + ' extension are not allowed');
        }
    }

    function dropHandler(ev) {
        // Prevent default behavior (Prevent file from being opened)
        ev.preventDefault();
        var caption = '';

        if (ev.dataTransfer.items) {
            var imageUrl = ev.dataTransfer.getData('text/html');

            var rex = /src="?([^"\s]+)"?\s*/;
            var altRex = /alt="?([^"]+)"?\s*/;
            var url = rex.exec(imageUrl);
            var alt = altRex.exec(imageUrl);

            if (alt != null && alt.length > 0) {
                caption = alt[1];
            }

            if (url != null && url.length > 0) {
                if (confirm('Are you sure you want to download image from ' + url[1])) {
                    pasteUrl(url[1], '{{route('photo::photos.downloadUrl')}}', caption);
                }
            }
        }
    }

    function dragOverHandler(ev) {
        ev.preventDefault();
    }
</script>
