# Stripe Demo - Laravel Payment Integration

A Laravel 12 application demonstrating Stripe payment integration with checkout sessions, webhooks, and order management.

## Overview

This is a complete example of integrating Stripe payments into a Laravel application. It demonstrates:

- **Checkout Sessions**: Create secure Stripe checkout sessions for payments
- **Order Management**: Track orders and payment status in the database
- **Webhook Handling**: Process Stripe webhook events for payment confirmations
- **Session Verification**: Verify payment success and handle cancellations

## Features

- 💳 **Stripe Checkout Integration**: Create payment checkout sessions
- 🔐 **Webhook Security**: Verify Stripe webhook signatures
- 📊 **Order Tracking**: Store orders with payment status (pending, paid, failed)
- 🎨 **Tailwind CSS**: Modern, responsive UI with Tailwind CSS
- ⚡ **Vite Build System**: Fast development and production builds
- 🧪 **PHPUnit**: Testing framework for unit and feature tests

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js and npm
- Stripe account with API keys
- Database (SQLite, MySQL, PostgreSQL, etc.)

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Amjad-Ali-Panhwar/stripe-demo.git
   cd stripe-demo
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Set up environment variables**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Add your Stripe keys to `.env`**
   ```
   STRIPE_PUBLIC=your_publishable_key
   STRIPE_SECRET=your_secret_key
   STRIPE_WEBHOOK_SECRET=your_webhook_signing_secret
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

## Quick Start

### Development Mode

Run all services concurrently (Laravel server, queue, logs, Vite):

```bash
composer run dev
```

This starts:
- PHP development server
- Queue listener
- Pail logs
- Vite dev server

### Production Build

```bash
npm run build
```

## Project Structure

```
├── app/
│   ├── Http/Controllers/StripeController.php    # Payment logic
│   └── Models/
│       ├── Order.php                            # Order model
│       └── User.php                             # User model
├── database/
│   └── migrations/                              # Database schema
├── resources/
│   ├── views/
│   │   ├── checkout.blade.php                   # Payment form
│   │   ├── success.blade.php                    # Success page
│   │   ├── cancel.blade.php                     # Cancellation page
│   │   └── welcome.blade.php                    # Home page
│   └── css/
│       └── app.css                              # Tailwind styles
├── routes/
│   └── web.php                                  # Route definitions
└── config/
    ├── services.php                             # Service configuration
    └── ...
```

## Routes

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/` | Home page |
| GET | `/checkout` | Show checkout form |
| POST | `/create-checkout-session` | Create Stripe checkout session |
| GET | `/success?session_id={id}` | Payment success page |
| GET | `/cancel` | Payment canceled page |
| POST | `/webhook/stripe` | Stripe webhook endpoint |

## API Integration

### Create Checkout Session

**Request:**
```
POST /create-checkout-session
Content-Type: application/json

{
  "email": "customer@example.com"
}
```

**Response:**
```json
{
  "url": "https://checkout.stripe.com/pay/..."
}
```

### Webhook Events

The application handles:
- `checkout.session.completed` - Payment successful
- `payment_intent.payment_failed` - Payment failed

## Configuration

Update Stripe configuration in `config/services.php`:

```php
'stripe' => [
    'public' => env('STRIPE_PUBLIC'),
    'secret' => env('STRIPE_SECRET'),
],
```

## Testing

Run the test suite:

```bash
composer test
```

## Environment Variables

| Variable | Description |
|----------|-------------|
| `STRIPE_PUBLIC` | Stripe publishable key |
| `STRIPE_SECRET` | Stripe secret key |
| `STRIPE_WEBHOOK_SECRET` | Webhook signing secret |

## Stripe Setup

1. Go to [Stripe Dashboard](https://dashboard.stripe.com)
2. Find your API keys in Developers > API Keys
3. Set up webhooks endpoint in Developers > Webhooks:
   - Endpoint URL: `https://yourdomain.com/webhook/stripe`
   - Events: `checkout.session.completed`, `payment_intent.payment_failed`

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For issues or questions, please open an issue on GitHub.
