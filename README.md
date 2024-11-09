# Comprint Services Device Management System

## Overview

A comprehensive device management and repair tracking system built for Comprint Services. This web-based application helps manage device repairs, track repair status, handle employee access, and generate reports.

## Features

### 1. Authentication & Security

- Secure login system with role-based access control (Admin/Employee)
- Session management with automatic timeout
- Password encryption and secure data handling
- XSS and SQL injection protection

### 2. Dashboard

- Quick statistics overview (Total Devices, Pending Repairs, Completed Repairs)
- Visual data representation using Chart.js
  - Device Status Distribution (Doughnut Chart)
  - Device Types Distribution (Bar Chart)
- Recent Devices Table
- Real-time updates

### 3. Device Management

- Add new devices with detailed information
- Device categories: Computer, Printer, Network, Other
- Conditional fields based on device type
- Status tracking (Pending, In Progress, Completed)
- File attachments for device documentation
- Bulk actions support
- Print functionality for device records

### 4. Employee Management (Admin Only)

- Add/Edit/Delete employees
- Role assignment
- Contact information management
- Activity tracking

### 5. Reporting System

- Date range based reports
- Status summary
- Device type analysis
- Export capabilities (PDF, Excel)
- Custom report generation
- Visual data representation

### 6. System Settings (Admin Only)

- Company information management
- System preferences
- Date format settings
- Timezone configuration
- Database backup
- System maintenance tools

## Technical Requirements

### Server Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled

### Browser Requirements

- Modern browsers (Chrome, Firefox, Safari, Edge)
- JavaScript enabled
- Minimum screen resolution: 1024x768

## Installation

1. Clone the repository:
   bash
   git clone https://github.com/samcuxx/Device-Management-System-PHP.git

2. Import the database schema:
   db/comprint_services.sql

## Project Structure

comprint-services/
├── api/ # API endpoints
├── assets/ # Static resources
│ ├── bootstrap/ # Bootstrap files
│ ├── boxicons/ # Icon files
│ ├── css/ # Custom CSS
│ └── js/ # JavaScript files
├── backups/ # Database backups
├── classes/ # PHP classes
├── config/ # Configuration files
├── includes/ # Helper functions
├── public/ # Public files
├── templates/ # Reusable templates
└── uploads/ # File uploads

## Known Issues and Solutions

### CSS Compatibility

For Safari compatibility, add the following to your CSS:

## Security Features

- Secure password hashing using PHP's password_hash()
- Session management with timeout
- CSRF protection
- Input validation and sanitization
- Prepared statements for database queries
- File upload validation
- Role-based access control

## Maintenance

### Database Backup

To create a database backup:

### System Cleanup

The system includes automatic:

- Temporary file cleanup
- Log rotation
- Database optimization
- Cache management

## Development Guidelines

### Code Style

- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Comment complex logic
- Keep functions small and focused

### Security Best Practices

- Always validate user input
- Use prepared statements for queries
- Sanitize output with htmlspecialchars()
- Implement proper access control

### Error Handling

- Use try-catch blocks for error handling
- Log errors appropriately
- Display user-friendly error messages

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please email support@comprintservices.com or open an issue in the repository.

## Authors

- Initial work - [Your Name]

## Acknowledgments

- Bootstrap for the UI framework
- Chart.js for data visualization
- DataTables for table management

### System Cleanup

The system includes automatic:

- Temporary file cleanup
- Log rotation
- Database optimization
- Cache management

## Development Guidelines

### Code Style

- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Comment complex logic
- Keep functions small and focused

### Security Best Practices

- Always validate user input
- Use prepared statements for queries
- Sanitize output with htmlspecialchars()
- Implement proper access control

### Error Handling

- Use try-catch blocks for error handling
- Log errors appropriately
- Display user-friendly error messages

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please email support@comprintservices.com or open an issue in the repository.

## Authors

- Initial work - [Your Name]

## Acknowledgments

- Bootstrap for the UI framework
- Chart.js for data visualization
- DataTables for table management

### System Cleanup

The system includes automatic:

- Temporary file cleanup
- Log rotation
- Database optimization
- Cache management

## Development Guidelines

### Code Style

- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Comment complex logic
- Keep functions small and focused

### Security Best Practices

- Always validate user input
- Use prepared statements for queries
- Sanitize output with htmlspecialchars()
- Implement proper access control

### Error Handling

- Use try-catch blocks for error handling
- Log errors appropriately
- Display user-friendly error messages

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please email support@comprintservices.com or open an issue in the repository.

## Authors

- Initial work - [Your Name]

## Acknowledgments

- Bootstrap for the UI framework
- Chart.js for data visualization
- DataTables for table management
