$(document).ready(function() {
    // alert(filepicker_server_url)
    jwdfilepicker.setDefaults({
        server_url: filepicker_server_url, // filepicker_upload_path ada di header.php
        icon_url: filepicker_icon_url
    });
});
