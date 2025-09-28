// ✅ CORRECT API URL - Point to the PHP file you just created
const API_URL = 'http://localhost/lost-and-found/api/submit.php';
console.log('API URL set to:', API_URL);

// Debug: Check if the URL is correct
console.log('API URL:', API_URL);

// Your existing code continues below...
document.addEventListener('DOMContentLoaded', function(){
// Debugging check - remove this in production
console.log("JavaScript file is properly linked!");

document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM content has loaded");
    
    // Navigation elements
    const navLinks = {
        home: document.getElementById('home-link'),
        report: document.getElementById('report-link'),
        browse: document.getElementById('browse-link'),
        about: document.getElementById('about-link')
    };
    
    // Page sections
    const sections = {
        home: document.getElementById('home-section'),
        report: document.getElementById('report-section'),
        browse: document.getElementById('browse-section'),
        about: document.getElementById('about-section')
    };
    
    // Show specific section and hide others
    function showSection(sectionId) {
        // First validate the section exists
        if (!sections[sectionId]) {
            console.error(`Section ${sectionId} not found!`);
            return;
        }
        
        // Hide all sections
        Object.values(sections).forEach(section => {
            if (section) section.classList.add('hidden');
        });
        
        // Show requested section
        sections[sectionId].classList.remove('hidden');
        
        // Update active nav link
        Object.values(navLinks).forEach(link => {
            link?.classList.remove('active');
        });
        navLinks[sectionId]?.classList.add('active');
    }
    
    // Setup navigation event listeners
    function setupNavigation() {
        Object.entries(navLinks).forEach(([sectionId, link]) => {
            if (!link) {
                console.error(`Navigation link for ${sectionId} not found!`);
                return;
            }
            
            link.addEventListener('click', function(e) {
                e.preventDefault();
                showSection(sectionId);
                
                // Update URL hash
                window.location.hash = sectionId;
            });
        });
        
        // Handle initial load with hash
        const initialSection = window.location.hash.substring(1) || 'home';
        showSection(initialSection);
    }
    
    // Initialize the application
    function init() {
        setupNavigation();
        
        // Set today's date as default in report form
        const dateField = document.getElementById('item-date');
        if (dateField) {
            dateField.valueAsDate = new Date();
        } else {
            console.warn('Date field not found');
        }
        
        // Initialize other components here...
    }
    
    // Start the application
    init();
});

const API_URL = 'http://localhost/lost-and-found/api/submit.php';

// Fetch items from PHP backend
async function fetchItems(filters = {}) {
    const query = new URLSearchParams(filters).toString();
    const response = await fetch(`${API_URL}?${query}`);
    return await response.json();
}

// Submit new item to PHP backend
async function submitItem(formData) {
    const response = await fetch(API_URL, {
        method: 'POST',
        body: formData
    });
    return await response.json();
}

// Example usage in form submission
document.getElementById('item-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('type', document.getElementById('item-type').value);
    formData.append('status', document.querySelector('input[name="status"]:checked').value);
    formData.append('title', document.getElementById('item-title').value);
    formData.append('description', document.getElementById('item-details').value);
    formData.append('location', document.getElementById('item-location').value);
    formData.append('date', document.getElementById('item-date').value);
    formData.append('contact', document.getElementById('contact-info').value);
    formData.append('image', document.getElementById('item-image').files[0]);

    try {
        await submitItem(formData);
        alert('Item reported successfully!');
        const items = await fetchItems();
        renderItems(items);
    } catch (error) {
        alert('Error: ' + error.message);
    }
});
async function fetchItems(filters = {}) {
    try {
        const query = new URLSearchParams(filters).toString();
        const response = await fetch(`${API_URL}?${query}`);
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            throw new Error(`Expected JSON, got: ${text.substring(0, 100)}...`);
        }

        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return []; // Return empty array as fallback
    }
}

async function submitItem(formData) {
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            body: formData
        });

        // Handle non-JSON responses
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(errorText || 'Server error');
        }

        return await response.json();
    } catch (error) {
        console.error('Submission error:', error);
        throw error; // Re-throw for form handling
    }
}});

