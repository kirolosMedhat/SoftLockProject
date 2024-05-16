<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Title -->
    <title>SoftLock</title>
    <!-- Link to external stylesheet -->
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
</head>

<body>
    <!-- Form section -->
    <div class="form">
        <h1>Kindly Upload Your File</h1>
        <!-- Form for uploading file -->
        <form action="{{ route('process') }}" method="POST" enctype="multipart/form-data" id="myForm">
            @csrf <!-- CSRF token for security -->
            <!-- File input field -->
            <input type="file" name="file" id="file" onchange="displayFileInfo(this)">
            <!-- Radio buttons for encryption/decryption choice -->
            <div class="radioChoice">
                <!-- Encryption option -->
                <div class="enc">
                    <label for="enc">Encryption</label>
                    <input type="radio" id="enc" name="type" value="enc" onchange="toggleSubmitButtons()">
                </div>
                <!-- Decryption option -->
                <div class="dec">
                    <label for="dec">Decryption</label>
                    <input type="radio" id="dec" name="type" value="dec" onchange="toggleSubmitButtons()">
                </div>
            </div>
            <!-- Display file information -->
            <span id='file-name'></span>
            <span id='file-size'></span>
            <span id='file-extension'></span>
            <!-- Submit buttons for encryption/decryption -->
            <input type="submit" value="Encrypt" id="encryptBtn" style="display: none;">
            <input type="submit" value="Decrypt" id="decryptBtn" style="display: none;">
        </form>
    </div>

</body>

<script>
    // Function to display file information
    function displayFileInfo(input) {
        if (input.files && input.files[0]) {
            let file = input.files[0];

            let fileSize = file.size;
            let fileData = file.name.split('.') // Extract extension

            document.getElementById('file-name').innerText = 'File Name: ' + fileData[0];
            document.getElementById('file-size').innerText = 'File Size: ' + fileSize + ' bytes';
            document.getElementById('file-extension').innerText = 'File Extension: .' + fileData[1];

            let myForm = document.getElementById('file');

        }
    }

    // Function to toggle submit buttons based on radio button selection
    function toggleSubmitButtons() {
        let encryptionRadio = document.getElementById('enc');
        let encryptBtn = document.getElementById('encryptBtn');
        let decryptBtn = document.getElementById('decryptBtn');

        if (encryptionRadio.checked) {
            encryptBtn.style.display = 'inline-block';
            decryptBtn.style.display = 'none';
        } else {
            encryptBtn.style.display = 'none';
            decryptBtn.style.display = 'inline-block';
        }
    }
</script>

</html>