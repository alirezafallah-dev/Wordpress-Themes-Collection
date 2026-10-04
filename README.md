# 🏛️ Dr. Mosalmani Law Firm — Custom WordPress Theme

<p align="center">
  <img src="assets/img/logo.png" alt="Dr. Mosalmani Logo" width="200"/>
</p>

<p align="center">
  <strong>A fully custom, RTL-optimized WordPress theme built for a professional law firm website in Iran.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/WordPress-Theme-21759B?style=flat&logo=wordpress&logoColor=white" alt="WordPress"/>
  <img src="https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white" alt="PHP"/>
  <img src="https://img.shields.io/badge/RTL-Fully%20Supported-FFD700?style=flat" alt="RTL"/>
  <img src="https://img.shields.io/badge/Multilingual-FA%20%7C%20EN%20%7C%20AR-green?style=flat" alt="Multilingual"/>
  <img src="https://img.shields.io/badge/License-MIT-blue.svg" alt="License"/>
  <img src="https://img.shields.io/badge/Responsive-Mobile%20First-brightgreen" alt="Responsive"/>
</p>

---

## 📖 Overview

**Dr. Mosalmani WordPress Theme** is a fully custom WordPress theme developed for a professional law firm specializing in civil, criminal, family, and corporate law. The theme was built from scratch with a focus on **modern UI/UX**, **performance**, and **legal industry-specific features**.

This project showcases advanced WordPress development skills including custom post types, authentication systems, booking platforms, and multilingual support.

### 🎯 Project Highlights

- **100% Custom Development** — No page builders or pre-made templates
- **RTL-First Approach** — Native Persian typography with IRANSansX & Vazir fonts
- **Advanced Authentication** — SMS OTP + Google OAuth integration
- **Booking System** — Complete consultation scheduling platform
- **Multilingual Support** — Three languages: Persian, English, and Arabic
- **SEO Optimized** — Schema.org markup and semantic HTML5

---

## ✨ Key Features

### 🔐 Authentication System

| Feature | Description |
|---|---|
| **SMS OTP Login** | Iranian phone number verification via SMS gateway |
| **Google OAuth** | One-click login with Google accounts |
| **User Registration** | Custom registration form with validation |
| **Profile Management** | User dashboard with appointment history |
| **Session Security** | Secure session handling and logout |

### 📅 Consultation Booking System

A complete appointment scheduling platform built from scratch.

#### Consultation Types

- Civil Litigation
- Criminal Defense
- Family Law
- Corporate Law
- Real Estate Law
- Commercial Contracts
- Dispute Resolution

#### Smart Scheduling

- Real-time availability checking
- Custom working hours configuration
- Time slot management
- Booking confirmation system

#### Admin Dashboard

- Booking management panel
- Consultation type configuration
- Working hours settings
- Client history tracking

### 🌐 Multilingual Support (i18n)

Full integration with the **Polylang** plugin.

| Language | Code | Direction | Typography |
|---|---|---|---|
| Persian (فارسی) | FA | RTL | IRANSansX FaNum |
| English | EN | LTR | Inter |
| Arabic (العربية) | AR | RTL | Vazir FD |

**Features:**

- Automatic RTL/LTR layout switching
- Language switcher with flag icons
- Separate content for each language
- Translatable theme options

### 🎨 Design & UX

- **Fully Responsive** — Mobile-first approach
- **RTL Optimized** — Right-to-left layouts
- **Modern UI** — Clean, professional design
- **Custom Assets**
  - SVG icons and illustrations
  - Web fonts: IRANSansX, Vazir, Inter
  - WebP images for performance
  - Custom background patterns
- **Smooth Animations** — CSS transitions and transforms
- **Accessibility** — WCAG-compliant markup

### ⚙️ WordPress Features

- Custom post types and taxonomies
- Advanced theme options panel
- Custom widgets and sidebars
- Breadcrumb navigation
- Custom comments system with admin moderation
- Terms & Conditions management
- Admin support ticket system
- Custom category and archive templates

### 🔍 SEO & Performance

- Semantic HTML5 structure
- Schema.org markup for legal services
- Optimized asset loading
- Lazy loading images
- SEO-friendly URL structure
- Meta tags optimization
- Open Graph tags for social sharing
- Fast page load times

---

## 🖥️ Page Templates

The theme includes custom templates for all major pages:

| Template | File | Description |
|---|---|---|
| **Homepage** | `index.php` | Hero section, services showcase, testimonials, CTA |
| **About Us** | `page-about-us.php` | Attorney profile, firm history, expertise areas |
| **Consultation Booking** | `page-consultation-booking.php` | Multi-step booking form with calendar |
| **Contact Us** | `page-contact-us.php` | Contact form, office locations, working hours |
| **Terms & Conditions** | `page-terms.php` | Legal terms, privacy policy |
| **User Account** | `page-account.php` | Dashboard, booking history, profile settings |
| **Single Post** | `single.php` | Blog post with comments |
| **Category Archive** | `category.php` | Blog category listings |
| **Comments** | `comments.php` | Custom threaded comments |

---

## 🛠️ Technology Stack

### Backend

- **CMS:** WordPress 5.8+
- **Language:** PHP 7.4+
- **Package Manager:** Composer
- **Authentication:** Custom JWT + Google OAuth 2.0

### Frontend

- **Markup:** HTML5 (Semantic)
- **Styling:** CSS3 (Custom, modular architecture)
- **JavaScript:** Vanilla JS (ES6+)
- **Fonts:**
  - IRANSansX FaNum (Persian)
  - Vazir FD (Arabic)
  - Inter (Latin)

### Plugins Integration

- **Polylang** — Multilingual support
- **Custom SMS Gateway** — OTP authentication
- **Google OAuth** — Social login

### Development Tools

- Git for version control
- Composer for dependency management
- VS Code for development

---

## 🚀 Installation

### Prerequisites

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.6+ or MariaDB 10.1+
- Composer

### Step-by-Step Setup

#### 1. Clone the Repository

```bash
git clone https://github.com/alirezafallah-dev/dr-mosalmani-wordpress-theme.git
```

#### 2. Upload to WordPress

Copy the theme folder to:

```text
wp-content/themes/
```

Optionally rename the folder to:

```text
dr-mosalmani
```

#### 3. Activate the Theme

In WordPress Admin, navigate to:

**Appearance → Themes**

Find **Dr. Mosalmani Theme** and click **Activate**.

#### 4. Install Required Plugins

Install and configure:

- **Polylang** — multilingual support
- **SMS Gateway** — OTP authentication

#### 5. Configure Theme Settings

##### a. Set Up Consultation Types

Go to:

**Appointments → Consultation Types**

Add consultation categories such as Civil, Criminal, Family, etc.

##### b. Configure Working Hours

Go to:

**Appointments → Working Hours**

Set available days and time slots.

##### c. Set Up Authentication

Configure SMS gateway credentials in the theme options.

Set up Google OAuth credentials in Google Cloud Console.

Add the required credentials to:

```text
inc/auth/google.php
inc/auth/sms.php
```

> **Security:** Do not commit API keys, OAuth secrets, or other credentials to Git. Use environment variables or a secure configuration mechanism whenever possible.

##### d. Add Languages via Polylang

Go to:

**Languages → Languages**

Add:

- Persian (FA)
- English (EN)
- Arabic (AR)

#### 6. Import Demo Content

Optionally create the following pages:

- Home
- About Us
- Contact
- Booking
- Terms

Assign the appropriate page templates from **Page Attributes**.

---

## ⚙️ Configuration

### SMS Authentication Setup

To enable SMS-based login for Iranian users:

1. Sign up for an Iranian SMS gateway service such as Kavenegar or SMS.ir.
2. Get your API key.
3. Add the required credentials to:

```text
inc/auth/sms.php
```

> Keep API credentials outside version control.

### Google OAuth Setup

1. Go to Google Cloud Console.
2. Create a new project.
3. Enable the required Google authentication APIs.
4. Create OAuth 2.0 credentials.
5. Set the authorized redirect URI:

```text
https://yourdomain.com/wp-admin/admin-ajax.php
```

6. Add the required credentials to:

```text
inc/auth/google.php
```

> Never commit the OAuth client secret to the repository.

### Consultation Booking Configuration

Customize the booking system from the WordPress admin:

- **Consultation Types** — Define service categories and durations
- **Working Hours** — Set available days and time slots
- **Admin Notifications** — Configure email/SMS notifications for new bookings
- **Booking Form** — Customize fields and validation rules

---

## 📸 Screenshots

> Screenshots will be added soon.

The theme features a modern, professional design optimized for legal services.

### Planned Screenshots

- 🏠 Homepage with hero section and services
- 📅 Consultation booking form
- 👤 User account dashboard
- 📱 Mobile responsive views
- 🌐 Multilingual language switcher

---

## 🎨 Design System

### Color Palette

The theme uses a professional color scheme suitable for legal services:

- **Primary:** Deep blue tones for trust and professionalism
- **Secondary:** Gold accents for a premium service feel
- **Neutral:** Clean whites and grays for readability
- **Accent:** Contrasting colors for CTAs and important elements

### Typography

| Language | Font Family | Weights | Usage |
|---|---|---|---|
| Persian | IRANSansX FaNum | Light to Black | Body text, headings |
| Arabic | Vazir FD | Thin to Black | Arabic content |
| Latin | Inter | Light to Bold | English content, UI elements |

### Spacing System

Consistent spacing using an **8px grid system** for visual harmony.

---

## 🧪 Browser Support

| Browser | Version |
|---|---|
| Chrome | Latest |
| Firefox | Latest |
| Safari | Latest |
| Edge | Latest |
| Opera | Latest |
| iOS Safari | 12+ |
| Android Chrome | Latest |

---

## 🤝 Contributing

This is a private client project. However:

- 🐛 **Bug Reports:** Please open an issue with a detailed description.
- 💡 **Suggestions:** Feel free to open discussion issues.
- 🔧 **Pull Requests:** Contact the maintainer before submitting.

---

## 👨‍💻 Developer

**Alireza Fallah**

- **GitHub:** [@alirezafallah-dev](https://github.com/alirezafallah-dev)
- **Email:** [alirezafallah.dev@gmail.com](mailto:alirezafallah.dev@gmail.com)

Built with ❤️ using modern WordPress development practices.

---

## 📄 License

This project is licensed under the **MIT License**. See the [`LICENSE`](LICENSE) file for details.

---

## 🙏 Acknowledgments

- **WordPress Community** — For the amazing CMS platform
- **Font Developers** — IRANSansX, Vazir, and Inter font families
- **Polylang Team** — For excellent multilingual support

---

## 📞 Support

For questions, issues, or custom development inquiries:

- 📧 **Email:** [alirezafallah.dev@gmail.com](mailto:alirezafallah.dev@gmail.com)
- 🐙 **GitHub:** Open an issue in this repository

<p align="center">
  <strong>⭐ If you found this project helpful, please consider giving it a star!</strong>
</p>

<p align="center">
  <a href="https://github.com/alirezafallah-dev/dr-mosalmani-wordpress-theme">View on GitHub</a> •
  <a href="#installation">Installation Guide</a> •
  <a href="#key-features">Features</a> •
  <a href="#technology-stack">Tech Stack</a>
</p>
