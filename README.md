# File Encryption and Decryption Web Application

## Overview

This project is a Laravel-based web application that allows users to securely encrypt and decrypt files using the OpenSSL library. Users can select files from their computer, view file details, perform encryption and decryption with AES-256-CBC, and save the processed files with custom names and locations.

## Features

1. **File Selection**: Users can select any file from their computer.
2. **File Details Display**: Shows the file name, size, and extension of the selected file.
3. **Encryption and Decryption**:
    - **Encryption**: Uses AES-256-CBC to encrypt the file.
    - **Decryption**: Retrieves the original file from the encrypted one.
4. **Custom File Saving**: Users can specify the name and location for saving the processed files.

## Technologies Used

- **Backend**: PHP, Laravel
- **Frontend**: HTML, CSS, JavaScript
- **Encryption Library**: OpenSSL

## Usage

1. **Upload a File**: Select a file from your computer to see its details.
2. **Encrypt/Decrypt the File**:
    - Choose either "Encrypt" or "Decrypt".
    - Provide a custom file name.
    - Click the corresponding button to process the file and download it.

## File Structure

- **app/Http/Controllers/FileController.php**: Handles file processing logic.
- **resources/views/index.blade.php**: Main interface for file upload and operations.
- **public/css/style.css**: Application styling.

## Security

- Uses CSRF protection.

## Contact

For questions or support, open an issue in this repository or contact the maintainer at kirolos.medhat.maksoud@gmail.com.
