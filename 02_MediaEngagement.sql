CREATE VIEW MediaEngagement AS
SELECT
    m.MediaID,
    m.Description,
    m.FilePath,
    m.CreationDate,
    u.Username,
    COUNT(DISTINCT l.LikeID) AS LikeCount,
    COUNT(DISTINCT c.CommentID) AS CommentCount
FROM Media m
JOIN `User` u
    ON m.UserID = u.UserID
LEFT JOIN `Like` l
    ON m.MediaID = l.MediaID
LEFT JOIN Comment c
    ON m.MediaID = c.MediaID
GROUP BY
    m.MediaID,
    m.Description,
    m.FilePath,
    m.CreationDate,
    u.Username;

-- Test
SELECT * FROM MediaEngagement;
