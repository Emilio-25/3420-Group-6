# StatTrax - Phase 4 LAMP Starter

This package implements the CMPS 3420 Phase 4 database-programming requirements for the StatTrax League of Legends stat tracker/community database.

## Included

- MySQL schema with foreign-key `ON UPDATE` / `ON DELETE` behavior
- 3 views
  - `PlayerMatchSummary`
  - `MediaEngagement`
  - `PendingReports`
- 4 stored procedures
  - `CreateMediaPost` (INSERT)
  - `DeleteComment` (DELETE)
  - `UpdateReportStatus` (UPDATE)
  - `GetPlayerStatistics` (statistics over a date range)
- 3 explicit triggers
  - `ReportAfterInsert`
  - `ReportAfterUpdate`
  - `ReportAfterDelete`
- `ReportHistory` shadow table for trigger auditing
- Demo data and SQL verification script
- PHP pages that exercise the views/procedures/triggers
- Phase 4 report section in DOCX and PDF form

## LAMP setup (Ubuntu/Debian example)

1. Install Apache, MySQL, and PHP:

   ```bash
   sudo apt update
   sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql
   ```

2. Create the database and objects:

   ```bash
   mysql -u root -p < database/00_full_install.sql
   ```

3. Copy this project under Apache's web root. For example:

   ```bash
   sudo cp -r StatTrax_LAMP_Phase4 /var/www/html/stattrax
   ```

4. Configure database credentials with environment variables if needed:

   - `STATTRAX_DB_HOST` (default: `localhost`)
   - `STATTRAX_DB_NAME` (default: `stattrax`)
   - `STATTRAX_DB_USER` (default: `root`)
   - `STATTRAX_DB_PASS` (default: empty)

5. Browse to:

   `http://localhost/stattrax/public/index.php`

## Recommended screenshots for the report

Use the pages in `public/` or phpMyAdmin/MySQL Workbench to capture:

1. `PlayerMatchSummary` result
2. `MediaEngagement` result
3. `PendingReports` result
4. `CreateMediaPost` before/after
5. `DeleteComment` before/after
6. `UpdateReportStatus` before/after
7. `GetPlayerStatistics` returned row
8. INSERT trigger + `ReportHistory`
9. UPDATE trigger + `ReportHistory`
10. DELETE trigger + `ReportHistory`

The report files include labeled evidence placeholders so you know where each screenshot goes.

## Important

The SQL is built from the StatTrax relational model used in the project conversations. If your group's Phase 3 implementation changed any table or column names, update the scripts before importing.

This is an educational prototype. Add authentication/authorization before exposing the PHP pages to the public Internet.
