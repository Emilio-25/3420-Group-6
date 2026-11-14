CREATE VIEW PendingReports AS
SELECT
    r.ReportID,
    r.Reason,
    r.CreationDate,
    r.Status,
    r.MediaID,
    r.UserID,
    u.Username
FROM Report r
JOIN `User` u
    ON r.UserID = u.UserID
WHERE r.Status = 'Pending';

-- Test
SELECT * FROM PendingReports;
