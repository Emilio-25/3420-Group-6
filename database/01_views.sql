USE stattrax;

DROP VIEW IF EXISTS PlayerMatchSummary;
CREATE VIEW PlayerMatchSummary AS
SELECT
    u.UserID,
    u.Username,
    m.MatchID,
    m.GameMode,
    m.PlayedAt,
    m.Duration,
    m.Result,
    ps.Champion,
    ps.Kills,
    ps.Deaths,
    ps.Assists,
    ps.DamageDealt,
    ps.DamageTaken,
    ps.GoldEarned,
    ps.Level,
    ps.WinOrLoss
FROM `User` AS u
JOIN PlayerStat AS ps
    ON u.UserID = ps.UserID
JOIN `Match` AS m
    ON ps.MatchID = m.MatchID;

DROP VIEW IF EXISTS MediaEngagement;
CREATE VIEW MediaEngagement AS
SELECT
    m.MediaID,
    m.Description,
    m.FilePath,
    m.CreationDate,
    m.UserID,
    u.Username,
    COUNT(DISTINCT l.LikeID) AS LikeCount,
    COUNT(DISTINCT c.CommentID) AS CommentCount
FROM `Media` AS m
JOIN `User` AS u
    ON m.UserID = u.UserID
LEFT JOIN `Like` AS l
    ON m.MediaID = l.MediaID
LEFT JOIN `Comment` AS c
    ON m.MediaID = c.MediaID
GROUP BY
    m.MediaID,
    m.Description,
    m.FilePath,
    m.CreationDate,
    m.UserID,
    u.Username;

DROP VIEW IF EXISTS PendingReports;
CREATE VIEW PendingReports AS
SELECT
    r.ReportID,
    r.Reason,
    r.CreationDate,
    r.Status,
    r.MediaID,
    r.UserID,
    u.Username
FROM `Report` AS r
JOIN `User` AS u
    ON r.UserID = u.UserID
WHERE r.Status = 'Pending';
