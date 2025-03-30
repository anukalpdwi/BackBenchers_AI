import os
import json
import logging
import requests
from flask import Flask, render_template, request, jsonify
import time
import random

# Configure logging
logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger(__name__)

app = Flask(__name__, static_folder='.', static_url_path='')

# Get API keys from environment variables
UNSPLASH_ACCESS_KEY = os.environ.get('UNSPLASH_ACCESS_KEY')

@app.route('/')
def index():
    """Serve the main index.html page"""
    return app.send_static_file('index.html')

@app.route('/api/generate', methods=['POST'])
def generate_image():
    """Handle image generation requests using Unsplash API"""
    try:
        # Log that we received a request
        logger.info("Received image generation request")
        
        # Get request data
        data = request.get_json()
        logger.debug(f"Request data: {data}")
        
        # Validate input
        if not data or 'prompt' not in data or not data['prompt'].strip():
            return jsonify({
                'success': False,
                'message': 'Prompt cannot be empty'
            }), 400
        
        # Extract parameters
        prompt = data['prompt']
        style = data.get('style', 'realistic')
        image_count = data.get('imageCount', 1)
        
        # Ensure image count is valid (1, 2, or 4)
        if image_count not in [1, 2, 4]:
            image_count = 1
        
        # Check if Unsplash API key is available
        if not UNSPLASH_ACCESS_KEY:
            logger.error("Unsplash API key is not configured")
            return jsonify({
                'success': False,
                'message': 'Unsplash API key is not configured'
            }), 500
            
        # Log API key status (without exposing the key)
        logger.info(f"Unsplash API Key available: {bool(UNSPLASH_ACCESS_KEY)}")
        
        # Modify the search query based on the style
        search_query = prompt
        if style != 'realistic':
            search_query = f"{prompt} {style} style"
            
        # Prepare Unsplash API request
        unsplash_url = "https://api.unsplash.com/search/photos"
        params = {
            'query': search_query,
            'per_page': image_count,
            'orientation': 'squarish',  # Try to get square-ish images similar to AI generated ones
            'content_filter': 'high'    # Filter for high-quality content
        }
        
        headers = {
            'Authorization': f'Client-ID {UNSPLASH_ACCESS_KEY}'
        }
        
        # Make request to Unsplash API
        logger.info(f"Making Unsplash API request for query: '{search_query}'")
        response = requests.get(
            unsplash_url,
            params=params,
            headers=headers,
            timeout=30
        )
        
        # Check response status
        if response.status_code != 200:
            logger.error(f"Unsplash API request failed with status code {response.status_code}")
            try:
                error_data = response.json()
                error_message = error_data.get('errors', ['Error connecting to Unsplash API'])[0]
            except:
                error_message = f"API request failed with status code {response.status_code}"
                
            return jsonify({
                'success': False,
                'message': error_message
            }), 500
            
        # Parse response data
        response_data = response.json()
        
        # Process and return image data
        images = []
        results = response_data.get('results', [])
        
        if not results:
            logger.warning(f"No images found for query: '{search_query}'")
            return jsonify({
                'success': False,
                'message': f"No images found for '{prompt}'. Please try a different prompt."
            }), 404
            
        logger.info(f"Found {len(results)} images for query: '{search_query}'")
        
        for image in results:
            # Get the regular sized image (medium size)
            image_url = image.get('urls', {}).get('regular')
            if image_url:
                img_id = image.get('id', f'img_{os.urandom(4).hex()}')
                images.append({
                    'id': img_id,
                    'url': image_url,
                    'prompt': prompt,
                    'credit': {
                        'name': image.get('user', {}).get('name', 'Unsplash Photographer'),
                        'link': image.get('user', {}).get('links', {}).get('html', 'https://unsplash.com')
                    }
                })
        
        # Log successful response
        logger.info(f"Successfully retrieved {len(images)} images from Unsplash")
            
        return jsonify({
            'success': True,
            'images': images,
            'message': 'Images retrieved successfully from Unsplash'
        })
            
    except requests.exceptions.RequestException as e:
        logger.error(f"Request exception: {e}")
        return jsonify({
            'success': False,
            'message': f'Error connecting to Unsplash API: {str(e)}'
        }), 500
    except Exception as e:
        logger.error(f"Unexpected error: {e}", exc_info=True)
        return jsonify({
            'success': False,
            'message': f'An unexpected error occurred: {str(e)}'
        }), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)