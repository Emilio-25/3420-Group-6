# StatTrax — Phase 4 + Phase 5 LAMP Application

StatTrax is a League of Legends stat-tracking/community database prototype for CMPS 3420. This package includes the completed Phase 4 database-programming objects and a Phase 5 PHP GUI that exercises them.

## Included Phase 4 database programming
- 3 views: `PlayerMatchSummary`, `MediaEngagement`, `PendingReports`
- 4 stored procedures: `CreateMediaPost`, `DeleteComment`, `UpdateReportStatus`, `GetPlayerStatistics`
- 3 explicit triggers: INSERT / UPDATE / DELETE auditing on `Report`
- Foreign-key update/delete behaviors
- `ReportHistory` audit table

## Included Phase 5 GUI requirements
- Login for two groups: Player/User and Administrator
- Role-specific navigation and permissions
- CRUD through the browser
- Aggregate and grouped statistics reporting
- CSV export and Print/Save PDF
- GUI use of views, procedures, and triggers
- Direct/indirect use of every database table

## 1. Install MySQL data
**Warning:** the installer drops and recreates StatTrax tables. Use a development database.

```bash
mysql -u root -p < database/00_full_install.sql
```

## 2. Start the PHP app
```bash
STATTRAX_DB_USER=root \
STATTRAX_DB_PASS='YOUR_MYSQL_PASSWORD' \
php -S localhost:8000 -t public
```

If your database uses a nonstandard port (for example MAMP), add:

```bash
STATTRAX_DB_PORT=8889
```

Then open `http://localhost:8000`.

## Demo accounts
### Player/User
- Username: `Joshua`
- Password: `player123`

### Administrator
- Email: `admin@stattrax.local`
- Password: `admin123`

## Main GUI pages
- `index.php` — gamer-style dashboard
- `login.php` — User/Admin authentication
- `matches.php` — PlayerMatchSummary view
- `stats.php` — aggregate + grouped player report
- `stats_export.php` — CSV export
- `community.php` — community CRUD / reporting
- `profile.php` — user-only profile update
- `reports.php` — admin-only moderation queue
- `comments.php` — admin-only DeleteComment procedure
- `trigger_demo.php` — admin-only trigger/audit demonstration

## Final supplemental database dump
The assignment requires a real `mysqldump` output. Once your final database is populated, run:

```bash
./scripts/make_database_dump.sh
```

This creates `database/stattrax_final_dump.sql`. The script includes routines and triggers.

## Final submission notes
- Update all `TODO` fields in `credentials.txt` with the final deployed URL and instructor-access DB credentials.
- Capture the screenshots listed in `report/screenshot_checklist_phase5.txt`.
- `report/Phase5_Report_Section.md` contains report-ready Phase 5 structure/content.
- See `PHASE5_REQUIREMENTS.md` for the requirement-to-feature map.
