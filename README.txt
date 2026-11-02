MQ WebAI Studio — PHP + MySQL (WordPress jaisa Admin Panel)
============================================================

ADMIN LOGIN
  URL      : https://aapki-site.com/admin/login.php
  Username : WebAI
  Password : Mehboob@1234
  (Password database mein hashed save hota hai. Login ke baad Account page se badal lein.)

INSTALL (cPanel / InfinityFree / Hostinger)
  1. Hosting mein MySQL database + user banayein (cPanel > MySQL Databases).
  2. Zip ka saara content public_html (ya kisi folder) mein upload + extract karein.
  3. Browser mein kholein:  https://aapki-site.com/install.php
     Host (aam taur par localhost), Database name, DB user, DB password dein -> Install.
  4. Install ke baad install.php delete kar dein.
  5. /admin/login.php par login karein.

INSTALL (XAMPP, apne computer par)
  1. Zip ko C:\xampp\htdocs\mq mein extract karein. XAMPP mein Apache + MySQL start karein.
  2. http://localhost/mq/install.php  -> host: localhost, user: root, password: khali,
     database name: mq_webai (khud ban jayega) -> Install.
  3. http://localhost/mq/admin/login.php

ADMIN PANEL MEIN KYA KAR SAKTE HAIN
  Posts            : nayi post add / edit / delete / draft-publish (editor + image upload, cover image)
  Website Content  : What We Build, Web, App, Software, AI, IT & Networking cards, CEO projects
                     — add / edit / delete / order
  Site Settings    : CEO naam, email, WhatsApp, address, bio, Facebook pages, hero image,
                     CEO portfolio HTML replace
  Enquiries        : "Hire Me" form ke saare messages (read / reply / delete)
  Account          : username aur password change

FOLDERS
  index.php, blog.php   public website       admin/   admin panel
  api/enquiry.php       Hire Me form handler inc/     core code (web se band)
  assets/               css, js, hero image, CEO portfolio     uploads/  admin ki uploaded files

REQUIREMENTS : PHP 7.4+ (extensions: pdo_mysql, mbstring, dom), MySQL/MariaDB, Apache.
NOTE         : .htaccess files config.php aur inc/ ko band rakhti hain. Agar server 500 error de
               (purana Apache 2.2 / Nginx), to .htaccess files delete karke hosting panel se
               config.php aur inc/ folder ka direct access block karwa lein.
