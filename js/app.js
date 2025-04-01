/**
 * Main JavaScript for AI Image Generator
 */





document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const generatorForm = document.getElementById('generator-form');
    const promptInput = document.getElementById('prompt-input');
    const styleSelect = document.getElementById('style-select');
    const imageCountSelect = document.getElementById('image-count');
    const generateBtn = document.getElementById('generate-btn');
    const resetBtn = document.getElementById('reset-btn');
    const loadingContainer = document.getElementById('loading-container');
    const resultsContainer = document.getElementById('results-container');
    const imagesContainer = document.getElementById('images-container');
    const errorAlert = document.getElementById('error-alert');
    const errorMessage = document.getElementById('error-message');
    
    // Initialize
    loadingContainer.classList.add('d-none');
    resultsContainer.classList.add('d-none');
    errorAlert.classList.add('d-none');
    
    // Event Listeners
    generatorForm.addEventListener('submit', handleImageGeneration);
    resetBtn.addEventListener('click', resetForm);
    
    // Handle form submission
    function handleImageGeneration(e) {
        e.preventDefault();

        
        
        // Validate form
        if (!promptInput.value.trim()) {
            showError('Please enter a prompt to generate images');
            return;
        }
        
        // Show loading state
        showLoading();

        // Prepare data, replacing 'omi' with 'momos' only in the backend request
    const formData = {
        prompt: promptInput.value.trim().replace(/\bomi\b/gi, "momos"), // Modify only in API request
        style: styleSelect.value,
        count: parseInt(imageCountSelect.value)
    };        
        
        // Prepare data
        /*const formData = {
            prompt: promptInput.value.trim(),
            style: styleSelect.value,
            count: parseInt(imageCountSelect.value)
        };*/
        
        // Call API and display results
        generateImage(formData)
            .then(response => {
                if (response.success && response.images && response.images.length > 0) {
                    displayImages(response.images);
                } else {
                    throw new Error(response.error || 'Failed to generate images');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError(error.message || 'An error occurred. Please try again.');
            })
            .finally(() => {
                hideLoading();
            });
    }
    
    // API Call to generate image
    async function generateImage(formData) {
        try {
            const response = await fetch("https://backbenchers-ai.onrender.com/api/generate.php", {
                // Updated API URL
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
    
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
    
            return await response.json();
        } catch (error) {
            console.error('API call error:', error);
            throw error;
        }
    }

    
    
    
    // Display generated images in the UI
    function displayImages(images) {
        // Clear previous results
        imagesContainer.innerHTML = '';
        
        // Hide error if visible
        hideError();
        
        // Add each image to the container
        images.forEach(imageData => {
            const imageCol = document.createElement('div');
            imageCol.className = 'col-md-6 col-lg-' + (12 / images.length > 6 ? 6 : 12 / images.length);
            
            // Get credits info
            const photographerName = imageData.credit?.name || 'Unsplash Photographer';
            const photographerLink = imageData.credit?.link || 'https://unsplash.com';
            
            imageCol.innerHTML = `
                <div class="image-card">
                    <img src="${imageData.url}" alt="Generated Image" class="generated-image">
                    <div class="image-info">
                        <small class="text-muted">
                            Photo by <a href="${photographerLink}" target="_blank" rel="noopener noreferrer">${photographerName}</a> 
                            on <a href="https://unsplash.com/?utm_source=ai_image_generator&utm_medium=referral" target="_blank">Unsplash</a>
                        </small>
                    </div>
                    <div class="image-actions">
                        <a href="${imageData.url}" class="btn btn-sm btn-primary" download target="_blank">
                            <i class="fas fa-download me-1"></i>View Full Size
                        </a>
                        <button class="btn btn-sm btn-outline-secondary copy-prompt-btn" 
                            data-prompt="${encodeURIComponent(promptInput.value)}">
                            <i class="fas fa-copy me-1"></i>Copy Prompt
                        </button>
                    </div>
                </div>
            `;
            
            imagesContainer.appendChild(imageCol);
        });
        
        
        // Add event listeners to copy prompt buttons
        document.querySelectorAll('.copy-prompt-btn').forEach(button => {
            button.addEventListener('click', function() {
                const prompt = decodeURIComponent(this.getAttribute('data-prompt'));
                navigator.clipboard.writeText(prompt)
                    .then(() => {
                        // Visual feedback for copy
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 2000);
                    })
                    .catch(err => {
                        console.error('Could not copy text: ', err);
                    });
            });
        });
        
        // Show results container
        resultsContainer.classList.remove('d-none');
        
        // Scroll to results
        resultsContainer.scrollIntoView({ behavior: 'smooth' });
    }
    
    // Reset form for a new generation
    function resetForm() {
        // Hide results
        resultsContainer.classList.add('d-none');
        // Clear form fields
        promptInput.value = '';
        styleSelect.selectedIndex = 0;
        imageCountSelect.selectedIndex = 0;
        // Focus on prompt input
        promptInput.focus();
    }
    
    // Show loading indicator
    function showLoading() {
        // Disable form
        generateBtn.disabled = true;
        generateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Generating...';
        
        // Show loading container
        loadingContainer.classList.remove('d-none');
        
        // Hide results if visible
        resultsContainer.classList.add('d-none');
        
        // Hide error if visible
        hideError();
    }
    
    // Hide loading indicator
    function hideLoading() {
        // Enable form
        generateBtn.disabled = false;
        generateBtn.innerHTML = '<i class="fas fa-magic me-2"></i>Generate';
        
        // Hide loading container
        loadingContainer.classList.add('d-none');
    }
    
    // Show error message
    function showError(message) {
        errorMessage.textContent = message;
        errorAlert.classList.remove('d-none');
        
        // Scroll to error
        errorAlert.scrollIntoView({ behavior: 'smooth' });
    }
    
    // Hide error message
    function hideError() {
        errorAlert.classList.add('d-none');
    }
});

