<?php
session_start(); // Mulai session
include 'database.php'; // Koneksi ke database jika diperlukan

// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: socialize.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Socialize</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: white;
            color: black;
        }
        .container {
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .logo {
            font-size: 28px;
            font-weight: 600;
            color: #000;
        }
        .chat-icon {
            width: 120px;
            height: auto;
            margin: 20px 0;
        }
        footer {
            margin-top: 40px;
            color: #555;
        }
    </style>
</head>

<body>
    <main class="container text-center">
        <h1 class="logo">Socialize</h1>
        <img src="gambar/sosial.png" alt="Chat Icon" class="chat-icon" />
        <h2 class="fw-bold mt-3">Stay Connected, Stay Social.</h2>
        <p class="lead">Join the Conversation – Create Your Account Today!</p>

        <button class="btn btn-primary btn-lg mb-3" data-bs-toggle="modal" data-bs-target="#signUpModal">Create an account</button>
        <p>Already have an account?</p>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#signInModal">Sign In</button>
    </main>

    <!-- Modal Sign Up -->
    <div class="modal fade" id="signUpModal" tabindex="-1" aria-labelledby="signUpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-3">
                        <img src="gambar/sosial.png" alt="Icon" width="40" class="mb-2" />
                        <h4 class="fw-bold">Sign Up</h4>
                        <p class="text-muted small">Enter your name, email, and password and start creating.</p>
                    </div>
                    <form action="signup.php" method="POST" novalidate>
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Name" required />
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required />
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required />
                        </div>
                        <div class="mb-3">
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required />
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required />
                            <label class="form-check-label small" for="terms">I agree to the terms and conditions</label>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">Sign Up</button>
                        </div>
                        <div class="text-center small">
                            Already have an Account? <a href="#" data-bs-toggle="modal" data-bs-target="#signInModal" data-bs-dismiss="modal">Sign in</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sign In -->
    <div class="modal fade" id="signInModal" tabindex="-1" aria-labelledby="signInModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="text-center">
                        <img src="gambar/sosial.png" alt="Icon" width="40" class="mb-2" />
                        <h4 class="fw-bold mb-4">Sign In</h4>
                    </div>
                    <form action="login.php" method="POST" novalidate>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required />
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required />
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe" />
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                            <a href="#" class="text-decoration-none small text-primary">Forgot Password?</a>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">Sign In</button>
                        </div>
                        <div class="text-center small">
                            Don’t have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#signUpModal" data-bs-dismiss="modal">Sign up</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5 mb-4">
        <div class="footer-links">
            <a href="#">About</a>
            <a href="#">Help Center</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <span>© 2025 Socialize</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
