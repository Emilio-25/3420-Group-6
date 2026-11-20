# StatTrax Phase 5 GUI

## Install the database
```bash
mysql -u root -p < database/00_full_install.sql
```

## Run locally
```bash
STATTRAX_DB_USER=root STATTRAX_DB_PASS='YOUR_PASSWORD' php -S localhost:8000 -t public
```

Open `http://localhost:8000`.

## Demo logins
- Player: `Joshua` / `player123`
- Admin: `admin@stattrax.local` / `admin123`

## Phase 5 features
- Two login roles with different permissions.
- CRUD through the browser.
- Phase 4 views, stored procedures, and triggers invoked from the GUI.
- Aggregate + grouped champion report.
- CSV export and Print / Save PDF.
- All StatTrax database tables used directly or indirectly.

See `PHASE5_REQUIREMENTS.md` for the rubric mapping and screenshot checklist.
