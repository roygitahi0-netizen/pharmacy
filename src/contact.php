<?php require_once 'includes/header.php'; ?>
<main>
    <div class="container" style="padding: 4rem 0;">
        <h1>Contact Us</h1>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
            <div>
                <h3>Get in Touch</h3>
                <p><i class="fas fa-phone"></i> +1 234 567 890</p>
                <p><i class="fas fa-envelope"></i> info@pharmacare.com</p>
                <p><i class="fas fa-map-marker-alt"></i> 123 Health Street, Medical City</p>
            </div>
            <div>
                <form>
                    <input type="text" placeholder="Your Name" style="width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                    <input type="email" placeholder="Your Email" style="width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                    <textarea placeholder="Your Message" rows="5" style="width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;"></textarea>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</main>
<?php require_once 'includes/footer.php'; ?>