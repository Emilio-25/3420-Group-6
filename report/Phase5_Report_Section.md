# 5 - GUI Development

StatTrax uses a browser-based graphical user interface built with PHP, HTML, CSS, and MySQL/MariaDB. The application separates player-facing features from administrator moderation features through session-based authentication. The application layer communicates with the database through PDO prepared statements, SQL views, stored procedures, and triggers.

## 5.1 - GUI Functionalities and User Groups

StatTrax supports two user groups: **Player/User** and **Administrator**.

### 5.1.1 - User Capabilities

**Player/User:** A player can log in with a StatTrax account, view only their own match history and performance statistics, create community media posts, delete their own media posts, report another user's post, and update their own profile. The player cannot access administrator moderation pages.

**Administrator:** An administrator logs in through the Admin table and receives moderation controls. Administrators can view player match/stat information, review pending media reports, update report status, delete comments, and view the trigger/audit demonstration. Administrator-only pages reject player accounts.

### 5.1.2 - Screenshots and Walkthrough

Recommended screenshot sequence:
1. Login page showing the Player/User and Administrator role selector.
2. Player dashboard after login.
3. Player Match History page.
4. Player Analytics page with aggregate summary and Champion Performance grouped report.
5. Community page showing Create Media Post and player-owned Delete action.
6. Player Profile page showing UPDATE functionality.
7. Administrator dashboard after login.
8. Admin Pending Reports page and status UPDATE.
9. Admin Comment Control page and DELETE stored procedure.
10. Trigger Demo page and ReportHistory audit rows.
11. CSV download or browser Print/Save PDF result.

## 5.2 - GUI Programming

### 5.2.1 - Database Layer Programming

StatTrax reuses the Phase 4 database logic throughout the GUI.

**Views**
- `PlayerMatchSummary`: joins User, PlayerStat, and Match to display player match history.
- `MediaEngagement`: joins Media and User while aggregating Like and Comment counts for the community feed.
- `PendingReports`: joins Report and User and filters unresolved reports for administrators.

**Stored Procedures**
- `CreateMediaPost`: called from the player Community page when a new media post is created.
- `DeleteComment`: called from the administrator Comment Control page.
- `UpdateReportStatus`: called from the administrator Moderation Queue.
- `GetPlayerStatistics`: called from the Player Analytics page for date-range aggregate statistics.

**Triggers**
- `ReportAfterInsert`, `ReportAfterUpdate`, and `ReportAfterDelete` write audit records to `ReportHistory`. The application demonstrates these through the administrator Trigger Demo page. A normal player report submission also activates the INSERT trigger.

### 5.2.2 - Application Layer Programming

The PHP application layer connects to MySQL/MariaDB through `src/db.php` using PDO. Connection settings can be provided with environment variables rather than storing deployment credentials in source code.

`src/functions.php` contains common database helpers as well as authentication and authorization functions. `public/login.php` checks either the User or Admin table and uses `password_verify()` to validate the stored password hash. Successful authentication is stored in the PHP session. Role checks prevent players from entering administrator pages.

The application executes SQL through prepared statements. It calls Phase 4 stored procedures for media creation, comment deletion, report status updates, and statistical reporting. Player-facing pages derive the logged-in user's UserID from the session rather than allowing a player to submit an arbitrary UserID.

### 5.2.3 - Front-end Layer Programming

The front end uses responsive HTML and CSS with a gaming/esports-inspired visual style. Navigation changes based on the logged-in role. The Player Analytics page includes a grouped Champion Performance report and two export options: CSV download and browser Print/Save PDF.

The interface includes responsive tables, form controls, role badges, validation through required HTML inputs, mobile layouts, and print-specific CSS so generated reports remain readable when saved to PDF.

## Requirement Checklist

1. **Two user groups with login:** Player/User and Administrator.
2. **CRUD:** Create Media/Report; Read matches/community/reports/statistics; Update profile/report status; Delete media/comment.
3. **Aggregate + grouping report:** Champion Performance groups PlayerStat by Champion and calculates averages, wins, and win rate.
4. **Exportable report:** CSV download and browser Print/Save PDF.
5. **Database programming:** GUI invokes Phase 4 views, stored procedures, and triggers.
6. **All database tables:** User, Admin, Match, PlayerStat, Media, Comment, Like, Report, and ReportHistory are used directly or indirectly.
