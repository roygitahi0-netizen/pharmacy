    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-prescription-bottle-alt"></i>
                        <span>PharmaCare</span>
                    </div>
                    <p>Your trusted partner in healthcare. We provide quality medicines and healthcare products at affordable prices.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Shop</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Customer Service</h4>
                    <ul>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Shipping Policy</a></li>
                        <li><a href="#">Return Policy</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contact Info</h4>
                    <ul class="contact-info">
                        <li><i class="fas fa-phone"></i> +1 234 567 890</li>
                        <li><i class="fas fa-envelope"></i> info@pharmacare.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Health Street, Medical City</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> PharmaCare. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Prescription Modal -->
    <div class="modal" id="prescriptionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Upload Prescription</h3>
                <button class="modal-close" onclick="closePrescriptionModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="prescriptionForm" enctype="multipart/form-data">
                    <div class="upload-area" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Drag & drop prescription image here</p>
                        <p class="upload-hint">or click to browse</p>
                        <input type="file" name="prescription" id="prescriptionFile" accept="image/*" style="display: none">
                    </div>
                    <div class="form-group">
                        <label>Doctor's Name (Optional)</label>
                        <input type="text" name="doctor_name" class="form-input" placeholder="Enter doctor's name">
                    </div>
                    <div class="form-group">
                        <label>Additional Notes</label>
                        <textarea name="notes" class="form-textarea" rows="3" placeholder="Any special instructions..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit Prescription</button>
                </form>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>