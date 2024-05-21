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
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <!-- Form section -->
    <div class="form">
        <h1>Kindly Upload Your File</h1>
        <!-- Form for uploading file -->
        <form method="POST" id="uploadForm" enctype="multipart/form-data" onsubmit="submitForm(event)">
            @csrf
            <!-- CSRF token for security -->
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
            <input type="submit" value="Encrypt" id="encryptBtn" style="display: none;" onclick="submitForm(event, 'encrypt')">
            <input type="submit" value="Decrypt" id="decryptBtn" style="display: none;" onclick="submitForm(event, 'decrypt')">
        </form>

        <!-- Progress bar -->
        <div id="progress-container" style="display: none;">
            <progress id="progress-bar" value="0" max="100"></progress>
            <span id="progress-text">0%</span>
        </div>
    </div>

    <script>
        const CHUNK_SIZE = 5 * 1024 * 1024; // 5MB chunks

        // Function to display file information
        function displayFileInfo(input) {
            if (input.files && input.files[0]) {
                let file = input.files[0];
                console.log(file);
                let fileSize = file.size;
                let fileData = file.name.split('.'); // Extract extension

                document.getElementById('file-name').innerText = 'File Name: ' + fileData[0];
                document.getElementById('file-size').innerText = 'File Size: ' + (fileSize / 1024 / 1024).toFixed(2) + ' Megabytes';
                document.getElementById('file-extension').innerText = 'File Extension: .' + fileData[1];
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

        // Function to handle form submission for encryption/decryption
        async function submitForm(event, type) {
            event.preventDefault(); // Prevent default form submission
            const fileInput = document.getElementById('file');
            const file = fileInput.files[0];
            if (!file) return alert('Please select a file.');

            const isEncrypt = type === 'encrypt';
            const url = isEncrypt ? '{{ route("encrypt") }}' : '{{ route("decrypt") }}';


            if (isEncrypt) {
                showProgressBar();
                uploadFileInChunks(file, url, type);
            } else {
                uploadFileInChunks(file, url, type);
            }
        }


        // Function to show progress bar
        function showProgressBar() {
            document.getElementById('progress-container').style.display = 'block';
        }

        // Function to update progress bar
        function updateProgressBar(percentage) {
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            progressBar.value = percentage;
            progressText.innerText = `${percentage.toFixed()}%`;
            if (percentage == 100) {
                document.getElementById('progress-container').style.display = 'none';
            }
        }

        // Function to handle chunked file upload
        async function uploadFileInChunks(file, url, type) {

            const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
            let uploadedChunks = 0;


            for (let start = 0; start < file.size; start += CHUNK_SIZE) {
                const chunk = file.slice(start, start + CHUNK_SIZE);
                const formData = new FormData();
                formData.append('file', chunk);
                formData.append('fileName', file.name);
                formData.append('fileSize', file.size);
                formData.append('totalChunks', totalChunks);
                formData.append('chunkIndex', uploadedChunks);


                await fetch('{{ route("upload") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })

                uploadedChunks++;
                const percentage = (uploadedChunks / totalChunks) * 100;
                updateProgressBar(percentage);
            }
            await processFile(url, file.name, file.size)


            const processUrl = type === 'encrypt' ? '{{ route("encrypt") }}' : '{{ route("decrypt") }}';
            await (processUrl, file.name, file.size);
        }

        // Function to handle encryption/decryption process
        function processFile(url, fileName, fileSize) {
            console.log(url);
            fetch(url, {
                    method: 'POST',
                    body: JSON.stringify({
                        fileName: fileName,
                        fileSize: fileSize
                    }),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.blob())
                .then(blob => {
                    let link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = fileName;
                    link.click();
                    hideLoadingScreen();
                    hideProgressBar();
                });
            return 'success';
        }
    </script>

</body>

</html>
