// API Key
const apiKey = 'AIzaSyB7kCaV6jLUib5S8TehmkrqdqVVFD_T1tM'; // Replace with your actual API key
const searchInput = document.getElementById('search-input');
const searchButton = document.getElementById('search-button');
const booksGrid = document.querySelector('.books-grid');
const paginationContainer = document.querySelector('.pagination');

// Pagination elements
let currentPage = 1;
const resultsPerPage = 10;
let totalPages = 1;
let currentQuery = '';

// Bookmarks system
let bookmarks = JSON.parse(localStorage.getItem('bookmarks') || '[]');

// Function to fetch books from the Google Books API
async function fetchBooks(query, page = 1) {
    currentQuery = query;
    currentPage = page;
    
    // Show loading state
    booksGrid.innerHTML = '<div class="loading">Loading books...</div>';
    
    const startIndex = (page - 1) * resultsPerPage;
    const apiUrl = `https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&key=${apiKey}&startIndex=${startIndex}&maxResults=${resultsPerPage}&langRestrict=en`;

    try {
        const response = await fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error.message || 'API Error');
        }

        if (data.items && data.items.length > 0) {
            // Calculate total pages
            totalPages = Math.ceil(data.totalItems / resultsPerPage);
            totalPages = Math.min(totalPages, 3); // Limit to 3 pages as per UI
            
            // Update pagination UI
            updatePaginationUI();
            
            // Display books
            displayBooks(data.items);
        } else {
            booksGrid.innerHTML = '<div class="no-results"><i class="fas fa-search"></i><p>No books found for your search. Try a different search term.</p></div>';
            paginationContainer.style.display = 'none';
        }
    } catch (error) {
        console.error('Error fetching books:', error);
        let errorMessage = 'Error fetching books. ';
        
        if (error.message.includes('API Error')) {
            errorMessage += 'There was an issue with the Google Books API. Please try again later.';
        } else if (error.message.includes('HTTP error! status: 429')) {
            errorMessage += 'Too many requests. Please wait a moment and try again.';
        } else if (error.message.includes('HTTP error! status: 403')) {
            errorMessage += 'Access denied. Please check your API key.';
        } else {
            errorMessage += 'Please check your internet connection and try again.';
        }
        
        booksGrid.innerHTML = `<div class="error-message"><i class="fas fa-exclamation-circle"></i><p>${errorMessage}</p><button class="retry-btn" onclick="fetchBooks('${currentQuery}', ${currentPage})"><i class="fas fa-redo"></i> Try Again</button></div>`;
        paginationContainer.style.display = 'none';
    }
}

// Function to update pagination UI
function updatePaginationUI() {
    paginationContainer.style.display = 'flex';
    const pageButtons = paginationContainer.querySelectorAll('.pagination-btn');
    
    // Update active state
    pageButtons.forEach(btn => {
        const pageNum = parseInt(btn.textContent);
        btn.classList.toggle('active', pageNum === currentPage);
        btn.disabled = pageNum > totalPages;
    });
}

// Function to handle pagination button clicks
function handlePaginationClick(pageNum) {
    if (pageNum !== currentPage && pageNum <= totalPages) {
        fetchBooks(currentQuery, pageNum);
    }
}

// Add event listeners for pagination buttons
document.querySelectorAll('.pagination-btn').forEach(button => {
    button.addEventListener('click', function() {
        const pageNum = parseInt(this.textContent);
        handlePaginationClick(pageNum);
    });
});

// Function to handle save button click
function handleSaveClick(event) {
    event.preventDefault();
    alert('Please login to save books. Redirecting to login page...');
    window.location.href = 'login.php';
}

// Function to handle read more button click
function handleReadMoreClick(event) {
    event.preventDefault();
    alert('Please login to access book details. Redirecting to login page...');
    window.location.href = 'login.php';
}

// Function to display books in the grid with enhanced 3D book card design
function displayBooks(books) {
    booksGrid.innerHTML = ''; // Clear previous results

    books.forEach(book => {
        const bookCard = document.createElement('div');
        bookCard.className = 'book-card';
        
        // Get book info
        const volumeInfo = book.volumeInfo;
        // Use medium or large image if available, fallback to thumbnail
        const imageLinks = volumeInfo.imageLinks || {};
        const thumbnail = imageLinks.extraLarge?.replace('http:', 'https:') || 
                         imageLinks.large?.replace('http:', 'https:') || 
                         imageLinks.medium?.replace('http:', 'https:') || 
                         imageLinks.thumbnail?.replace('http:', 'https:') || 
                         '/api/placeholder/300/400';
        const title = volumeInfo.title || 'Unknown Title';
        const authors = volumeInfo.authors?.join(', ') || 'Unknown Author';
        const description = volumeInfo.description?.substring(0, 150) + '...' || 'No description available';
        const infoLink = volumeInfo.infoLink || '#';
        
        // Check if book is new (published within last 90 days)
        const publishedDate = new Date(volumeInfo.publishedDate || '');
        const isNew = !isNaN(publishedDate.getTime()) && 
                     ((new Date()) - publishedDate) < (90 * 24 * 60 * 60 * 1000);
        
        // Check if book is bookmarked
        const isBookmarked = bookmarks.includes(book.id);
        
        // Create enhanced book card with 3D effect
        bookCard.innerHTML = `
            <div class="bookmark-indicator" style="${isBookmarked ? 'opacity: 1' : 'opacity: 0'}"></div>
            <div class="book-spine"></div>
            ${isNew ? '<span class="book-tag">New</span>' : ''}
            <div class="book-img">
                <img src="${thumbnail}" alt="${title}" onerror="this.src='/api/placeholder/300/400'" loading="lazy">
            </div>
            <div class="book-details">
                <h3 class="book-title">${title}</h3>
                <p class="book-author"><i class="fas fa-feather-alt"></i> ${authors}</p>
                <p class="book-description">${description}</p>
                <div class="book-actions">
                    <button class="book-btn icon-btn" onclick="handleSaveClick(event)">
                        <i class="far fa-bookmark"></i>
                    </button>
                    <a href="#" class="book-btn read-more-btn" onclick="handleReadMoreClick(event)">
                        <i class="fas fa-book-reader"></i> Read More
                    </a>
                </div>
            </div>
            <div class="book-glow"></div>
        `;
        
        booksGrid.appendChild(bookCard);
    });
    
    // Setup action buttons after adding books to DOM
    setupBookActionListeners();
}

// Function to set up event listeners for book actions
function setupBookActionListeners() {
    // Bookmark button event listeners
    document.querySelectorAll('.bookmark-btn').forEach(button => {
        button.addEventListener('click', function() {
            const bookId = this.getAttribute('data-id');
            toggleBookmark(bookId, this);
        });
    });
}

// Function to toggle bookmark status
function toggleBookmark(bookId, buttonElement) {
    const index = bookmarks.indexOf(bookId);
    const icon = buttonElement.querySelector('i');
    
    if (index === -1) {
        // Add bookmark
        bookmarks.push(bookId);
        icon.classList.remove('far');
        icon.classList.add('fas');
        
        // Show bookmark indicator
        const bookCard = buttonElement.closest('.book-card');
        const indicator = bookCard.querySelector('.bookmark-indicator');
        if (indicator) {
            indicator.style.opacity = '1';
        }
    } else {
        // Remove bookmark
        bookmarks.splice(index, 1);
        icon.classList.remove('fas');
        icon.classList.add('far');
        
        // Hide bookmark indicator
        const bookCard = buttonElement.closest('.book-card');
        const indicator = bookCard.querySelector('.bookmark-indicator');
        if (indicator) {
            indicator.style.opacity = '0';
        }
    }
    
    // Save to localStorage
    localStorage.setItem('bookmarks', JSON.stringify(bookmarks));
}

// Event listener for Enter key in the search input
searchInput.addEventListener('keyup', event => {
    if (event.key === 'Enter') {
        const query = searchInput.value.trim();
        if (query) {
            fetchBooks(query, 1); // Reset to first page on new search
        }
    }
});

// Function to search books by category
function searchBooksByCategory(category) {
    // Clear the search input to avoid confusion
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Reset pagination
    currentPage = 1;
    
    // Create a search query specific to the category
    // Using "subject:" parameter for more accurate category filtering
    const query = `subject:${category}`;
    
    // Call the fetchBooks function with the category query
    fetchBooks(query);
    
    // Update the UI to show what category is being viewed
    const resultsTitle = document.querySelector('#search-results .section-title');
    if (resultsTitle) {
        resultsTitle.textContent = `${category} Books`;
    }
    
    // Scroll to search results if it exists
    const searchResults = document.getElementById('search-results');
    if (searchResults) {
        searchResults.scrollIntoView({ behavior: 'smooth' });
    }
}

// Function to explore featured/popular books
function exploreCollection() {
    // Reset pagination
    currentPage = 1;
    
    // Clear the search input
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Fetch popular or featured books
    // Using a general query that will return popular books
    fetchBooks('bestseller');
    
    // Update the UI to indicate what's being shown
    const resultsTitle = document.querySelector('#search-results .section-title');
    if (resultsTitle) {
        resultsTitle.textContent = 'Featured Collection';
    }
    
    // Scroll to the search results
    const searchResults = document.getElementById('search-results');
    if (searchResults) {
        searchResults.scrollIntoView({ behavior: 'smooth' });
    }
}

// Function to load user's bookmarked books
function loadBookmarkedBooks() {
    if (bookmarks.length === 0) {
        booksGrid.innerHTML = '<p>You have no bookmarked books.</p>';
        return;
    }
    
    // Show loading state
    booksGrid.innerHTML = '<div class="loading">Loading your bookmarks...</div>';
    
    // Create promises for each bookmarked book
    const bookPromises = bookmarks.map(bookId => 
        fetch(`https://www.googleapis.com/books/v1/volumes/${bookId}?key=${apiKey}`)
            .then(response => response.json())
            .catch(error => {
                console.error(`Error fetching bookmarked book ${bookId}:`, error);
                return null;
            })
    );
    
    // Wait for all promises to resolve
    Promise.all(bookPromises)
        .then(books => {
            // Filter out any null results (errors)
            const validBooks = books.filter(book => book !== null);
            
            if (validBooks.length > 0) {
                displayBooks(validBooks);
            } else {
                booksGrid.innerHTML = '<p>Error loading bookmarked books.</p>';
            }
        });
}

// Add event listeners when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load featured books by default
    fetchBooks('bestseller');
    
    // Add event listeners to category cards
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            // Get the category name from the h3 element
            const categoryName = this.querySelector('h3').textContent;
            
            // Remove active class from all cards
            categoryCards.forEach(c => c.classList.remove('active-category'));
            
            // Add active class to clicked card
            this.classList.add('active-category');
            
            // Search for books by the selected category
            searchBooksByCategory(categoryName);
        });
    });
    
    // Add event listener to "Explore Collection" button
    const exploreButton = document.querySelector('.hero-content .cta-btn');
    if (exploreButton) {
        exploreButton.addEventListener('click', function() {
            exploreCollection();
            
            // Optional: Remove active class from all category cards
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach(card => card.classList.remove('active-category'));
        });
    }
    
    // Add event listeners for "My Bookmarks" button if it exists
    const bookmarksButton = document.getElementById('my-bookmarks');
    if (bookmarksButton) {
        bookmarksButton.addEventListener('click', function() {
            loadBookmarkedBooks();
            
            // Update the UI title
            const resultsTitle = document.querySelector('#search-results .section-title');
            if (resultsTitle) {
                resultsTitle.textContent = 'My Bookmarked Books';
            }
        });
    }
});