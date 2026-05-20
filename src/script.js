$(document).ready(function() {
    // Mobile menu toggle
    $('#mobileMenuBtn').click(function() {
        $('#navMenu').toggleClass('active');
    });

    // Add to cart functionality
    $('.add-to-cart').click(function() {
        const productId = $(this).data('id');
        $.ajax({
            url: 'user/add-to-cart.php',
            method: 'POST',
            data: { product_id: productId, quantity: 1 },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    $('.cart-count').text(data.cart_count);
                    showToast('Product added to cart!', 'success');
                } else {
                    showToast(data.message || 'Please login to add items', 'error');
                }
            }
        });
    });

    // Newsletter subscription
    $('#newsletterForm').submit(function(e) {
        e.preventDefault();
        const email = $(this).find('input').val();
        $.ajax({
            url: 'subscribe.php',
            method: 'POST',
            data: { email: email },
            success: function(response) {
                showToast('Subscribed successfully!', 'success');
                $('#newsletterForm')[0].reset();
            }
        });
    });

    // Prescription upload
    $('#uploadArea').click(function() {
        $('#prescriptionFile').click();
    });

    $('#prescriptionFile').change(function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#uploadArea').html('<img src="' + e.target.result + '" style="max-width: 100%; max-height: 150px;">');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    $('#prescriptionForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: 'upload-prescription.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    showToast('Prescription uploaded successfully!', 'success');
                    closePrescriptionModal();
                    $('#prescriptionForm')[0].reset();
                    $('#uploadArea').html('<i class="fas fa-cloud-upload-alt"></i><p>Drag & drop prescription image here</p><p class="upload-hint">or click to browse</p>');
                }
            }
        });
    });
});

// Toast notification
function showToast(message, type) {
    const toast = $('<div class="toast"></div>');
    toast.text(message);
    if (type === 'success') {
        toast.css('background', '#10b981');
    } else if (type === 'error') {
        toast.css('background', '#ef4444');
    }
    toast.css({
        position: 'fixed',
        bottom: '20px',
        right: '20px',
        color: 'white',
        padding: '12px 24px',
        borderRadius: '8px',
        zIndex: 1000,
        animation: 'fadeInOut 3s ease'
    });
    $('body').append(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Modal functions
function openPrescriptionModal() {
    $('#prescriptionModal').addClass('active');
}

function closePrescriptionModal() {
    $('#prescriptionModal').removeClass('active');
}

// Close modal on outside click
$(window).click(function(e) {
    if ($(e.target).hasClass('modal')) {
        closePrescriptionModal();
    }
});