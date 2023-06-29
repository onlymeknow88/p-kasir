<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        #scanner-container {
            text-align: center;
        }

        #video-preview {
            /* transform: scaleX(-1); */
            width: 100%;
            height: auto;
        }
    </style>
</head>

<div id="scanner-container">
    <video id="video-preview"></video>
</div>

<form id="barcode-form" action="/barcode-scanner" method="post">
    @csrf
    <input type="hidden" name="barcode" id="barcode-input">
    <button type="submit">Submit</button>
</form>

<div class="code"></div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
<script>
    $(document).ready(function() {
        let scanner = new Instascan.Scanner({
            video: document.getElementById('video-preview'),
            mirror: false
        });

        Instascan.Camera.getCameras().then(function(cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                console.error('No cameras found.');
            }
        }).catch(function(e) {
            console.error(e);
        });

        scanner.addListener('scan', function(content) {
            $('#barcode-input').val(content);
            $('#barcode-form').submit();
            $('.code').val(content);

        });
    });
</script>
{{-- <script>
    var scanner = new Instascan.Scanner({
        video: document.getElementById('preview'),
        scanPeriod: 5,
        mirror: false
    });
    scanner.addListener('scan', function(content) {
        alert(content);
        //window.location.href=content;
    });
    Instascan.Camera.getCameras().then(function(cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]);
            $('[name="options"]').on('change', function() {
                if ($(this).val() == 1) {
                    if (cameras[0] != "") {
                        scanner.start(cameras[0]);
                    } else {
                        alert('No Front camera found!');
                    }
                } else if ($(this).val() == 2) {
                    if (cameras[1] != "") {
                        scanner.start(cameras[1]);
                    } else {
                        alert('No Back camera found!');
                    }
                }
            });
        } else {
            console.error('No cameras found.');
            alert('No cameras found.');
        }
    }).catch(function(e) {
        console.error(e);
        alert(e);
    });
</script> --}}
</body>

</html>
