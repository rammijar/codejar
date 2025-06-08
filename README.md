```markdown
# CodeJar - Developer Community Platform

![CodeJar Banner](https://codejar.infy.uk/assets/images/banner.png)

A professional platform for developers to share code, build portfolios, and receive donations. Empowering creators through open-source collaboration and financial support.

🌐 **Live Demo**: [https://codejar.infy.uk/](https://codejar.infy.uk/)  
📦 **GitHub**: [https://github.com/rammijar/codejar](https://github.com/rammijar/codejar)

## 🚀 Key Features

### For Developers
- 🧑‍💻 **Portfolio Profiles**: Showcase your projects and skills
- 📦 **Code Sharing**: Upload and version control ZIP packages
- 💰 **Monetization**: Accept donations via Khalti/eSewa
- 🔗 **Social Integration**: Connect GitHub, Twitter, etc.
- 📊 **Analytics**: Track downloads and donations

### For Admins
- 👥 **User Management**: Comprehensive admin dashboard
- 🛡️ **Content Moderation**: Approve/reject submissions
- 💸 **Revenue Reports**: Detailed transaction tracking
- ⚙️ **System Configuration**: Platform settings

## 🛠 Technology Stack

| Category        | Technologies                          |
|-----------------|---------------------------------------|
| **Frontend**    | HTML5, CSS3, JavaScript, Animate.css  |
| **Backend**     | PHP 8.1, MySQL 8.0                    |
| **Payment**     | Khalti API, eSewa API                 |
| **Security**    | CSRF protection, PDO prepared statements |
| **DevOps**      | Apache/Nginx, Linux hosting           |

## 📦 Installation

### Prerequisites
- PHP 8.0+
- MySQL 8.0+
- Composer (for dependencies)
- Web server with HTTPS

### Quick Setup
```bash
# Clone repository
git clone https://github.com/rammijar/codejar.git
cd codejar

# Install dependencies
composer install

# Set up database (see database/schema.sql)
mysql -u root -p < database/schema.sql

# Configure environment
cp .env.example .env
nano .env  # Update with your credentials

# Set permissions
chmod -R 775 assets/uploads/
```

## 🌐 Deployment

### Recommended Hosting
- **Shared Hosting**: cPanel with PHP 8.0+
- **VPS**: Ubuntu 20.04 LTS with LAMP stack
- **Cloud**: AWS Lightsail or DigitalOcean Droplet

### Configuration Checklist
1. Set up cron job for maintenance tasks
2. Configure SMTP for email notifications
3. Enable OPcache for PHP performance
4. Set up daily database backups

## 💳 Payment Integration

### Khalti Setup
1. Register at [Khalti Merchant Portal](https://merchant.khalti.com/)
2. Add to `.env`:
   ```ini
   KHALTI_PUBLIC_KEY=test_public_key_...
   KHALTI_SECRET_KEY=test_secret_key_...
   ```

### eSewa Setup
1. Apply at [eSewa Developer Portal](https://developer.esewa.com.np/)
2. Add to `.env`:
   ```ini
   ESEWA_MERCHANT_ID=EPAYTEST
   ESEWA_SECRET_KEY=8gBm/:&EnhH.1/q
   ```

## 🏗 Project Structure

```
codejar/
├── assets/               # Static assets
│   ├── dist/            # Compiled assets
│   └── src/             # Source files
├── config/              # Configuration
├── database/            # SQL schemas/migrations
├── includes/            # Core PHP classes
│   ├── Auth/           # Authentication
│   ├── Payments/       # Gateway integrations
│   └── Utilities/      # Helpers
├── public/              # Document root
├── tests/               # PHPUnit tests
├── .env.example         # Environment template
├── LICENSE              # MIT License
└── composer.json        # PHP dependencies
```

## 🤝 Contributing

We welcome contributions! Please follow our guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -m 'Add some feature'`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Open a Pull Request

## 📜 License

MIT License - See [LICENSE](LICENSE) for details.

## 📬 Contact

**Project Maintainer**: Rammijar  
**Email**: contact@codejar.infy.uk  
**GitHub**: [github.com/rammijar](https://github.com/rammijar)

---

<div align="center">
  <sub>Built with ❤️ by <a href="https://github.com/rammijar">Rammijar</a></sub>
</div>
```
