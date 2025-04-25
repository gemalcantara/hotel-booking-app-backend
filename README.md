# Hotel Booking API

A Laravel-based REST API for hotel room booking management with authentication, validation, and API documentation.

## Features

- **Authentication**: Secure API endpoints with Laravel Sanctum token authentication
- **Booking Management**: Create, read, update, and delete booking records
- **Data Validation**: Comprehensive request validation including date checks and custom promo code rules
- **Conflict Prevention**: Prevents double-booking of rooms through date overlap checks
- **Database Transactions**: Ensures data integrity during booking operations
- **API Documentation**: OpenAPI/Swagger documentation of all endpoints
- **Performance Monitoring**: Custom middleware for logging request performance metrics
- **Soft Deletes**: Implements soft deletion for bookings to maintain data history

## API Endpoints

### Authentication

- POST `/api/login`: Obtain an API token (Sanctum)
- POST `/api/logout`: Invalidate the current API token

### Bookings

- GET `/api/bookings`: Retrieve paginated list of bookings with room details
- POST `/api/bookings`: Create a new booking with validation
- GET `/api/bookings/{id}`: Get specific booking details
- PUT `/api/bookings/{id}`: Update an existing booking
- DELETE `/api/bookings/{id}`: Soft delete a booking

## Requirements

- PHP 8.1 or higher
- Composer
- MySQL database
- Laravel 10.x

## Installation & Setup

1. **Clone the repository**

```bash
git clone https://github.com/yourusername/hotel-booking-app-backend.git
cd hotel-booking-app-backend
```

2. **Install dependencies**

```bash
composer install
```

3. **Configure environment variables**

Create an `.env` file by copying the example:

```bash
cp .env.example .env
```

Update the database configuration in `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_booking
DB_USERNAME=root
DB_PASSWORD=
```

4. **Generate application key**

```bash
php artisan key:generate
```

5. **Run database migrations**

```bash
php artisan migrate
```

6. **Seed the database (optional)**

```bash
php artisan db:seed
```

7. **Start the development server**

```bash
php artisan serve
```

The API will be available at http://localhost:8000

## API Authentication

For simplicity in testing, a hardcoded API token is provided:

```
Bearer 12345
```

This token is automatically created when you run the database seeder. You can use this token by including it in your API requests with the following header:

```
Authorization: Bearer 12345
```

Example using curl:
```bash
curl -X GET http://localhost:8000/api/bookings \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 12345"
```

## API Documentation

Access the Swagger API documentation at:

```
http://localhost:8000/api/documentation
```

## Validation Rules

- **guest_name**: Required, max 255 characters
- **room_id**: Required, must exist in rooms table and be available
- **check_in_date**: Required, valid date, must be after today
- **check_out_date**: Required, valid date, must be after check_in_date
- **promo_code**: Optional, max 50 characters, custom format validation (uppercase alphanumeric, 6-10 characters)

## Response Examples

### POST /api/bookings (Success)

```json
{
    "message": "Booking created",
    "booking": {
        "id": 1,
        "guest_name": "John Doe",
        "room_id": 1,
        "check_in_date": "2025-05-01",
        "check_out_date": "2025-05-03",
        "status": "pending",
        "promo_code": "SUMMER2025"
    }
}
```

### POST /api/bookings (Validation Error)

```json
{
    "message": "Validation failed",
    "errors": {
        "guest_name": [
            "The guest name field is required."
        ],
        "check_in_date": [
            "The check in date must be a date after today."
        ]
    }
}
```

## Testing

Run the test suite with:

```bash
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
