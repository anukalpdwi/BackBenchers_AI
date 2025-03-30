import os
import json
import requests
from flask import Flask, render_template, request, jsonify

app = Flask(__name__, static_folder='.', static_url_path='')

# Get API key from environment variable
STARRYAI_API_KEY = os.environ.get('STARRYAI_API_KEY')

@app.route('/')
def index():
    """Serve the main index.html page"""
    return app.send_static_file('index.html')

@app.route('/api/generate', methods=['POST'])
def generate_image():
    """Handle image generation requests"""
    try:
        # Get request data
        data = request.get_json()
        
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
            
        # Map our style options to what StarryAI expects
        style_mapping = {
            'realistic': 'photographic',
            'anime': 'anime',
            'cartoon': 'cartoon',
            'painting': 'oil-painting',
            'sketch': 'pencil-sketch'
        }
        
        # Default to photographic if style not in mapping
        starry_ai_style = style_mapping.get(style, 'photographic')
        
        # Check if API key is available
        if not STARRYAI_API_KEY:
            return jsonify({
                'success': False,
                'message': 'StarryAI API key is not configured'
            }), 500
            
        # Prepare request data for StarryAI
        post_data = {
            'prompt': prompt,
            'style': starry_ai_style,
            'num_images': image_count,
            'width': 512,
            'height': 512
        }
        
        # Make request to StarryAI API
        response = requests.post(
            'https://api.starryai.com/v1/images/generate',
            json=post_data,
            headers={
                'Content-Type': 'application/json',
                'Authorization': f'Bearer {STARRYAI_API_KEY}'
            },
            timeout=30
        )
        
        # Check response status
        if response.status_code != 200:
            error_data = response.json()
            error_message = error_data.get('error', {}).get('message', f'API request failed with status code {response.status_code}')
            return jsonify({
                'success': False,
                'message': error_message
            }), 500
            
        # Parse response data
        response_data = response.json()
        
        # Process and return image data
        images = []
        for image in response_data.get('images', []):
            images.append({
                'id': image.get('id', f'img_{os.urandom(4).hex()}'),
                'url': image.get('url'),
                'prompt': prompt
            })
            
        return jsonify({
            'success': True,
            'images': images,
            'message': 'Images generated successfully'
        })
            
    except requests.exceptions.RequestException as e:
        return jsonify({
            'success': False,
            'message': f'Error connecting to StarryAI API: {str(e)}'
        }), 500
    except Exception as e:
        return jsonify({
            'success': False,
            'message': f'An unexpected error occurred: {str(e)}'
        }), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)