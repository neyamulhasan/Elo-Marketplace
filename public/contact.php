<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';

$page_title = "Contact Us";
include 'includes/header.php';
?>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-12 text-center">
                <h1 class="display-4 mb-3 fw-bold">Contact Us</h1>
                <p class="lead text-muted">Get in touch with Bio-Organic Herbal Solutions</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Contact Information -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title text-success mb-4">Get In Touch</h4>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-geo-alt-fill text-success fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Location</h6>
                                    <p class="text-muted mb-0">Gazipur Chowrasta 1704<br>Gazipur, Bangladesh</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-whatsapp text-success fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">WhatsApp</h6>
                                    <a href="https://wa.me/8801792457594" target="_blank" class="text-decoration-none text-success">
                                        +880 1792-457594
                                    </a>
                                    <p class="text-muted small mb-0">Click to chat with us</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-facebook text-success fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Facebook Page</h6>
                                    <a href="https://www.facebook.com/Bio.Organic.Herbal.Solutions" target="_blank" class="text-decoration-none text-success">
                                        Bio.Organic.Herbal.Solutions
                                    </a>
                                    <p class="text-muted small mb-0">Follow us for updates</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-messenger text-success fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Messenger</h6>
                                    <a href="https://m.me/Bio.Organic.Herbal.Solutions" target="_blank" class="text-decoration-none text-success">
                                        Message us on Messenger
                                    </a>
                                    <p class="text-muted small mb-0">Chat with us directly</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-telephone-fill text-success fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Phone</h6>
                                    <a href="tel:+8801792457594" class="text-decoration-none text-success">
                                        +880 1792-457594
                                    </a>
                                    <p class="text-muted small mb-0">Call us anytime</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="mb-3">Follow Us</h6>
                            <div class="d-flex gap-2">
                                <a href="https://www.facebook.com/Bio.Organic.Herbal.Solutions" target="_blank" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-facebook"></i> Facebook
                                </a>
                                <a href="https://wa.me/8801792457594" target="_blank" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                </a>
                                <a href="https://m.me/Bio.Organic.Herbal.Solutions" target="_blank" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-messenger"></i> Messenger
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title text-success mb-4">Send Us a Message</h4>
                        
                        <form action="/Bio-Organic-Herbal-Solutions/public/contact-submit.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Your Name *</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Your Email *</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Subject *</label>
                                    <input type="text" class="form-control" name="subject" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Message *</label>
                                    <textarea class="form-control" name="message" rows="6" required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-send"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Contact Buttons -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-success text-white border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <h4 class="mb-3">Need Immediate Assistance?</h4>
                        <p class="mb-4">Contact us directly through WhatsApp for quick responses!</p>
                        <a href="https://wa.me/8801792457594?text=Hello%2C%20I%27m%20interested%20in%20your%20herbal%20products" 
                           target="_blank" 
                           class="btn btn-light btn-lg">
                            <i class="bi bi-whatsapp"></i> Chat on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Hours -->
        <div class="row mt-4">
            <div class="col-md-6 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <h5 class="text-success mb-3">Business Hours</h5>
                        <div class="text-start">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Saturday - Thursday:</span>
                                <strong>9:00 AM - 8:00 PM</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Friday:</span>
                                <strong>2:00 PM - 8:00 PM</strong>
                            </div>
                            <hr>
                            <p class="text-muted small mb-0 text-center">
                                <i class="bi bi-info-circle"></i> WhatsApp messages will be responded to within 24 hours
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
