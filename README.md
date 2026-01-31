# E-Commerce API - Laravel 11 + Flutter

A comprehensive e-commerce API built with Laravel 11, featuring advanced shopping cart management, product search, coupons, reviews, notifications, and analytics.

## 🚀 Features

### Core E-Commerce Features
- ✅ **Product Management** - Categories, variants, attributes, discounts
- ✅ **User Authentication** - Sanctum-based API authentication
- ✅ **Shopping Cart** - Full cart management for both authenticated and guest users
- ✅ **Order Management** - Complete order processing with status tracking
- ✅ **Payment Integration** - ABA, ACLEDA payment gateways + Cash payments
- ✅ **Product Reviews** - Rating system with image uploads and helpful votes
- ✅ **Favorites/Wishlist** - Save favorite products

### Advanced Features (NEW)
- 🎯 **Advanced Search & Filters** - Multi-criteria product search with sorting
- 🎫 **Coupon System** - Percentage and fixed-amount discount coupons
- 📍 **Address Management** - Multiple shipping addresses with geolocation
- 🔔 **Notifications** - Real-time user notifications
- 📊 **Analytics Dashboard** - Sales reports, revenue trends, product performance
- 📧 **Newsletter** - Email subscription management
- 🔍 **Product Comparison** - Compare up to 4 products side-by-side
- 📦 **Order Tracking** - Track shipments with tracking numbers
- ⭐ **Enhanced Reviews** - Verified purchases, image uploads, helpful votes

## 📋 API Endpoints

### Authentication
```
POST   /api/login                    - User login
GET    /api/sanctum/user             - Get authenticated user
```

### Products
```
GET    /api/home                      - Homepage products (featured, recommended)
GET    /api/products/{id}             - Get product details
```

### Search & Filters
```
GET    /api/search                    - Advanced product search
       Query params: q, category_id, min_price, max_price, min_rating, 
                     is_featured, is_recommended, in_stock, sort_by, sort_order
GET    /api/search/suggestions        - Search autocomplete suggestions
GET    /api/search/filters            - Get available filters (categories, price range)
```

### Shopping Cart
```
GET    /api/cart                      - Get cart items
POST   /api/cart/add                  - Add item to cart
PUT    /api/cart/{id}                 - Update cart item quantity
DELETE /api/cart/{id}                 - Remove item from cart
DELETE /api/cart                      - Clear entire cart
```

### Coupons
```
GET    /api/coupons                   - List active coupons
POST   /api/coupons/validate          - Validate and apply coupon
```

### Favorites
```
GET    /api/favorites                 - Get user favorites
POST   /api/favorites/add             - Add to favorites
POST   /api/favorites/remove          - Remove from favorites
```

### Product Comparison
```
GET    /api/comparison                - Get comparison list
POST   /api/comparison/add            - Add product to comparison
DELETE /api/comparison/{productId}    - Remove from comparison
DELETE /api/comparison                - Clear comparison list
```

### Newsletter
```
POST   /api/newsletter/subscribe      - Subscribe to newsletter
POST   /api/newsletter/unsubscribe    - Unsubscribe from newsletter
GET    /api/newsletter/verify/{token} - Verify email subscription
```

### Orders
```
GET    /api/user/orders               - Get user orders
GET    /api/user/orders/{id}          - Get order details
POST   /api/sale/proccess             - Create new order
POST   /api/checkout/cash             - Cash checkout
```

### Addresses (Auth Required)
```
GET    /api/addresses                 - List user addresses
POST   /api/addresses                 - Create new address
PUT    /api/addresses/{id}            - Update address
DELETE /api/addresses/{id}            - Delete address
POST   /api/addresses/{id}/set-default - Set default address
```

### Notifications (Auth Required)
```
GET    /api/notifications             - Get notifications
GET    /api/notifications/unread-count - Get unread count
POST   /api/notifications/{id}/read   - Mark as read
POST   /api/notifications/read-all    - Mark all as read
DELETE /api/notifications/{id}        - Delete notification
DELETE /api/notifications             - Clear all notifications
```

### Reviews (Auth Required)
```
GET    /api/reviews                   - List reviews
POST   /api/reviews                   - Create review
GET    /api/reviews/{id}              - Get review details
PUT    /api/reviews/{id}              - Update review
DELETE /api/reviews/{id}              - Delete review
POST   /api/reviews/{id}/vote-helpful - Vote review as helpful
GET    /api/products/{id}/reviews     - Get product reviews
```

### Analytics (Auth Required - Admin)
```
GET    /api/analytics/dashboard       - Dashboard metrics
       Query params: period (day, week, month, year)
GET    /api/analytics/sales-report    - Sales report
       Query params: start_date, end_date
GET    /api/analytics/product/{id}/performance - Product performance metrics
```

### User Profile
```
GET    /api/user/profile/{id}         - Get user profile
POST   /api/user/profile/{id}         - Update user profile
```

## 🗄️ Database Schema

### New Tables
- **carts** - Shopping cart items
- **coupons** - Discount coupons
- **addresses** - User shipping addresses
- **notifications** - User notifications
- **newsletters** - Email subscriptions
- **reviews** - Product reviews (enhanced)
- **review_helpful** - Review helpful votes
- **product_comparisons** - Product comparison lists

### Enhanced Tables
- **transactions** - Added: coupon_id, discount_amount, shipping_address_id, tracking_number, shipped_at, delivered_at
- **products** - Enhanced with reviews, cart items, and comparison relationships
- **users** - Enhanced with addresses, notifications, cart, and reviews relationships

## 🔧 Installation

1. Clone the repository
```bash
git clone <repository-url>
cd e_commerce_api_flutter
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Run migrations
```bash
php artisan migrate
```

5. Seed database (optional)
```bash
php artisan db:seed
```

6. Start development server
```bash
php artisan serve
```

## 📱 Guest User Support

The following features support both authenticated and guest users via session ID:
- Shopping Cart
- Product Comparison
- Favorites (with session tracking)

**Usage**: Include `X-Session-ID` header in requests for guest users.

## 💳 Coupon System

### Coupon Types
- **Percentage**: Discount by percentage (e.g., 10% off)
- **Fixed**: Fixed amount discount (e.g., $5 off)

### Coupon Validation
- Minimum purchase amount
- Maximum discount cap
- Usage limits
- Valid date ranges
- Active/inactive status

### Example Coupon Validation
```json
POST /api/coupons/validate
{
  "code": "SAVE10",
  "subtotal": 100.00
}

Response:
{
  "success": true,
  "data": {
    "coupon": {...},
    "discount_amount": 10.00,
    "final_amount": 90.00
  }
}
```

## 📊 Analytics Features

### Dashboard Metrics
- Total Revenue
- Total Orders
- Total Customers
- Average Order Value
- Revenue Trend (daily breakdown)
- Top Selling Products
- Order Status Distribution
- Recent Orders

### Sales Reports
- Custom date range reports
- Total revenue and discount tracking
- Detailed transaction listings

### Product Performance
- Units sold
- Total revenue
- Average rating
- Review count
- Favorite count

## 🔍 Advanced Search

### Search Capabilities
- Text search (product name, description)
- Category filtering
- Price range filtering
- Rating filtering
- Stock availability filtering
- Featured/Recommended filtering

### Sorting Options
- Newest first
- Price: Low to High
- Price: High to Low
- Highest Rated
- Most Popular (by favorites)

### Example Search Query
```
GET /api/search?q=phone&category_id=1&min_price=100&max_price=500&sort_by=price_low&per_page=20
```

## 🔔 Notification Types

- **order_update** - Order status changes
- **promotion** - Promotional offers
- **system** - System notifications
- Custom notification types

## 🛡️ Security Features

- Sanctum API authentication
- CSRF protection
- SQL injection prevention
- XSS protection
- Rate limiting
- Input validation

## 📦 Technologies Used

- **Laravel 11** - PHP Framework
- **MySQL** - Database
- **Laravel Sanctum** - API Authentication
- **Spatie Permission** - Role & Permission Management
- **Carbon** - Date/Time manipulation

## 🎨 Frontend Integration

This API is designed to work with Flutter mobile applications. Key features for Flutter integration:

- RESTful API design
- JSON responses
- Token-based authentication
- Image URL handling
- Session management for guest users
- Comprehensive error handling

## 📝 Response Format

All API responses follow this format:

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data
  }
}
```

Error responses:
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    // Validation errors (if applicable)
  }
}
```

## 🚧 Future Enhancements

- [ ] Real-time notifications with WebSockets
- [ ] Advanced inventory management
- [ ] Multi-vendor support
- [ ] Social media integration
- [ ] Product recommendations AI
- [ ] Advanced analytics with charts
- [ ] Email notifications
- [ ] SMS notifications
- [ ] Multi-currency support
- [ ] Multi-language support

## 📄 License

This project is open-sourced software licensed under the MIT license.

## 👥 Contributors

- Your Name - Initial work

## 📞 Support

For support, email support@example.com or create an issue in the repository.