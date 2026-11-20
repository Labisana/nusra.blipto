#!/bin/bash
set -e

# ==========================================================
# Laravel Production Setup Script for VPS
# Supports Ubuntu 20.04/22.04/24.04 & Debian 11/12
# Nginx + PHP 8.4 + MySQL/MariaDB + Node.js 22 + Composer 2
# ==========================================================

export DEBIAN_FRONTEND=noninteractive

echo "=========================================="
echo " 1. Mendeteksi Sistem Operasi"
echo "=========================================="
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$ID
    CODENAME=$VERSION_CODENAME
fi

OS="${OS:-ubuntu}"
CODENAME="${CODENAME:-$(lsb_release -sc 2>/dev/null || echo 'jammy')}"

echo "OS terdeteksi: $OS ($CODENAME)"

echo "=========================================="
echo " 2. Memperbarui Paket Dasar & Sertifikat"
echo "=========================================="
sudo apt-get update -y
sudo apt-get install -y curl git unzip zip software-properties-common ca-certificates apt-transport-https gnupg lsb-release ufw

echo "=========================================="
echo " 3. Memasang Repository PHP 8.4"
echo "=========================================="
# Bersihkan repository lama jika ada konflik
sudo rm -f /etc/apt/sources.list.d/ondrej-ubuntu-php-resolute.sources 2>/dev/null || true

if [ "$CODENAME" = "resolute" ] || [ "$OS" = "debian" ]; then
    echo "Menambahkan Sury PHP repo untuk $OS ($CODENAME)..."
    sudo curl -sSLo /etc/apt/trusted.gpg.d/sury-php.gpg https://packages.sury.org/php/apt.gpg
    echo "deb https://packages.sury.org/php/ ${CODENAME} main" | sudo tee /etc/apt/sources.list.d/sury-php.list
    sudo apt-get update -y
elif [ "$OS" = "ubuntu" ]; then
    echo "Menambahkan repo PHP untuk Ubuntu ($CODENAME)..."
    sudo curl -sSLo /etc/apt/trusted.gpg.d/sury-php.gpg https://packages.sury.org/php/apt.gpg
    echo "deb https://packages.sury.org/php/ ${CODENAME} main" | sudo tee /etc/apt/sources.list.d/sury-php.list
    sudo apt-get update -y
    # Jika gagal mencari paket, fallback ke PPA ondrej
    if ! apt-cache show php8.4-fpm &>/dev/null; then
        echo "Mencoba fallback PPA ondrej/php..."
        sudo LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php || true
        sudo apt-get update -y
    fi
else
    echo "Distro lain terdeteksi ($OS / $CODENAME)..."
    sudo curl -sSLo /etc/apt/trusted.gpg.d/sury-php.gpg https://packages.sury.org/php/apt.gpg || true
    echo "deb https://packages.sury.org/php/ ${CODENAME} main" | sudo tee /etc/apt/sources.list.d/sury-php.list || true
    sudo apt-get update -y
fi

echo "=========================================="
echo " 4. Memasang Nginx & Database Server"
echo "=========================================="
sudo apt-get install -y nginx
sudo apt-get install -y default-mysql-server || sudo apt-get install -y mysql-server || sudo apt-get install -y mariadb-server

echo "=========================================="
echo " 5. Memasang PHP 8.4 & Ekstensi Laravel"
echo "=========================================="
sudo apt-get install -y \
    php8.4-fpm \
    php8.4-cli \
    php8.4-common \
    php8.4-mysql \
    php8.4-mbstring \
    php8.4-xml \
    php8.4-curl \
    php8.4-zip \
    php8.4-bcmath \
    php8.4-intl \
    php8.4-gd \
    php8.4-sqlite3

echo "=========================================="
echo " 6. Memasang Composer 2"
echo "=========================================="
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
fi

echo "=========================================="
echo " 7. Memasang Node.js 22 LTS"
echo "=========================================="
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
    sudo apt-get install -y nodejs
fi

echo "=========================================="
echo " 8. Menyiapkan Database"
echo "=========================================="
sudo systemctl start mariadb 2>/dev/null || sudo systemctl start mysql 2>/dev/null || true
sudo systemctl enable mariadb 2>/dev/null || sudo systemctl enable mysql 2>/dev/null || true

DB_NAME="nusra_db"
DB_USER="nusra_user"
DB_PASS="BlipTo_SecurePass2026!"

sudo mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -e "ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

echo "=========================================="
echo " 9. Clone Repository Proyek"
echo "=========================================="
WEB_DIR="/var/www/nusra.blipto"
sudo mkdir -p /var/www

if [ -d "$WEB_DIR" ]; then
    echo "Folder proyek sudah ada, memperbarui kode..."
    cd "$WEB_DIR"
    sudo git pull origin main
else
    echo "Mengunduh repository dari GitHub..."
    sudo git clone https://github.com/Labisana/nusra.blipto.git "$WEB_DIR"
fi

cd "$WEB_DIR"

echo "=========================================="
echo " 10. Konfigurasi Environment (.env)"
echo "=========================================="
if [ ! -f "$WEB_DIR/.env" ]; then
    sudo cp "$WEB_DIR/.env.example" "$WEB_DIR/.env"
fi

sudo sed -i 's/APP_ENV=.*/APP_ENV=production/' "$WEB_DIR/.env"
sudo sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' "$WEB_DIR/.env"
sudo sed -i 's|APP_URL=.*|APP_URL=http://103.217.144.69|' "$WEB_DIR/.env"
sudo sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' "$WEB_DIR/.env"
sudo sed -i 's/# DB_HOST=.*/DB_HOST=127.0.0.1/' "$WEB_DIR/.env"
sudo sed -i 's/DB_HOST=.*/DB_HOST=127.0.0.1/' "$WEB_DIR/.env"
sudo sed -i 's/# DB_PORT=.*/DB_PORT=3306/' "$WEB_DIR/.env"
sudo sed -i 's/DB_PORT=.*/DB_PORT=3306/' "$WEB_DIR/.env"
sudo sed -i 's/# DB_DATABASE=.*/DB_DATABASE=nusra_db/' "$WEB_DIR/.env"
sudo sed -i 's/DB_DATABASE=.*/DB_DATABASE=nusra_db/' "$WEB_DIR/.env"
sudo sed -i 's/# DB_USERNAME=.*/DB_USERNAME=nusra_user/' "$WEB_DIR/.env"
sudo sed -i 's/DB_USERNAME=.*/DB_USERNAME=nusra_user/' "$WEB_DIR/.env"
sudo sed -i "s/# DB_PASSWORD=.*/DB_PASSWORD=${DB_PASS}/" "$WEB_DIR/.env"
sudo sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASS}/" "$WEB_DIR/.env"
sudo sed -i 's/SESSION_DRIVER=.*/SESSION_DRIVER=file/' "$WEB_DIR/.env"
sudo sed -i 's/CACHE_STORE=.*/CACHE_STORE=file/' "$WEB_DIR/.env"

if ! grep -q "^APP_KEY=base64:" "$WEB_DIR/.env"; then
    sudo sed -i 's/APP_KEY=.*/APP_KEY=base64:Ea4pJDGCdMo\/yqnIB2\/FYW2YlLjfWNpdItxe9D3uiug=/' "$WEB_DIR/.env"
fi

echo "=========================================="
echo " 11. Menginstal Dependensi PHP & Frontend"
echo "=========================================="
export COMPOSER_ALLOW_SUPERUSER=1
sudo -E composer install --no-dev --optimize-autoloader --no-interaction
sudo npm install
sudo npm run build

echo "=========================================="
echo " 12. Menjalankan Migrasi Database"
echo "=========================================="
sudo php artisan storage:link || true
sudo php artisan migrate --force --seed || sudo php artisan migrate --force

echo "=========================================="
echo " 13. Mengatur Izin Folder (Permissions)"
echo "=========================================="
sudo chown -R www-data:www-data "$WEB_DIR"
sudo find "$WEB_DIR" -type f -exec chmod 644 {} \;
sudo find "$WEB_DIR" -type d -exec chmod 755 {} \;
sudo chmod -R 775 "$WEB_DIR/storage" "$WEB_DIR/bootstrap/cache"

echo "=========================================="
echo " 14. Konfigurasi Nginx Server Block"
echo "=========================================="
NGINX_CONF="/etc/nginx/sites-available/nusra.blipto"

sudo tee "$NGINX_CONF" > /dev/null << 'EOF'
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    server_name 103.217.144.69 nusratim.my.id www.nusratim.my.id;
    root /var/www/nusra.blipto/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;
    charset utf-8;

    client_max_body_size 64M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

sudo rm -f /etc/nginx/sites-enabled/default
sudo ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/

sudo nginx -t
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx
sudo systemctl enable nginx
sudo systemctl enable php8.4-fpm

sudo ufw allow 'Nginx Full' 2>/dev/null || true
sudo ufw allow 22/tcp 2>/dev/null || true

echo ""
echo "=========================================================="
echo " SELAMAT! INSTALASI & SETUP SELESAI DENGAN SUKSES! 🎉"
echo "=========================================================="
echo " Website Anda sekarang sudah aktif dan bisa diakses di:"
echo " 👉 http://103.217.144.69"
echo " 👉 http://nusratim.my.id (setelah A-record domain diarahkan)"
echo ""
echo " Detail Database MySQL:"
echo " - Database: nusra_db"
echo " - User:     nusra_user"
echo " - Password: ${DB_PASS}"
echo "=========================================================="
