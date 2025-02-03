# Affrahdz Mobile App API

## Overview
This API serves as the backend for the Affrahdz mobile application,An app for discovering and booking wedding halls, DJs, and event services, where members can publish and manage their events,supporting multiple resources such as `annonce`, `client`, `membre`, `admin`, `moderateur`, `reservation`, and more. It is built using PHP and follows REST API principles with JWT authentication.

## Features
- JWT Authentication: Secure access to endpoints with JSON Web Tokens.
- RESTful Endpoints: Manage resources like clients, members, reservations, announcements, etc.
- Advanced Validation: Validate inputs for images, videos, and other data types.
- Custom Resources & Collections: Customized data structures for efficient data handling.
- Error Handling: Advanced error handling for better debugging and user feedback.
- Authorization: Role-based access control for admin, membre, client, and moderateur.
- Database Optimization: Indexes, optimized queries, and SQL injection prevention.
- Mailer: Integrated email functionality for notifications and password resets.
- CORS Support: Cross-Origin Resource Sharing for secure client-server communication.
- Structured Code: Object-Oriented Programming (OOP) principles for maintainability.
- Environment Configuration: .env file for managing environment variables.
- Docker Integration: Easy setup with Docker and phpMyAdmin for database management.
- HVVM (opcache) for fast api execute bytecode .


## Installation & Setup
### Prerequisites
- Docker & Docker Compose
  
### Run the API using Docker
1. Clone the repository:
   ```sh
   git clone https://github.com/your-username/affrahdz-api.git
   cd affrahdz-api
   ```
2. Build and start the containers:
   ```sh
   docker-compose up -d --build
   ```
3. Verify the database in phpMyAdmin:
   - Open `127.0.0.1:8082` in your browser.
   - Login with:
     - Username: `root`
     - Password: `rootpassword`
   - Check for the database `affrah` and its tables (should match `db.sql`).
# API Endpoints

## Clients
- `GET /clients` - Get all clients
- `GET /clients/{id}` - Get a specific client by ID
- `GET /clients/{id}/image` - Get a client's image by ID
- `POST /clients` - Create a new client
- `POST /clients/login` - Client login
- `POST /clients/info` - Get user info by token
- `POST /clients/forgot-password` - Update client password
- `POST /clients/otp` - Verify client OTP
- `PUT /clients/{id}` - Update client details
- `DELETE /clients/{id}` - Delete a client

## Members
- `GET /members` - Get all members
- `GET /members/{id}` - Get a specific member by ID
- `GET /members/{id}/image` - Get a member's image by ID
- `POST /members` - Create a new member
- `POST /members/login` - Member login
- `POST /members/forgot-password` - Update member password
- `POST /members/otp` - Verify member OTP
- `PUT /members/{id}` - Update member details
- `DELETE /members/{id}` - Delete a member

## Reservations
- `GET /reservations` - Get all reservations
- `GET /reservations/mine` - Get user's reservations
- `GET /reservations/plan` - Get reservation planning
- `GET /reservations/{id}` - Get a specific reservation by ID
- `POST /reservations` - Create a new reservation
- `PUT /reservations/{id}` - Update reservation details
- `DELETE /reservations/{id}` - Delete a reservation

## Announcements
- `GET /announcements` - Get all announcements
- `GET /announcements/mine` - Get user's announcements
- `GET /announcements/favorites` - Get favorite announcements
- `GET /announcements/categories` - Get announcement categories
- `GET /announcements/vip` - Get VIP announcements
- `GET /announcements/gold` - Get Gold announcements
- `GET /announcements/{id}` - Get a specific announcement by ID
- `GET /announcements/search/{word}` - Search announcements
- `GET /announcements/{id}/visits` - Track visits to an announcement
- `POST /announcements` - Create a new announcement
- `PUT /announcements/{id}` - Update announcement details
- `PUT /announcements/{id}/like` - Like an announcement
- `DELETE /announcements/{id}` - Delete an announcement

## Favorites
- `GET /favorites` - Get all favorites
- `GET /favorites/{id}` - Get a specific favorite by ID
- `POST /favorites` - Add to favorites
- `PUT /favorites/{id}` - Update favorite details
- `DELETE /favorites/{id}` - Remove from favorites

## Boosts
- `GET /boosts` - Get all boosts
- `GET /boosts/{id}` - Get a specific boost by ID
- `POST /boosts` - Create a new boost
- `PUT /boosts/{id}` - Update boost details
- `DELETE /boosts/{id}` - Delete a boost

## Contacts
- `GET /contacts` - Get all contacts
- `GET /contacts/{id}` - Get a specific contact by ID
- `POST /contacts` - Create a new contact
- `PUT /contacts/{id}` - Update contact details
- `DELETE /contacts/{id}` - Delete a contact

## Images
- `GET /images` - Get all images
- `GET /images/{id}` - Get a specific image by ID
- `POST /images` - Upload a new image
- `PUT /images/{id}` - Update image details
- `DELETE /images/{id}` - Delete an image

## Response Structure

- Every API response follows this structure:

{ "status": "success | error", "message": "Description of the response", "data": "Actual data object or null", "token": "JWT token if applicable" }

## Deployment
For production deployment, ensure:

- Secure MySQL credentials

- HTTPS setup for the API

- Proper rate-limiting and logging mechanisms

- Environment variables are properly configured in the .env file


## Contributing
Feel free to submit issues and pull requests!
- Postman Collections invite to test API:
   - https://app.getpostman.com/join-team?invite_code=ef7f0a5882cdd2edc5ef51799e14aa0ff53d08ba9d45b280fb8a015e07872f9a&target_code=e20db84e09f4e75e1c5eb879a3d31ef9
