USE stattrax;

-- ============================================================
-- 4.2 VIEWS
-- ============================================================
SELECT * FROM PlayerMatchSummary ORDER BY PlayedAt DESC;
SELECT * FROM MediaEngagement ORDER BY CreationDate DESC;
SELECT * FROM PendingReports ORDER BY CreationDate DESC;

-- ============================================================
-- 4.3 STORED PROCEDURES
-- ============================================================
CALL CreateMediaPost(1, '/uploads/test_media.png', 'Created through CreateMediaPost');
SELECT * FROM `Media` ORDER BY MediaID DESC LIMIT 3;

SELECT * FROM `Comment` WHERE CommentID = 4;
CALL DeleteComment(4);
SELECT * FROM `Comment` WHERE CommentID = 4;

SELECT * FROM `Report` WHERE ReportID = 1;
CALL UpdateReportStatus(1, 'Resolved', 1);
SELECT * FROM `Report` WHERE ReportID = 1;

CALL GetPlayerStatistics(1, '2026-10-01 00:00:00', '2026-10-31 23:59:59');

-- ============================================================
-- 4.4 EXPLICIT TRIGGERS
-- ============================================================
-- INSERT trigger
INSERT INTO `Report` (Reason, Status, UserID, MediaID)
VALUES ('Trigger test report', 'Pending', 1, 2);
SET @trigger_report_id = LAST_INSERT_ID();

-- UPDATE trigger
UPDATE `Report`
SET Status = 'Reviewed', ReviewerAdminID = 1
WHERE ReportID = @trigger_report_id;

-- DELETE trigger
DELETE FROM `Report`
WHERE ReportID = @trigger_report_id;

-- Proof of all three actions
SELECT *
FROM ReportHistory
WHERE ReportID = @trigger_report_id
ORDER BY HistoryID;
