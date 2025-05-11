<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BackBenchers AI</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.replit.com/agent/bootstrap-agent-dark-theme.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <!--Font-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">


</head>
<body>
    <!-- Header -->
    <header class="header-container mb-4 mt-2 fs-7">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand fs-4" href="#">
                    BackBenchers AI
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#team-section">Team
                            </a>
                        </li>
                        
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Hero Section -->
            <section class="hero-section">
                <h1 class="hero-title">AI Image Generator</h1>
                <p class="hero-description">
                    Generate beautiful, high-quality images with artificial intelligence. 
                    Simply enter a description and our AI will create stunning visuals based on your prompt.
                </p>
            </section>
            
            <!-- Generator Form -->
            <section>
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-8">
                        <div class="generator-card">
                            <form id="generator-form">
                                <div class="mb-3">
                                    
                                    <textarea 
                                        id="prompt-input" 
                                        class="form-control" 
                                        rows="1" 
                                        placeholder="Describe the image you want to generate..."
                                        required
                                    ></textarea>
                                    
                                
                                <div class="row">
                                    <!---->
                                    <div class="col-md-6">
                                        <label for="style-select" class="form-label"></label>
                                        <select id="style-select" class="form-select">
                                            <option value="photo">Photographic</option>
                                            <option value="digital-art">Digital Art</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="image-count" class="form-label text-center"></label>
                                        <select id="image-count" class="form-select">
                                            <option value="1">1 image</option>
                                            <option value="2">2 images</option>
                                            <option value="4">4 images</option>
                                        </select>
                                    </div>
                                    
                                </div>
                                
                                </div>
                                
                                <div class="alert alert-danger d-none" role="alert" id="error-alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <span id="error-message">Error message goes here</span>
                                </div>
                                
                                <div class="d-grid gap-4 d-md-flex btn-lg justify-content-md-center mt-4">
                                    <button type="submit" id="generate-btn" class="btn btn-primary btn-lg">
                                        <i class="fas fa-magic me-2"></i>Generate
                                    </button>

                                    <button type="button" id="reset-btn" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo me-1"></i>Reset
                                    </button>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Loading indicator -->
            <section id="loading-container" class="loading-container d-none">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="loading-text mt-3 pulse-animation">Generating your images...</p>
                </div>
            </section>
            
            <!-- Results Section -->
            <section id="results-container" class="results-container d-none">
                <h2 class="text-center mb-4">Generated Images</h2>
                <div id="images-container" class="row"></div>
            </section>
            
            <!-- About Section -->
            <section id="about" class="mt-5 pt-5">
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-8">
                        <div class="generator-card">
                            <h2 class="mb-4">About BackBenchers.AI</h2>
<p>
    BackBenchers.AI is a <strong>B.Sc. (CS) 2nd Year Student Project</strong> from 
    <strong>Pt. Sambhunath Shukla University</strong>, developed under the guidance of the 
    <strong>Department of Computer Science</strong> at PTSNS University.
</p>
<p>
    This project is an <strong>AI-powered image generator</strong> that uses artificial intelligence 
    to transform text descriptions into visually stunning images. It allows users to generate 
    images in various styles, including <em>photographic, digital art, anime, oil paintings,</em> and more. 
    Leveraging high-quality image resources from Unsplash, BackBenchers.AI ensures beautiful and creative visuals 
    that match users' descriptions.
</p>


                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Our Team -->
    <section class="team-section" id="team-section">
        <h2 class="mb-4 text-center">Our Team</h2>
        <div class="team-container">
            <div class="team-member">
                <img src="Our Team/Anukalp.png" alt="Anukalp Dwivedi">
                <h4>Anukalp Dwivedi</h4>
                <a href="https://www.linkedin.com/in/anukalp-dwivedi/" target="_blank">
                    <i class="fab fa-linkedin"></i></a>
            </div>
            <div class="team-member">
                <img src="Our Team/Nikhil.jpg" alt="Nikhil Sharma">
                <h4>Nikhil Sharma</h4>
                <a href="https://www.linkedin.com/in/nikhil-sharma-4117442a4 " target="_blank"><i class="fab fa-linkedin"></i></a>
            </div>
            <div class="team-member">
                <img src="Our Team/Ashutosh.jpg" alt="Ashutosh Lariya">
                <h4>Ashutosh Lariya</h4>
                <a href="#" target="_blank"><i class="fab fa-linkedin"></i></a>
            </div>
            <div class="team-member">
                <img src="Our Team/Chitranshu.jpg" alt="Chitranshu Shrivastav">
                <h4>Chitranshu Shrivastav</h4>
                <a href="#" target="_blank"><i class="fab fa-linkedin"></i></a>
            </div>
            <div class="team-member">
                <img src="Our Team/Sajeb.png" alt="Sajeb Jilani">
                <h4>Sajeb Jilani</h4>
                <a href="#" target="_blank"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </section>
    
    
    <!-- Footer -->
    <footer class="mt-auto">
        <div class="container">
            <p>
                &copy; 2025 BackBenchers Ai. All rights reserved.
                <a href="https://www.linkedin.com/in/anukalp-dwivedi/" target="_blank">Anukalp Dwivedi</a>.
            </p>
            <p>
                <small>
                    This application uses the Unsplash API but is not endorsed or certified by Unsplash.
                    All rights to the respective owners.
                </small>
            </p>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="js/app.js"></script>
</body>
</html>