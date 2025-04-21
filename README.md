# Athena Library Management System

A modern, user-friendly library management system built with PHP, MySQL, and JavaScript. The system allows users to browse books, borrow them, and manage their reading list with features like bookmarking and detailed book information.

## Features

- **User Authentication**
  - Secure login and registration system
  - User profile management
  - Session handling

- **Book Management**
  - Integration with Google Books API for extensive book catalog
  - Real-time book search functionality
  - Advanced filtering and categorization
  - Detailed book information with cover images

- **Interactive Features**
  - Book borrowing system
  - Save/bookmark favorite books
  - Read more details about each book
  - Preview books through Google Books
  - Responsive design for all devices

- **User Dashboard**
  - View borrowed books
  - Manage bookmarked books
  - Track borrowing history
  - User profile settings

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP/WAMP/MAMP server
- Modern web browser
- Internet connection (for Google Books API)

## Installation

1. Clone the repository to your local machine:
   ```bash
   git clone https://github.com/yourusername/athena-library.git
   ```

2. Move the project files to your web server directory (e.g., htdocs for XAMPP):
   ```bash
   mv athena-library /path/to/xampp/htdocs/
   ```

3. Create a new MySQL database named 'athena_library':
   ```sql
   CREATE DATABASE athena_library;
   ```

4. Import the database structure:
   ```bash
   mysql -u root -p athena_library < database.sql
   ```

5. Configure the database connection:
   - Open `config/database.php`
   - Update the database credentials if needed

6. Start your web server and MySQL service

7. Access the application through your web browser:
   ```
   http://localhost/athena-library
   ```

## Configuration

### Google Books API
1. Get a Google Books API key from the Google Cloud Console
2. Update the API key in `js/script.js`:
   ```javascript
   const apiKey = 'YOUR_API_KEY';
   ```

### Database Settings
- Default database configuration:
  - Host: localhost
  - Username: root
  - Password: (empty)
  - Database: athena_library

## Usage

1. **Registration/Login**
   - Create a new account or login with existing credentials
   - Profile information can be updated in the dashboard

2. **Browsing Books**
   - Use the search bar to find specific books
   - Browse through categories
   - Click "Read More" for detailed information
   - Save interesting books for later

3. **Borrowing Books**
   - Click "Borrow" on any available book
   - Track borrowed books in your dashboard
   - Return books when finished

4. **Managing Books**
   - View your borrowed books
   - Manage saved/bookmarked books
   - Track your reading history

## Security

- Password hashing using modern algorithms
- SQL injection prevention
- XSS protection
- CSRF protection
- Secure session handling

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Google Books API for the extensive book database
- Font Awesome for icons
- Bootstrap for some UI components
- XAMPP for the development environment

## Support

For support, please email support@athena-library.com or create an issue in the repository.

## Authors

- Prasant Yadav - Initial work and maintenance

## Project Status

Active development - Bug reports and feature requests are welcome! 