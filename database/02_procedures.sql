USE stattrax;

DROP PROCEDURE IF EXISTS CreateMediaPost;
DROP PROCEDURE IF EXISTS DeleteComment;
DROP PROCEDURE IF EXISTS UpdateReportStatus;
DROP PROCEDURE IF EXISTS GetPlayerStatistics;

DELIMITER $$

CREATE PROCEDURE CreateMediaPost(
    IN p_UserID INT,
    IN p_FilePath VARCHAR(255),
    IN p_Description VARCHAR(500)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO `Media` (FilePath, Description, CreationDate, UserID)
    VALUES (p_FilePath, p_Description, NOW(), p_UserID);

    SELECT LAST_INSERT_ID() AS NewMediaID;

    COMMIT;
END$$

CREATE PROCEDURE DeleteComment(
    IN p_CommentID INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    DELETE FROM `Comment`
    WHERE CommentID = p_CommentID;

    SELECT ROW_COUNT() AS RowsDeleted;

    COMMIT;
END$$

CREATE PROCEDURE UpdateReportStatus(
    IN p_ReportID INT,
    IN p_Status VARCHAR(30),
    IN p_AdminID INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    UPDATE `Report`
    SET Status = p_Status,
        ReviewerAdminID = p_AdminID
    WHERE ReportID = p_ReportID;

    SELECT ROW_COUNT() AS RowsUpdated;

    COMMIT;
END$$

CREATE PROCEDURE GetPlayerStatistics(
    IN p_UserID INT,
    IN p_StartDate DATETIME,
    IN p_EndDate DATETIME
)
BEGIN
    SELECT
        p_UserID AS UserID,
        COUNT(*) AS MatchesPlayed,
        ROUND(AVG(ps.Kills), 2) AS AverageKills,
        ROUND(AVG(ps.Deaths), 2) AS AverageDeaths,
        ROUND(AVG(ps.Assists), 2) AS AverageAssists,
        MAX(ps.Kills) AS HighestKills,
        ROUND(AVG(ps.DamageDealt), 2) AS AverageDamage,
        ROUND(AVG(ps.GoldEarned), 2) AS AverageGold
    FROM PlayerStat AS ps
    JOIN `Match` AS m
        ON ps.MatchID = m.MatchID
    WHERE ps.UserID = p_UserID
      AND m.PlayedAt BETWEEN p_StartDate AND p_EndDate;
END$$

DELIMITER ;
