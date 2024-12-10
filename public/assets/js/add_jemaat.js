document.getElementById('pass_photo').addEventListener('change', function(event) {
    const maxFiles = 5; 
    const maxImageSize = 1 * 1024 * 1024; 
    const maxVideoSize = 10 * 1024 * 1024;
    const allowedImageFormats = ['image/jpeg', 'image/png', 'image/jpg']; 
    const allowedVideoFormats = ['video/mp4', 'video/avi', 'video/mov']; 
    const fileInput = event.target;
    const errorMessage = document.getElementById('error_message_media');
    const fileList = document.getElementById('file_list');
    let errorMessages = [];
    let validFiles = [];
    fileList.innerHTML = ''; 
    errorMessage.textContent = ''; 
    if (fileInput.files.length > maxFiles) {
        fileInput.value = '';
        errorMessages.push(`Anda hanya dapat mengunggah maksimal ${maxFiles} file.`);
    } else {
        Array.from(fileInput.files).forEach(file => {
            if (allowedImageFormats.includes(file.type) && file.size > maxImageSize) {
                errorMessages.push(`Gambar "${file.name}" melebihi ukuran maksimal 1 MB.`);
            } else if (allowedVideoFormats.includes(file.type) && file.size > maxVideoSize) {
                errorMessages.push(`Video "${file.name}" melebihi ukuran maksimal 10 MB.`);
            } else if (![...allowedImageFormats, ...allowedVideoFormats].includes(file.type)) {
                errorMessages.push(`File "${file.name}" memiliki format yang tidak diizinkan.`);
            } else {
                validFiles.push(file);
            }
        });
        validFiles.forEach(file => {
            const li = document.createElement('li');
            li.textContent = `✔ ${file.name}`;
            fileList.appendChild(li);
        });
    }
   
    if (errorMessages.length > 0) {
        errorMessage.textContent = errorMessages.join(' ');
        fileInput.value = ''; 
    }
});