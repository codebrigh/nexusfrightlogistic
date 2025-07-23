# Nexus Fright Logistics Database Documentation

This document describes the MySQL database structure for the Nexus Fright logistics site, including table definitions, field descriptions, and instructions for importing and backing up the database.

---

## Database Name
`nexus_fright`

---

## Tables & Fields

### 1. `users`
| Field         | Type           | Description           |
|---------------|----------------|----------------------|
| id            | INT, PK, AI    | User ID (Primary Key) |
| email         | VARCHAR(100)   | User email (unique)   |
| phone         | VARCHAR(20)    | User phone number     |
| password_hash | VARCHAR(255)   | Hashed password       |
| created_at    | DATETIME       | Registration date     |

### 2. `packages`
| Field             | Type           | Description                        |
|-------------------|----------------|------------------------------------|
| id                | INT, PK, AI    | Package ID (Primary Key)           |
| tracking_number   | VARCHAR(50)    | Tracking number (unique)           |
| user_id           | INT, FK        | Linked user (nullable)             |
| status            | VARCHAR(50)    | Status (in transit, delivered, etc.)|
| last_update       | VARCHAR(255)   | Last update message                |
| estimated_delivery| DATETIME       | Estimated delivery date/time       |
| created_at        | DATETIME       | Created date                       |

### 3. `locations`
| Field      | Type           | Description                         |
|------------|----------------|-------------------------------------|
| id         | INT, PK, AI    | Location ID (Primary Key)           |
| user_id    | INT, FK        | Linked user (not null)              |
| latitude   | DECIMAL(10,7)  | Latitude                            |
| longitude  | DECIMAL(10,7)  | Longitude                           |
| updated_at | DATETIME       | Last update time                    |

---

## How to Import the Database

1. **Open your MySQL client** (phpMyAdmin, MySQL Workbench, or command line).
2. **Run the schema file:**
   - Command line example:
     ```
     mysql -u your_mysql_user -p < nexus_fright_schema.sql
     ```
   - Or use the import feature in your MySQL GUI tool.

---

## How to Back Up the Database

To create a backup of your database (structure + data):

```
mysqldump -u your_mysql_user -p nexus_fright > nexus_fright_backup.sql
```

This will create a file called `nexus_fright_backup.sql` in your current directory.

---

## Notes
- The actual database data is stored by MySQL, not in your project folder.
- The `nexus_fright_schema.sql` file contains only the structure (tables, fields, constraints).
- Log files for package and location updates are stored in `backend/logs/`.

---

For any questions or further customization, contact the project maintainer. 