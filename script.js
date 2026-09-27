// script.js - Client-Side Scripts for CampusFind
document.addEventListener('DOMContentLoaded', function () {
    // Bootstrap form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Real-time image upload preview
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            let previewContainer = document.getElementById('image-preview-container');
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.id = 'image-preview-container';
                previewContainer.className = 'mt-3 p-2 bg-light rounded border text-center';
                imageInput.parentNode.appendChild(previewContainer);
            }

            if (file) {
                // Check 5MB limit on client
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB limit. Please choose a smaller image.');
                    imageInput.value = '';
                    previewContainer.innerHTML = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    previewContainer.innerHTML = `
                        <div class="small text-muted mb-1">Image Preview:</div>
                        <img src="${event.target.result}" alt="Preview" class="img-thumbnail shadow-sm" style="max-height: 160px; object-fit: contain;">
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.innerHTML = '';
            }
        });
    }

    // Auto dismiss flash alerts after 6 seconds
    const autoAlerts = document.querySelectorAll('.alert-dismissible');
    autoAlerts.forEach(alertEl => {
        setTimeout(() => {
            try {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
                bsAlert.close();
            } catch (e) {}
        }, 6000);
    });
});
