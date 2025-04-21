// API Key
const apiKey = 'AIzaSyB7kCaV6jLUib5S8TehmkrqdqVVFD_T1tM';

// Initialize variables
let currentPage = 1;
const resultsPerPage = 10;
let totalPages = 1;
let currentQuery = '';

// Bookmarks and borrowed books from localStorage
let bookmarks = JSON.parse(localStorage.getItem('bookmarks') || '[]');
let borrowedBooks = JSON.parse(localStorage.getItem('borrowedBooks') || '[]');

// Function to fetch books from Google Books API
async function fetchBooks(query, page = 1) {
    currentQuery = query;
    currentPage = page;
    
    const booksContainer = document.getElementById('books-container');
    booksContainer.innerHTML = '<div class="loading">Loading books...</div>';
    
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
            totalPages = Math.ceil(data.totalItems / resultsPerPage);
            totalPages = Math.min(totalPages, 3); // Limit to 3 pages
            
            displayBooks(data.items);
        } else {
            booksContainer.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <p>No books found for your search. Try a different search term.</p>
                </div>
            `;
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
        
        booksContainer.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <p>${errorMessage}</p>
                <button class="retry-btn" onclick="fetchBooks('${currentQuery}', ${currentPage})">
                    <i class="fas fa-redo"></i> Try Again
                </button>
            </div>
        `;
    }
}

// Function to display books in the grid
function displayBooks(books) {
    const booksContainer = document.getElementById('books-container');
    booksContainer.innerHTML = '';

    books.forEach(book => {
        const volumeInfo = book.volumeInfo;
        const imageLinks = volumeInfo.imageLinks || {};
        const thumbnail = imageLinks.extraLarge?.replace('http:', 'https:') || 
                         imageLinks.large?.replace('http:', 'https:') || 
                         imageLinks.medium?.replace('http:', 'https:') || 
                         imageLinks.thumbnail?.replace('http:', 'https:') || 
                         '/api/placeholder/300/400';
        const title = volumeInfo.title || 'Unknown Title';
        const authors = volumeInfo.authors?.join(', ') || 'Unknown Author';
        const description = volumeInfo.description?.substring(0, 100) + '...' || 'No description available.';
        const infoLink = volumeInfo.infoLink || 'https://www.google.com/books';
        
        const publishedDate = new Date(volumeInfo.publishedDate || '');
        const isNew = !isNaN(publishedDate.getTime()) && 
                     ((new Date()) - publishedDate) < (90 * 24 * 60 * 60 * 1000);
        
        const isBookmarked = bookmarks.includes(book.id);
        
        const bookCard = document.createElement('div');
        bookCard.className = 'book-card';
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
                    <button class="book-btn icon-btn bookmark-btn" data-id="${book.id}">
                    
                        <i class="${isBookmarked ? 'fas' : 'far'} fa-bookmark"> </i>
                    </button>
                </div>
                <div class="book-actions">
                    <a href="${infoLink}" target="_blank" class="book-btn more-info-btn">
                        <i class="fas fa-book-reader"></i> Read More
                    </a>
                </div>
            </div>
            <div class="book-glow"></div>
        `;
        
        booksContainer.appendChild(bookCard);
    });
    
    setupBookActionListeners();
}

// Function to set up event listeners for book actions
function setupBookActionListeners() {
    document.querySelectorAll('.bookmark-btn').forEach(button => {
        button.addEventListener('click', function() {
            const bookId = this.getAttribute('data-id');
            toggleBookmark(bookId, this);
        });
    });
}

// Function to toggle bookmark status
function toggleBookmark(bookId, buttonElement) {
    const bookCard = buttonElement.closest('.book-card');
    const bookmarkIndicator = bookCard.querySelector('.bookmark-indicator');
    const iconElement = buttonElement.querySelector('i');
    
    if (bookmarks.includes(bookId)) {
        bookmarks = bookmarks.filter(id => id !== bookId);
        localStorage.setItem('bookmarks', JSON.stringify(bookmarks));
        iconElement.className = 'far fa-bookmark';
        bookmarkIndicator.style.opacity = '0';
    } else {
        bookmarks.push(bookId);
        localStorage.setItem('bookmarks', JSON.stringify(bookmarks));
        iconElement.className = 'fas fa-bookmark';
        bookmarkIndicator.style.opacity = '1';
    }
}

// Function to load saved books
function loadSavedBooks() {
    const savedBooksContainer = document.getElementById('saved-books-container');
    const bookmarks = JSON.parse(localStorage.getItem('bookmarks') || '[]');
    
    if (bookmarks.length === 0) {
        savedBooksContainer.innerHTML = `
            <div class="no-saved-books">
                <i class="fas fa-bookmark"></i>
                <h3>No Saved Books Yet</h3>
                <p>Start exploring and save your favorite books!</p>
            </div>
        `;
        return;
    }

    savedBooksContainer.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading saved books...</div>';

    Promise.all(bookmarks.map(bookId => 
        fetch(`https://www.googleapis.com/books/v1/volumes/${bookId}?key=${apiKey}`)
            .then(response => response.json())
            .catch(error => {
                console.error(`Error fetching saved book ${bookId}:`, error);
                return null;
            })
    ))
    .then(books => {
        const validBooks = books.filter(book => book !== null);
        if (validBooks.length > 0) {
            displaySavedBooks(validBooks);
        } else {
            savedBooksContainer.innerHTML = `
                <div class="no-saved-books">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Error Loading Books</h3>
                    <p>There was a problem loading your saved books. Please try again later.</p>
                </div>
            `;
        }
    });
}

// Function to display saved books
function displaySavedBooks(books) {
    const container = document.getElementById('saved-books-container');
    container.innerHTML = '';

    books.forEach(book => {
        const volumeInfo = book.volumeInfo;
        const bookCard = document.createElement('div');
        bookCard.className = 'book-card saved';
        
        bookCard.innerHTML = `
            <div class="book-cover">
                <img src="${volumeInfo.imageLinks?.thumbnail || 'assets/default-book.jpg'}" alt="${volumeInfo.title}">
                <div class="book-badge">Saved</div>
            </div>
            <div class="book-info">
                <h3>${volumeInfo.title}</h3>
                <p class="author">${volumeInfo.authors?.[0] || 'Unknown Author'}</p>
                <p class="description">${volumeInfo.description?.substring(0, 150) || 'No description available'}...</p>
                <div class="book-actions">
                    <button class="read-more-btn" data-book-id="${book.id}">
                        <i class="fas fa-book-open"></i> Read More
                    </button>
                    <button class="remove-save-btn" data-book-id="${book.id}">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;

        container.appendChild(bookCard);

        const readMoreBtn = bookCard.querySelector('.read-more-btn');
        readMoreBtn.addEventListener('click', () => showBookDetails(book));

        const removeBtn = bookCard.querySelector('.remove-save-btn');
        removeBtn.addEventListener('click', () => removeSavedBook(book.id, bookCard));
    });
}

// Function to remove a saved book
function removeSavedBook(bookId, bookCard) {
    bookmarks = bookmarks.filter(id => id !== bookId);
    localStorage.setItem('bookmarks', JSON.stringify(bookmarks));
    bookCard.remove();
    
    if (bookmarks.length === 0) {
        document.getElementById('saved-books-container').innerHTML = `
            <div class="no-saved-books">
                <i class="fas fa-bookmark"></i>
                <h3>No Saved Books Yet</h3>
                <p>Start exploring and save your favorite books!</p>
            </div>
        `;
    }
}

// Initialize when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load featured books by default
    fetchBooks('bestseller');
    
    // Add search functionality
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');

    // Search when clicking the search button
    searchButton.addEventListener('click', function() {
        const query = searchInput.value.trim();
        if (query) {
            fetchBooks(query, 1); // Reset to first page on new search
        }
    });

    // Search when pressing Enter in the search input
    searchInput.addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            const query = searchInput.value.trim();
            if (query) {
                fetchBooks(query, 1); // Reset to first page on new search
            }
        }
    });

    // Add category card click handlers
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Remove active class from all cards
            document.querySelectorAll('.category-card').forEach(c => {
                c.classList.remove('active');
            });
            
            // Add active class to clicked card
            this.classList.add('active');
            
            // Search for books in this category
            fetchBooks(`subject:${category}`, 1);
            
            // Update the section title
            const sectionTitle = document.querySelector('#search-results .section-title');
            if (sectionTitle) {
                sectionTitle.textContent = `${category} Books`;
            }
            
            // Scroll to search results
            document.getElementById('search-results').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        });
    });
    
    // Add event listeners for saved books section
    document.getElementById('saved-books-btn').addEventListener('click', function() {
        const searchSection = document.getElementById('search-results');
        const savedSection = document.getElementById('saved-books-section');
        
        searchSection.style.display = 'none';
        savedSection.style.display = 'block';
        loadSavedBooks();
    });

    document.getElementById('back-to-search').addEventListener('click', function() {
        const searchSection = document.getElementById('search-results');
        const savedSection = document.getElementById('saved-books-section');
        
        savedSection.style.display = 'none';
        searchSection.style.display = 'block';
    });

    // Profile section functionality
    const profileBtn = document.getElementById('profile-btn');
    const profileSection = document.getElementById('profile-section');
    const closeProfileBtn = document.getElementById('close-profile');
    const overlay = document.getElementById('overlay');

    function showProfile() {
        profileSection.style.display = 'block';
        overlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function hideProfile() {
        profileSection.style.display = 'none';
        overlay.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    profileBtn.addEventListener('click', showProfile);
    closeProfileBtn.addEventListener('click', hideProfile);
    overlay.addEventListener('click', hideProfile);
}); 