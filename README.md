# 🚀 Airdrop Portal

A comprehensive cryptocurrency airdrop management portal built with Laravel, featuring modern dark/light themes, multi-blockchain support, and advanced admin management capabilities.

## 🌟 Features

### 🎯 Core Features
- **Multi-blockchain Support**: Ethereum, Solana, Cosmos, Polygon, BSC, Arbitrum
- **Dark/Light Theme**: Automatic system detection with manual toggle
- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Multi-language**: Built-in translation system with AI-powered translations
- **Advanced Search & Filtering**: Find airdrops by blockchain, status, dates, and more

### 👤 User Features
- **Wallet Authentication**: MetaMask, Phantom, Keplr wallet integration
- **Social Login**: Google, Facebook, Twitter OAuth
- **Notification System**: Email alerts for airdrop updates
- **Favorites & Subscriptions**: Track and follow interesting airdrops
- **User Profiles**: Manage wallets, preferences, and settings

### 🛠️ Admin Features
- **Complete CRUD**: Manage airdrops, projects, blockchains, and categories
- **Phase Management**: Multiple seasons/phases per airdrop
- **AI Translations**: Automatic content translation via ChatGPT API
- **Media Management**: Image uploads for projects and airdrops
- **Analytics Dashboard**: Comprehensive statistics and insights
- **User Management**: Admin and user role management

## 🏗️ Technology Stack

- **Backend**: PHP 8.2+ with Laravel Framework
- **Database**: MySQL 8.0+ with Redis caching
- **Frontend**: HTML5, CSS3, JavaScript with Alpine.js
- **Styling**: Tailwind CSS with dark mode support
- **Containerization**: Docker with multi-stage builds
- **Web Server**: Nginx with PHP-FPM
- **Process Manager**: Supervisor for background tasks

## 🚀 Quick Start with Docker

### Prerequisites
- Docker & Docker Compose
- Git

### Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd airdrop-portal
```

2. **Run the initialization script**
```bash
chmod +x init.sh
./init.sh
```

3. **Access the application**
- Frontend: http://localhost:8080
- Admin Panel: http://localhost:8080/admin
- Mailhog: http://localhost:8025

### Default Admin Access
- **Email**: admin@example.com
- **Password**: admin123
- ⚠️ **Important**: Change password on first login!

## 📁 Project Structure

```
airdrop-portal/
├── app/                    # Laravel application
│   ├── Http/Controllers/   # Controllers
│   ├── Models/            # Eloquent models
│   └── ...
├── database/              # Database files
│   ├── migrations/        # Database migrations
│   └── seeders/          # Database seeders
├── docker/               # Docker configuration
│   ├── nginx/            # Nginx configuration
│   └── supervisor/       # Supervisor configuration
├── resources/            # Frontend resources
│   ├── views/            # Blade templates
│   └── js/              # JavaScript files
├── scripts/              # Utility scripts
│   ├── backup.sh         # Database backup
│   └── restore.sh        # Database restore
├── docker-compose.yml    # Docker services
├── Dockerfile           # Application container
└── init.sh             # Initialization script
```

## 🔧 Configuration

### Environment Variables
Copy `.env.example` to `.env` and configure:

```env
# Database
DB_HOST=db
DB_DATABASE=airdrop_portal
DB_USERNAME=airdrop_user
DB_PASSWORD=airdrop_password

# AI Translation
OPENAI_API_KEY=your_openai_api_key

# Social Login
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret

# Email
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

## 🗃️ Database Schema

The application includes comprehensive database migrations for:

- **Users & Authentication**: User accounts, wallets, social logins
- **Content Management**: Airdrops, projects, blockchains, categories
- **Localization**: Multi-language support with translations
- **Notifications**: User subscriptions and alerts
- **Analytics**: View tracking and user engagement

## 📚 API Documentation

The portal includes RESTful APIs for:

- Airdrop management
- User authentication
- Wallet integration
- Notification services

API documentation is available at `/api/documentation` when enabled.

## 🛡️ Security Features

- **CSRF Protection**: Laravel's built-in CSRF protection
- **XSS Prevention**: Content Security Policy headers
- **SQL Injection Protection**: Eloquent ORM with prepared statements
- **Rate Limiting**: API and form submission limits
- **Secure File Uploads**: Validation and virus scanning
- **GDPR Compliance**: Data export and deletion capabilities

## 🚀 Deployment

### Development
```bash
docker-compose up -d
```

### Production
1. Update environment variables for production
2. Enable SSL/TLS certificates
3. Configure proper backup strategies
4. Set up monitoring and alerting

### Scaling
The application is designed for horizontal scaling:
- Load balancer compatible
- Stateless session management
- Redis clustering support
- Database read replicas

## 🔧 Maintenance

### Database Backup
```bash
./scripts/backup.sh
```

### Database Restore
```bash
./scripts/restore.sh backups/backup_file.sql.gz
```

### Update Application
```bash
docker-compose down
git pull
docker-compose up -d --build
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Check the documentation
- Review existing issues
- Create a new issue with detailed information

## 🔮 Roadmap

- [ ] Mobile applications (iOS/Android)
- [ ] Advanced analytics dashboard
- [ ] Telegram/Discord bot integration
- [ ] Portfolio tracking features
- [ ] White-label solutions
- [ ] API marketplace integration

---

**Built with ❤️ for the crypto community**
