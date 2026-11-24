const imageInput = document.getElementById('image');
const imagePreview = document.getElementById('image-preview');
const imagePlaceholder = document.getElementById('image-placeholder');
const currentImage = imagePreview.getAttribute('src');
let previewUrl;

function showPreview(url, message = 'No image selected') {
    if (url) {
        imagePreview.src = url;
    } else {
        imagePreview.removeAttribute('src');
    }

    imagePreview.hidden = !url;
    imagePlaceholder.hidden = Boolean(url);
    imagePlaceholder.textContent = message;
}

imageInput.addEventListener('change', async () => {
    if (previewUrl) URL.revokeObjectURL(previewUrl);
    previewUrl = undefined;

    const file = imageInput.files[0];
    showPreview(file ? null : currentImage);
    if (!file) return;

    // Only preview supported files in the browser. Laravel still validates the upload.
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024) {
        showPreview(null, 'Preview unavailable');
        return;
    }

    const url = URL.createObjectURL(file);
    previewUrl = url;
    const image = new Image();
    image.src = url;

    try {
        await image.decode();
        if (previewUrl === url) showPreview(url);
    } catch {
        if (previewUrl === url) showPreview(null, 'Preview unavailable');
    }
});
