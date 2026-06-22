CDCMS — Cricket Drafting Ceremony Management System

LEGAL AND AUTHORIZATION NOTICE

Copyright © 2026 Mehran Khan. All rights reserved.
This software is strictly proprietary. Unauthorized copying, modification, distribution, or use of this system, via any medium, is strictly prohibited.
You cannot use this system without explicit written permission.

For permissions contact:
Phone: +92 3436782451
Email: [mehrankhankhan476@gmail.com](mailto:mehrankhankhan476@gmail.com)

A production-ready Laravel application for running a live multi-team cricket player draft system including registration, admin approval, categorization, real-time drafting ceremony, and match/stat tracking.

TECH STACK

Backend: Laravel 11 (PHP 8.2+)
Database: MySQL 8 (fully normalized, see database/cricket_draft_system.sql)
Frontend: Blade, Bootstrap 5, vanilla JavaScript
Charts: Chart.js
Real-time: Laravel Broadcasting via Pusher with polling fallback if Pusher is not configured


INSTALLATION

Run the following commands:

composer install
cp .env.example .env
php artisan key:generate

Then configure your .env file with database credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD).
Optionally configure Pusher credentials for real-time updates.

DATABASE SETUP

Option A (Recommended): Laravel migrations
php artisan migrate
php artisan db:seed

Option B: Direct SQL import
Import database/cricket_draft_system.sql using phpMyAdmin or run:

mysql -u root -p < database/cricket_draft_system.sql

This will create all tables, foreign keys, indexes, and seed the admin user and 6 draft categories.


STORAGE LINK (for uploads)

php artisan storage:link

RUN APPLICATION

php artisan serve

Open in browser:
[http://localhost:8000](http://localhost:8000)


REAL-TIME DRAFT (PUSHER)

Set the following in .env:

PUSHER_APP_ID=...
PUSHER_APP_KEY=...
PUSHER_APP_SECRET=...
PUSHER_APP_CLUSTER=...

If not configured, the system will automatically use polling via /api/draft/{id}/poll as a fallback.

SCHEDULER (AUTO SKIP EXPIRED TURNS)

Add this cron job on server:

cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1



Admin login details 
Email: admin@cdcms.com
Password: Admin@123


HOW THE DRAFT ENGINE WORKS

1. Admin creates a draft session and sets per-pick timer (example 300 seconds)
2. Admin starts the draft
3. System selects the first active category based on draft order and shuffles teams into pick order
4. Each team picks one player in turn from available players in that category
5. Server validates every action to ensure correct turn, valid player, and correct category
6. If a team’s timer expires, the turn is automatically skipped via browser, admin action, or scheduled command
7. After all teams complete a round, system moves to the next category and generates a new random order
8. After final category is completed, draft session is marked as completed and event is broadcast
9. All actions are logged in audit_logs table



FOLDER STRUCTURE

app/
Events/ → Broadcasting events
Http/Controllers/Admin/ → Admin dashboard, controls
Http/Controllers/Player/ → Player dashboard and registration
Http/Controllers/Team/ → Team dashboard and draft room
Http/Controllers/Auth/ → Authentication
Http/Middleware/ → Role middleware
Models/ → Eloquent models
Services/DraftEngine.php → Core draft logic

database/
migrations/ → Database schema
seeders/ → Seed data
cricket_draft_system.sql → Full SQL dump

resources/views/
admin/ → Admin interface
player/ → Player interface
team/ → Team interface
public/ → Landing pages

routes/
web.php → Web routes
channels.php → Broadcasting channels
console.php → Scheduled commands



NOTES

Default placeholder images should be placed in public/images:
images/default-player.png
images/default-team.png

File uploads are stored in:
storage/app/public/

Accessible via:
php artisan storage:link


SYSTEM METADATA

Project Name: cricket-draft-system
Build Signature: 5817ece78617455c35e3139f973ea34e11206927
License Holder: Mehran Khan
Phone: +92 3436782451
Email: [mehrankhankhan476@gmail.com](mailto:mehrankhankhan476@gmail.com)

