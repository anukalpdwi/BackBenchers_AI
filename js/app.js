// Main application JavaScript for AI Image Generator

document.addEventListener('DOMContentLoaded', function() {
    // Set current year in footer
    document.getElementById('currentYear').textContent = new Date().getFullYear();
    
    // Elements
    const imageGeneratorForm = document.getElementById('imageGeneratorForm');
    const promptInput = document.getElementById('prompt');
    const styleSelect = document.getElementById('style');
    const imageCountSelect = document.getElementById('imageCount');
    const generateBtn = document.getElementById('generateBtn');
    const loadingContainer = document.getElementById('loadingContainer');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    const resultsContainer = document.getElementById('resultsContainer');
    const imagesContainer = document.getElementById('imagesContainer');
    const newGenerationBtn = document.getElementById('newGenerationBtn');
    
    // Event Listeners
    imageGeneratorForm.addEventListener('submit', handleImageGeneration);
    newGenerationBtn.addEventListener('click', resetForm);
    
    // Handle form submission for image generation
    function handleImageGeneration(e) {
        e.preventDefault();
        
        // Validate form
        if (!promptInput.value.trim()) {
            showError('Please enter a prompt description.');
            return;
        }
        
        // Show loading state
        showLoading();
        
        // Collect form data
        const formData = {
            prompt: promptInput.value.trim(),
            style: styleSelect.value,
            imageCount: parseInt(imageCountSelect.value)
        };
        
        // Call the API
        generateImage(formData)
            .then(response => {
                if (response.success) {
                    displayImages(response.images);
                } else {
                    showError(response.message || 'Failed to generate images.');
                }
            })
            .catch(error => {
                console.error('Error generating images:', error);
                showError('An error occurred while connecting to the server. Please try again.');
            })
            .finally(() => {
                hideLoading();
            });
    }
    
    // API Call to generate image
    async function generateImage(formData) {
        try {
            const response = await fetch('/api/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
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
            
            imageCol.innerHTML = `
                <div class="image-card">
                    <img src="${imageData.url}" alt="AI Generated Image" class="generated-image">
                    <div class="image-actions">
                        <a href="${imageData.url}" class="btn btn-sm btn-primary" download="ai-image.jpg" target="_blank">
                            <i class="fas fa-download me-1"></i>Download
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
