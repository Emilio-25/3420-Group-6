# Phase 4 Requirement Map

This file maps the assignment requirements to the included implementation.

## 4.1 Introduction
- Report: `report/StatTrax_Phase4_Report.docx` / `.pdf`
- Explains SQL programming logic and the use of views, stored procedures, and triggers in StatTrax.

## 4.2 Views - at least 3
1. `PlayerMatchSummary` - `database/01_views.sql`, demonstrated by `public/matches.php`
2. `MediaEngagement` - `database/01_views.sql`, demonstrated by `public/community.php`
3. `PendingReports` - `database/01_views.sql`, demonstrated by `public/reports.php`

## 4.3 Stored Procedures - at least 4
1. INSERT: `CreateMediaPost` - demonstrated by `public/community.php`
2. DELETE: `DeleteComment` - demonstrated by `public/comments.php`
3. UPDATE: `UpdateReportStatus` - demonstrated by `public/reports.php`
4. Statistical/time-range: `GetPlayerStatistics` - demonstrated by `public/stats.php`

## 4.4.1 Foreign Key Constraints
- Defined in `database/00_schema.sql`
- Includes intentional `ON UPDATE CASCADE`, `ON DELETE CASCADE`, and `ON DELETE SET NULL` behavior.

## 4.4.2 Explicit Triggers - INSERT, UPDATE, DELETE
1. `ReportAfterInsert`
2. `ReportAfterUpdate`
3. `ReportAfterDelete`

All are defined in `database/03_triggers.sql` and demonstrated by `public/trigger_demo.php`.
The audit table is `ReportHistory`.

## Evidence
- `tests/phase4_test.sql` provides a complete SQL-only test sequence.
- `report/screenshot_checklist.txt` lists the screenshots/results to collect from the group's own MySQL run.
