<script type="text/javascript">
    $("body").bind("paste", function (e) {
        var pastedData = e.originalEvent.clipboardData.getData('text');

        if (confirm('Are you sure you want to download image from ' + pastedData)) {
            pasteUrl(pastedData, '{{route('photo::photos.downloadUrl')}}', '');
        }

    });
    $("body").bind('drop', function (e) {
        if (typeof e.originalEvent.dataTransfer.files !== 'undefined') {
            var files = e.originalEvent.dataTransfer.files;

            for (var i = 0; i < files.length; i++) {
                var allowedMimes = ["image/png", "image/jpg", "image/gif", "image/jpeg"];
                if (allowedMimes.includes(files[i].type) == true) {
                    uploadFile(files[i]);
                } else {
                    console.log('Invalid file type');
                }
            }
        }
    });

    function uploadFile(file) {
        let url = '{{route('photo::api.photos.store')}}'
        let redirectUrl = '{{route('photo::photos.show',['photo'=>'@id@'])}}'
        let csrfToken = '{{csrf_token()}}';
        let formData = new FormData();
        var xhr = new XMLHttpRequest();

        formData.append('file', file)
        formData.append('_token', csrfToken)

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                var response = JSON.parse(xhr.response);
                window.location.href = redirectUrl.replace("@id@", response.data.id);
            }
        }
        xhr.open('POST', url, true)

        xhr.send(formData)

    }

    function pasteUrl(pastedData, url, caption) {
        var photoUrl = new URL(pastedData);
        var currentUrl = new URL(window.location.href);
        if (photoUrl.hostname === currentUrl.hostname) {
            return false;
        }
        var whiteListUrl = ["images.unsplash.com", "instagram.fdac13-1.fna.fbcdn.net"];
        var ext = photoUrl.pathname.split('.').pop();
        var allowedExt = ["jpg", "jpeg", "png", 'gif', 'webp'];
        if ((allowedExt.length > 0 && allowedExt.indexOf(ext) !== -1) || whiteListUrl.indexOf(photoUrl.hostname) !== -1) {
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
