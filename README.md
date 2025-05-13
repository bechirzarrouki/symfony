# 🌐 InvestConnect - Social Media Platform for Investors, Workers, and Companies

**InvestConnect** is a comprehensive social media platform that connects **investors**, **workers**, and **companies** through a shared ecosystem of ideas, learning, and investment opportunities. Built with **Symfony** for the web interface and **JavaFX** for the desktop version, the application serves as a multi-functional collaboration hub.

---

## 🚀 Key Features

### 🔐 User Management
- Secure user registration and login
- Roles: Investor, Worker, Project Owner, Admin
- Profile customization

### 📚 Course Management (Gestion Cours)
- Browse and enroll in professional courses
- Take **quizzes** after each course
- Receive **certificates** upon successful completion

### 💼 Investor Management (Gestion Investors)
- Investors can post **investment propositions**
- View and filter propositions by interest or sector
- Workers and companies can apply or show interest

### 💡 Project Management (Gestion Projets)
- Project holders can post **project ideas**
- Receive feedback, collaboration offers, or investments

### 📝 Social Feed (Gestion Posts)
- Create posts just like **LinkedIn**
- Like, comment, and share
- Professional networking and engagement

---

## 🖥️ Platforms

### 🌍 Web Version
- **Framework**: Symfony (PHP)
- **Features**: Full access to all modules via browser
- **Target**: General users accessing through a website

### 💻 Desktop Version
- **Framework**: JavaFX
- **Features**: Smooth desktop experience with offline-friendly features
- **Target**: Users needing advanced functionalities locally

---

## 🧰 Technologies Used

- **Backend**: PHP (Symfony)
- **Frontend (Web)**: Twig, HTML, CSS, JavaScript
- **Frontend (Desktop)**: JavaFX
- **Database**: MySQL / PostgreSQL
- **Others**: REST API, JWT Authentication (if applicable)

---

## 📦 Installation & Setup

### Symfony Web App

```bash
git clone https://github.com/yourusername/investconnect.git
cd investconnect/web
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console server:run
