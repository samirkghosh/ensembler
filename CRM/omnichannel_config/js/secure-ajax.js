// Secure AJAX utility functions
const SecureAjax = {
    // Get CSRF token from meta tag
    getCsrfToken: function() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    },

    // Validate input data
    validateInput: function(data) {
        if (typeof data !== 'object') return false;
        
        // Sanitize string values
        for (let key in data) {
            if (typeof data[key] === 'string') {
                data[key] = this.sanitizeString(data[key]);
            }
        }
        return data;
    },

    // Sanitize string input
    sanitizeString: function(str) {
        return str.replace(/[<>]/g, ''); // Basic XSS prevention
    },

    // Secure AJAX GET request
    get: function(url, data = {}, successCallback, errorCallback) {
        this.request('GET', url, data, successCallback, errorCallback);
    },

    // Secure AJAX POST request
    post: function(url, data = {}, successCallback, errorCallback) {
        this.request('POST', url, data, successCallback, errorCallback);
    },

    // Main request handler
    request: function(method, url, data, successCallback, errorCallback) {
        // Add CSRF token
        data.csrf_token = this.getCsrfToken();

        // Validate input data
        data = this.validateInput(data);
        if (!data) {
            errorCallback('Invalid input data');
            return;
        }

        // Configure request
        const config = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': this.getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        };

        // Add body for POST requests
        if (method === 'POST') {
            config.body = JSON.stringify(data);
        } else {
            // Add query parameters for GET requests
            const params = new URLSearchParams(data);
            url += '?' + params.toString();
        }

        // Make request
        fetch(url, config)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    errorCallback(data.error);
                } else {
                    successCallback(data);
                }
            })
            .catch(error => {
                console.error('Request failed:', error);
                errorCallback(error.message);
            });
    },

    // File upload with progress
    uploadFile: function(url, file, progressCallback, successCallback, errorCallback) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('csrf_token', this.getCsrfToken());

        const xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressCallback(percentComplete);
            }
        });

        xhr.addEventListener('load', () => {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    successCallback(response);
                } catch (e) {
                    errorCallback('Invalid server response');
                }
            } else {
                errorCallback(`Upload failed with status ${xhr.status}`);
            }
        });

        xhr.addEventListener('error', () => {
            errorCallback('Network error occurred');
        });

        xhr.open('POST', url);
        xhr.send(formData);
    }
};

// Export for use in other files
window.SecureAjax = SecureAjax; 