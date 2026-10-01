# Customer List with Pagination and Search Filters

A simple PHP + MySQL app that lists customers from `tbl_customers`, 10 per page, with a single search box (First Name / Last Name / City / Country) and a Reset button.

## Project structure

```
customer-list/
├── index.php    # Customers list, search, pagination
├── db.php       # PDO database connection
├── style.css    # Responsive styling
└── README.md
```

## Features

- Real MySQL data (no hardcoded rows), 10 customers per page
- Previous / page numbers / Next pagination that keeps the search text
- Search across first name, last name, city and country
- Reset clears the search and returns to page 1
- "No customers found." message when nothing matches
- PDO prepared statements (SQL injection safe) and output escaping (XSS safe)
- Friendly database connection error message
- UTF-8 (utf8mb4) so characters like é, ü, ã, Å display correctly
- Responsive layout for desktop and mobile

## 1. Create the database (XAMPP / phpMyAdmin)

1. Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
2. Go to `http://localhost/phpmyadmin`.
3. Click **New** in the left sidebar.
4. Database name: `ultimez_interview_tasks`
5. Collation: `utf8mb4_general_ci` (or `utf8mb4_unicode_ci`), then click **Create**.

## 2. Import the supplied SQL file

1. Select the `ultimez_interview_tasks` database in phpMyAdmin.
2. Click the **Import** tab.
3. Click **Choose File** and select `ultimez_interview_tasks.sql` (included in this folder, built from the supplied dataset).
4. Make sure the format is **SQL**, then click **Import**.
5. Open the `tbl_customers` table and confirm the rows are there.

> Do not edit or delete the imported records.

## 3. Configure the database connection

Open `db.php` and check these values (defaults work for XAMPP):

```php
$host   = 'localhost';
$dbname = 'ultimez_interview_tasks';
$user   = 'root';
$pass   = '';
```

## 4. Run the project in XAMPP

1. Copy the `customer-list` folder into `C:\xampp\htdocs\`
   (macOS: `/Applications/XAMPP/htdocs/`).
2. Make sure Apache and MySQL are running.
3. Open `http://localhost/customer-list/` in your browser.

## 5. Test search and pagination

- **Pagination:** the first page shows 10 customers, Sl. No. 1-10. Click **Next** or a page number; page 2 starts at Sl. No. 11.
- **Search:** type `Germany` and click **Search**. Only matching customers appear, still 10 per page. Click page 2 and the search stays applied.
- **Other fields:** try a first name, a last name and a city.
- **No results:** search `zzzzz`. You should see "No customers found."
- **Reset:** click **Reset**. The search box clears, you return to page 1, and the full list is shown.
- **Special characters:** search for names with accents (e.g. `é`, `ü`, `Å`) and check they display correctly.
- **Mobile:** narrow the browser window (or use DevTools device mode) to check the responsive layout.

## 6. Upload to GitHub

1. Create a new empty repository on GitHub (e.g. `customer-list`).
2. Open a terminal in the project folder and run:

```bash
cd customer-list
git init
git add .
git commit -m "Customer list with pagination and search"
git branch -M main
git remote add origin https://github.com/<your-username>/customer-list.git
git push -u origin main
```

3. Refresh the GitHub page to confirm the files are there.
4. Optional: add the supplied SQL file to the repo so others can import it.
   Before pushing, replace your real DB password in `db.php` if you set one.

## Troubleshooting

| Problem | Fix |
|---|---|
| "Could not connect to the database" | Start MySQL in XAMPP and check `db.php` credentials |
| "tbl_customers could not be read" | Import the SQL file into `ultimez_interview_tasks` |
| Strange characters | Make sure the DB/table use utf8mb4 and the SQL file is imported as UTF-8 |
| 404 page | Confirm the folder is inside `htdocs` and the URL matches its name |
