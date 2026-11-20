# StatTrax Phase 5 Requirement Map

## Requirement 1 — GUI with login for at least two user groups
Implemented with `public/login.php` and session-based authorization.

### Player/User
- Logs in with Username/Email + password.
- Reads only their own match/stat data.
- Creates media posts.
- Deletes their own media posts.
- Reports another user's media.
- Updates only their own profile.

### Administrator
- Logs in with Admin email + password.
- Reads player match/stat data.
- Reviews pending reports.
- Updates report status with `UpdateReportStatus`.
- Deletes comments with `DeleteComment`.
- Runs the trigger demonstration / audit tools.

## Requirement 2 — CRUD data operations
- **Create:** User creates a Media row with `CreateMediaPost`; User can create a Report from the Community page.
- **Read:** Match history, media engagement, reports, comments, and statistics are displayed in the GUI.
- **Update:** User updates their own User profile; Admin updates Report status through `UpdateReportStatus`.
- **Delete:** User can delete their own Media row; Admin deletes Comment rows through `DeleteComment`.

## Requirement 3 — Aggregate/grouped report that is exportable
`public/stats.php`:
- Calls `GetPlayerStatistics` for aggregate summary metrics.
- Groups PlayerStat rows by Champion and calculates matches, averages, wins, and win rate.
- Exports grouped report through `public/stats_export.php` as CSV.
- Supports browser Print / Save as PDF.

## Requirement 4 — Server-side database programming
The GUI uses Phase 4 database objects:
- Views: `PlayerMatchSummary`, `MediaEngagement`, `PendingReports`
- Procedures: `CreateMediaPost`, `DeleteComment`, `UpdateReportStatus`, `GetPlayerStatistics`
- Triggers: `ReportAfterInsert`, `ReportAfterUpdate`, `ReportAfterDelete`

## Requirement 5 — All database tables used
- `User`: authentication, profile, joins
- `Admin`: authentication and moderation identity
- `Match`: match/stat queries
- `PlayerStat`: match/stat queries and grouped report
- `Media`: community CRUD
- `Comment`: engagement view and admin deletion
- `Like`: engagement view
- `Report`: player reporting and admin moderation
- `ReportHistory`: trigger audit display

## Evidence to capture for the report
1. Login screen with User/Admin role selector.
2. Player login and player-only navigation/profile.
3. Admin login and admin-only moderation navigation.
4. Create Media action and resulting community row.
5. Profile UPDATE before/after.
6. DELETE action evidence.
7. Player analytics page showing grouped champion report.
8. Downloaded CSV or browser Print/Save PDF screen.
9. Admin report moderation result.
10. Trigger demo and ReportHistory rows.
