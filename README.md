# 🚤 Boat Booking System

A comprehensive web application for managing boat reservations with advanced features including real-time availability checking, payment processing, user reviews, and administrative analytics.

## 🌟 Features

### 🎯 **Core Functionality**
- **Boat Management**: Display boats with details, images, capacity, and pricing
- **Real-time Booking**: Advanced availability checking before payment processing
- **Payment Integration**: Multiple payment gateways (Stripe, PayPal, Bank Transfer)
- **User Reviews & Ratings**: Interactive 5-star rating system with customer feedback
- **Admin Dashboard**: Comprehensive management with analytics and reporting

### 🔐 **User Features**
- Browse available boats with detailed information
- Real-time availability checking
- Secure booking process with validation
- Payment processing with multiple options
- Booking status tracking
- Leave reviews and ratings
- Contact and support system

### 👨‍💼 **Admin Features**
- **Dashboard**: Overview of bookings, revenue, and analytics
- **Boat Management**: Add, edit, delete boats with image uploads
- **Booking Management**: Accept/reject bookings with remarks
- **Review Moderation**: Approve/disapprove customer reviews
- **Analytics Dashboard**: Revenue charts, booking trends, customer ratings
- **User Management**: Sub-admin creation and management
- **Reports**: Date-wise booking reports and analytics

## 🛠️ **Technology Stack**

### **Frontend**
- **HTML5** - Modern semantic markup
- **Bootstrap 4** - Responsive CSS framework
- **JavaScript/jQuery** - Interactive features and AJAX
- **Font Awesome** - Professional icons
- **Chart.js** - Data visualization for analytics

### **Backend**
- **PHP 7+** - Server-side scripting
- **MySQL** - Database management
- **AdminLTE 3** - Admin dashboard framework

### **Third-party Integrations**
- **Payment Gateways**: Stripe, PayPal
- **DatePicker**: Bootstrap DatePicker
- **DataTables**: Advanced table functionality

## 📋 **Requirements**

### **System Requirements**
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache/Nginx
- **Extensions**: mysqli, gd, curl

### **Development Environment**
- **XAMPP/WAMP/LAMP** for local development
- **Git** for version control
- **Code Editor**: VS Code (recommended)

## 🚀 **Installation**

### **1. Clone Repository**
```bash
git clone https://github.com/bajunii/Boat-booking-system.git
cd Boat-booking-system
```

### **2. Database Setup**
1. Create a MySQL database named `bbsdb`
2. Import the main database structure:
   ```sql
   mysql -u username -p bbsdb < "SQL File/bbsdb.sql"
   ```
3. Import enhanced features (optional):
   ```sql
   mysql -u username -p bbsdb < "SQL File/enhanced_features.sql"
   mysql -u username -p bbsdb < "SQL File/booking_enhancement.sql"
   ```

### **3. Configuration**
1. Configure database connection in `bbs/includes/config.php`:
   ```php
   $con = mysqli_connect("localhost", "username", "password", "bbsdb");
   ```
2. Update admin configuration in `bbs/admin/includes/config.php`

### **4. File Permissions**
```bash
chmod 755 bbs/admin/images/
chmod 755 bbs/images/
```

### **5. Access Application**
- **Frontend**: `http://localhost/bbs/`
- **Admin Panel**: `http://localhost/bbs/admin/`
- **Default Admin**: Username: `admin`, Password: `admin123`

## 📁 **Project Structure**

```
Boat-Booking-System-PHP/
├── bbs/                          # Main application
│   ├── admin/                    # Admin panel
│   │   ├── includes/            # Admin includes
│   │   ├── plugins/             # AdminLTE plugins
│   │   ├── dist/                # AdminLTE distribution
│   │   ├── dashboard.php        # Admin dashboard
│   │   ├── manage-boat.php      # Boat management
│   │   ├── all-booking.php      # Booking management
│   │   ├── analytics-dashboard.php # Analytics & reports
│   │   └── ...
│   ├── includes/                # Shared includes
│   │   ├── config.php          # Database configuration
│   │   ├── navbar.php          # Navigation bar
│   │   ├── footer.php          # Footer
│   │   └── availability-functions.php # Booking validation
│   ├── css/                     # Stylesheets
│   │   ├── style.css           # Main styles
│   │   ├── custom-enhancements.css # Enhanced styles
│   │   └── ...
│   ├── js/                      # JavaScript files
│   ├── images/                  # Image uploads
│   ├── index.php               # Homepage
│   ├── book-boat.php           # Booking form
│   ├── payment.php             # Payment processing
│   ├── leave-review.php        # Review system
│   ├── check-availability.php   # Availability API
│   └── ...
├── SQL File/                    # Database files
│   ├── bbsdb.sql               # Main database
│   ├── enhanced_features.sql    # Enhanced features
│   └── booking_enhancement.sql  # Booking improvements
├── AVAILABILITY_SYSTEM.md       # Availability system docs
└── README.md                    # This file
```

## 🎮 **Usage Guide**

### **For Customers**
1. **Browse Boats**: View available boats with details and images
2. **Check Availability**: Use real-time availability checker
3. **Make Booking**: Fill booking form with personal details
4. **Process Payment**: Choose payment method and complete transaction
5. **Track Status**: Check booking status anytime
6. **Leave Review**: Rate and review your experience

### **For Administrators**
1. **Login**: Access admin panel with credentials
2. **Dashboard**: Monitor key metrics and statistics
3. **Manage Boats**: Add new boats, edit existing ones
4. **Process Bookings**: Accept or reject booking requests
5. **Moderate Reviews**: Approve customer reviews
6. **Generate Reports**: View analytics and export data

## 🔒 **Security Features**

- **SQL Injection Protection**: Prepared statements and validation
- **XSS Prevention**: Input sanitization and output encoding
- **Session Management**: Secure session handling
- **Admin Authentication**: Role-based access control
- **Payment Security**: Secure payment processing
- **File Upload Validation**: Image type and size restrictions

## 📊 **Key Features Detail**

### **Availability System**
- Real-time availability checking
- Boat capacity validation
- Date conflict prevention
- Booking locks for race conditions
- AJAX-powered instant feedback

### **Payment System**
- Multiple payment gateways
- Secure transaction processing
- Payment status tracking
- Order summary and receipts
- Refund management

### **Review System**
- 5-star rating interface
- Customer feedback collection
- Admin moderation
- Rating aggregation and display
- Review filtering and sorting

### **Analytics Dashboard**
- Revenue tracking and charts
- Booking trends analysis
- Customer satisfaction metrics
- Performance reports
- Data export capabilities

## 🐛 **Known Issues & Solutions**

### **Common Issues**
1. **Database Connection**: Verify config.php settings
2. **Image Upload**: Check folder permissions (755)
3. **Payment Gateway**: Configure API keys properly
4. **Session Issues**: Clear browser cache/cookies

### **Troubleshooting**
- Enable PHP error reporting for debugging
- Check MySQL error logs
- Verify file permissions
- Review browser console for JavaScript errors

## 🤝 **Contributing**

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 **Credits**

### **Development Team**
- **Main Developer**: [bajunii](https://github.com/bajunii)
- **Enhanced Features**: AI-assisted development

### **Third-party Libraries**
- **Bootstrap**: Responsive CSS framework
- **AdminLTE**: Admin dashboard template
- **Chart.js**: Data visualization
- **jQuery**: JavaScript library
- **Font Awesome**: Icon library

## 📞 **Support**

### **Contact Information**
- **Email**: haithamomar520@gmail.com
- **Phone**: +254 794320563
- **GitHub**: [bajunii/Boat-booking-system](https://github.com/bajunii/Boat-booking-system)

### **Documentation**
- [Availability System Documentation](AVAILABILITY_SYSTEM.md)
- [Database Schema](SQL%20File/)
- [API Documentation](docs/api.md) *(coming soon)*

## 🔄 **Version History**

### **v2.0.0** *(Current)*
- ✅ Enhanced availability checking system
- ✅ Payment integration with multiple gateways
- ✅ User reviews and ratings system
- ✅ Advanced analytics dashboard
- ✅ Responsive design improvements
- ✅ Security enhancements

### **v1.0.0** *(Initial)*
- ✅ Basic boat booking functionality
- ✅ Admin panel with boat management
- ✅ User registration and booking
- ✅ Basic reporting features

## 🚀 **Future Enhancements**

### **Planned Features**
- [ ] Mobile application (React Native)
- [ ] Email notification system
- [ ] SMS booking confirmations
- [ ] Calendar integration
- [ ] Multi-language support
- [ ] Advanced search and filtering
- [ ] Loyalty program
- [ ] API for third-party integrations

### **Technical Improvements**
- [ ] Migration to PHP 8+
- [ ] Laravel/CodeIgniter framework adoption
- [ ] RESTful API development
- [ ] Docker containerization
- [ ] CI/CD pipeline setup
- [ ] Automated testing suite

---

**Made with ❤️ for boat rental businesses worldwide**

*For more information, please refer to the documentation or contact our support team.*
