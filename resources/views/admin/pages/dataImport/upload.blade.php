@extends('admin.layouts.app')

@section('main')
    <div id="uploadArea" class="upload-area mt-4">
        <div class="upload-area__header">
            <h1 class="upload-area__title">Upload your file</h1>
            <p class="upload-area__paragraph">
                File should be a CSV
                <strong class="upload-area__tooltip">
                    Like
                    <span class="upload-area__tooltip-data"></span>
                </strong>
            </p>
        </div>
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form action="{{ route('import.csv') }}" method="POST" id="uploadForm" enctype="multipart/form-data">
            @csrf
            <div id="dropZoon" class="upload-area__drop-zoon drop-zoon">
                <span class="drop-zoon__icon">
                    <i class='bx bxs-file-image'></i>
                </span>
                <p class="drop-zoon__paragraph">Drop your file here or Click to browse</p>
                <span id="loadingText" class="drop-zoon__loading-text">Please Wait</span>
                <img src="" alt="Preview Image" id="previewImage" class="drop-zoon__preview-image"
                    draggable="false">
                <input type="file" id="fileInput" name="csv_file" class="drop-zoon__file-input" accept=".csv">
            </div>

            <div id="fileDetails" class="upload-area__file-details file-details">
                <h3 class="file-details__title">Uploaded File</h3>
                <div id="uploadedFile" class="uploaded-file">
                    <div class="uploaded-file__icon-container">
                        <i class='bx bxs-file-blank uploaded-file__icon'></i>
                        <span class="uploaded-file__icon-text"></span>
                    </div>
                    <div id="uploadedFileInfo" class="uploaded-file__info">
                        <span class="uploaded-file__name">Project 1</span>
                        <span class="uploaded-file__counter">0%</span>
                    </div>
                </div>
            </div>

            <div id="uploadButtons" class="upload-area__buttons d-none">
                <button id="uploadButton" class="upload-area__button btn btn-primary m-4" type="submit"
                    disabled>Upload</button>
                <button id="resetButton" class="upload-area__button btn btn-danger m-4">Reset</button>
                <div id="errorMessages" class="error-messages">
                    <p id="errorMessage" class="error-message"></p>
                </div>
            </div>
        </form>
    </div>
@endsection


@section('css')
    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --clr-white: rgb(255, 255, 255);
            --clr-black: rgb(0, 0, 0);
            --clr-light: rgb(245, 248, 255);
            --clr-light-gray: rgb(196, 195, 196);
            --clr-blue: rgb(63, 134, 255);
            --clr-light-blue: rgb(171, 202, 255);
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--clr-light);
            color: var(--clr-black);
            font-family: 'Poppins', sans-serif;
            font-size: 1.125rem;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .upload-area {
            min-width: 90%;
            max-width: 25rem;
            background-color: var(--clr-white);
            box-shadow: 0 10px 60px rgb(218, 229, 255);
            border: 2px solid var(--clr-light-blue);
            border-radius: 24px;
            padding: 2rem 1.875rem 5rem 1.875rem;
            margin: 0.625rem;
            text-align: center;
            margin: auto;
            min-height: 80vh;
        }

        .upload-area--open {
            animation: slidDown 500ms ease-in-out;
        }

        @keyframes slidDown {
            from {
                height: 28.125rem;
                /* 450px */
            }

            to {
                height: 35rem;
                /* 560px */
            }
        }

        /* Header */
        .upload-area__header {}

        .upload-area__title {
            font-size: 1.8rem;
            font-weight: 500;
            margin-bottom: 0.3125rem;
        }

        .upload-area__paragraph {
            font-size: 0.9375rem;
            color: var(--clr-light-gray);
            margin-top: 0;
        }

        .upload-area__tooltip {
            position: relative;
            color: var(--clr-light-blue);
            cursor: pointer;
            transition: color 300ms ease-in-out;
        }

        .upload-area__tooltip:hover {
            color: var(--clr-blue);
        }

        .upload-area__tooltip-data {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -125%);
            min-width: max-content;
            background-color: var(--clr-white);
            color: var(--clr-blue);
            border: 1px solid var(--clr-light-blue);
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            opacity: 0;
            visibility: hidden;
            transition: none 300ms ease-in-out;
            transition-property: opacity, visibility;
        }

        .upload-area__tooltip:hover .upload-area__tooltip-data {
            opacity: 1;
            visibility: visible;
        }

        /* Drop Zoon */
        .upload-area__drop-zoon {
            position: relative;
            height: 11.25rem;
            /* 180px */
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            border: 2px dashed var(--clr-light-blue);
            border-radius: 15px;
            margin-top: 2.1875rem;
            cursor: pointer;
            transition: border-color 300ms ease-in-out;
        }

        .upload-area__drop-zoon:hover {
            border-color: var(--clr-blue);
        }

        .drop-zoon__icon {
            display: flex;
            font-size: 3.75rem;
            color: var(--clr-blue);
            transition: opacity 300ms ease-in-out;
        }

        .drop-zoon__paragraph {
            font-size: 0.9375rem;
            color: var(--clr-light-gray);
            margin: 0;
            margin-top: 0.625rem;
            transition: opacity 300ms ease-in-out;
        }

        .drop-zoon:hover .drop-zoon__icon,
        .drop-zoon:hover .drop-zoon__paragraph {
            opacity: 0.7;
        }

        .drop-zoon__loading-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
            color: var(--clr-light-blue);
            z-index: 10;
        }

        .drop-zoon__preview-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 0.3125rem;
            border-radius: 10px;
            display: none;
            z-index: 1000;
            transition: opacity 300ms ease-in-out;
        }

        .drop-zoon:hover .drop-zoon__preview-image {
            opacity: 0.8;
        }

        .drop-zoon__file-input {
            display: none;
        }

        .drop-zoon--over {
            border-color: var(--clr-blue);
        }

        .drop-zoon--over .drop-zoon__icon,
        .drop-zoon--over .drop-zoon__paragraph {
            opacity: 0.7;
        }

        .drop-zoon--Uploaded {}

        .drop-zoon--Uploaded .drop-zoon__icon,
        .drop-zoon--Uploaded .drop-zoon__paragraph {
            display: none;
        }

        .upload-area__file-details {
            height: 0;
            visibility: hidden;
            opacity: 0;
            text-align: left;
            transition: none 500ms ease-in-out;
            transition-property: opacity, visibility;
            transition-delay: 500ms;
        }

        .file-details--open {
            height: auto;
            visibility: visible;
            opacity: 1;
        }

        .file-details__title {
            font-size: 1.125rem;
            font-weight: 500;
            color: var(--clr-light-gray);
        }

        .uploaded-file {
            display: flex;
            align-items: center;
            padding: 0.625rem 0;
            visibility: hidden;
            opacity: 0;
            transition: none 500ms ease-in-out;
            transition-property: visibility, opacity;
        }

        .uploaded-file--open {
            visibility: visible;
            opacity: 1;
        }

        .uploaded-file__icon-container {
            position: relative;
            margin-right: 0.3125rem;
        }

        .uploaded-file__icon {
            font-size: 3.4375rem;
            color: var(--clr-blue);
        }

        .uploaded-file__icon-text {
            position: absolute;
            top: 1.5625rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.9375rem;
            font-weight: 500;
            color: var(--clr-white);
        }

        .uploaded-file__info {
            position: relative;
            top: -0.3125rem;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }

        .uploaded-file__info::before,
        .uploaded-file__info::after {
            content: '';
            position: absolute;
            bottom: -0.9375rem;
            width: 0;
            height: 0.5rem;
            background-color: #ebf2ff;
            border-radius: 0.625rem;
        }

        .uploaded-file__info::before {
            width: 100%;
        }

        .uploaded-file__info::after {
            width: 100%;
            background-color: var(--clr-blue);
        }

        .uploaded-file__info--active::after {
            animation: progressMove 800ms ease-in-out;
            animation-delay: 300ms;
        }

        @keyframes progressMove {
            from {
                width: 0%;
                background-color: transparent;
            }

            to {
                width: 100%;
                background-color: var(--clr-blue);
            }
        }

        .uploaded-file__name {
            width: 100%;
            max-width: 6.25rem;
            /* 100px */
            display: inline-block;
            font-size: 1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .uploaded-file__counter {
            font-size: 1rem;
            color: var(--clr-light-gray);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('backend') }}/assets/vendor/fonts/boxicons.css" />

    <link rel="stylesheet" href="{{ asset('backend') }}/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('backend') }}/assets/vendor/css/theme-default.css"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('backend') }}/assets/css/demo.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection


@section('script')
    <script>
        const uploadArea = document.querySelector('#uploadArea'),
            dropZoon = document.querySelector('#dropZoon'),
            loadingText = document.querySelector('#loadingText'),
            fileInput = document.querySelector('#fileInput'),
            fileDetails = document.querySelector('#fileDetails'),
            uploadedFile = document.querySelector('#uploadedFile'),
            uploadedFileInfo = document.querySelector('#uploadedFileInfo'),
            uploadedFileName = document.querySelector('.uploaded-file__name'),
            uploadedFileIconText = document.querySelector('.uploaded-file__icon-text'),
            uploadedFileCounter = document.querySelector('.uploaded-file__counter'),
            toolTipData = document.querySelector('.upload-area__tooltip-data'),
            errorMessage = document.createElement('p'),
            validTypes = ["csv"];

        errorMessage.classList.add('text-red-500');
        errorMessage.style.display = 'none';
        uploadArea.appendChild(errorMessage);

        toolTipData.innerHTML = [...validTypes].join(', .');

        dropZoon.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropZoon.classList.add('drop-zoon--over');
        });

        dropZoon.addEventListener('dragleave', () => {
            dropZoon.classList.remove('drop-zoon--over');
        });

        dropZoon.addEventListener('drop', (event) => {
            event.preventDefault();
            dropZoon.classList.remove('drop-zoon--over');
            const file = event.dataTransfer.files[0];
            fileInput.files = event.dataTransfer.files; // Update file input with the dropped file
            uploadFile(file);
        });

        dropZoon.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            uploadFile(file);
        });

        function uploadFile(file) {
            const fileReader = new FileReader(),
                fileType = file.type;

            errorMessage.style.display = 'none';

            if (fileValidate(fileType)) {
                dropZoon.classList.add('drop-zoon--Uploaded');
                loadingText.style.display = "block";
                uploadedFile.classList.remove('uploaded-file--open');
                uploadedFileInfo.classList.remove('uploaded-file__info--active');

                fileReader.addEventListener('load', () => {
                    setTimeout(() => {
                        uploadArea.classList.add('upload-area--open');
                        loadingText.style.display = "none";
                        fileDetails.classList.add('file-details--open');
                        uploadedFile.classList.add('uploaded-file--open');
                        uploadedFileInfo.classList.add('uploaded-file__info--active');
                    }, 100);
                    uploadedFileName.innerHTML = file.name;
                    progressMove();
                });

                fileReader.readAsText(file);
            }
        }

        function progressMove() {
            let counter = 0;
            setTimeout(() => {
                let interval = setInterval(() => {
                    if (counter === 100) {
                        clearInterval(interval);
                        document.getElementById('uploadButtons').classList.remove('d-none');
                        document.getElementById('uploadButton').removeAttribute('disabled');
                    } else {
                        counter += 20;
                        uploadedFileCounter.innerHTML = `${counter}%`;
                    }
                }, 100);
            }, 600);
        }

        function fileValidate(fileType) {
            const isCSV = validTypes.filter(type => fileType.indexOf(type) !== -1);
            uploadedFileIconText.innerHTML = isCSV[0] ? 'csv' : '';

            if (isCSV.length === 0) {
                errorMessage.innerHTML = 'Please upload a valid CSV file.';
                errorMessage.style.display = 'block';
                return false;
            }

            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const uploadButton = document.querySelector('#uploadButton');
            const fileInput = document.querySelector('#fileInput');
            const uploadArea = document.querySelector('#uploadArea');
            const uploadDiv = document.querySelector('#uploadDiv');
            const uploadedFile = document.querySelector('#uploadedFile');
            const uploadedFileName = document.querySelector('.uploaded-file__name');
            const uploadedFileCounter = document.querySelector('.uploaded-file__counter');
            const loadingText = document.querySelector('#loadingText');
            const errorMessages = document.querySelector('#errorMessages');

            fileInput.addEventListener('change', updateFileDetails);
            uploadButton.addEventListener('click', uploadFile);

            function updateFileDetails() {
                const file = fileInput.files[0];
                if (file) {
                    uploadedFileName.textContent = file.name;
                    uploadedFileCounter.textContent = '0%';
                    uploadedFile.classList.remove('d-none');
                    uploadButton.disabled = false;
                }
            }

            function uploadFile(event) {
                event.preventDefault();
                uploadButton.disabled = true;
                const file = fileInput.files[0];

                if (!file) {
                    showMessage('Please select a file.', 'danger');
                    uploadButton.disabled = false;
                    return;
                }

                if (file.size > 250 * 1024 * 1024) {
                    showMessage('File size exceeds the limit of 250 MB.', 'danger');
                    uploadButton.disabled = false;
                    return;
                }

                const formData = new FormData();
                formData.append('csv_file', file);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('import.csv') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 200) {
                            showMessage(data.message, 'success');
                            clearForm();
                        } else {
                            showMessage('Data verification failed.', 'danger');
                        }
                    })
                    .catch(error => {
                        showMessage('An error occurred while uploading the CSV file.', 'danger');
                        console.error('There was an error with the upload:', error);
                    })
                    .finally(() => {
                        uploadButton.disabled = false;
                    });
            }

            function showMessage(message, type) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                alertDiv.role = 'alert';
                alertDiv.innerHTML = `
            ${message}
            <button type="button" class="close btn btn-success" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true" >&times;</span>
            </button>
        `;
                uploadArea.insertAdjacentElement('afterbegin', alertDiv);
            }

            function clearForm() {
                fileInput.value = '';
                uploadedFile.classList.add('d-none');
                uploadedFileName.textContent = '';
                uploadedFileCounter.textContent = '';
                uploadButton.disabled = true;
            }
        });
    </script>
@endsection
