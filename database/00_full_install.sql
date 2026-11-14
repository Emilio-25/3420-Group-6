CREATE DATABASE IF NOT EXISTS stattrax
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE stattrax;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS ReportHistory;
DROP TABLE IF EXISTS `Like`;
DROP TABLE IF EXISTS `Comment`;
DROP TABLE IF EXISTS `Report`;
DROP TABLE IF EXISTS `Media`;
DROP TABLE IF EXISTS `PlayerStat`;
DROP TABLE IF EXISTS `Match`;
DROP TABLE IF EXISTS `Admin`;
DROP TABLE IF EXISTS `User`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `User` (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Email VARCHAR(120) NOT NULL UNIQUE,
    CreationDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PUUID VARCHAR(100) UNIQUE NULL,
    RiotGameName VARCHAR(100) NULL,
    RiotTagLine VARCHAR(20) NULL
) ENGINE=InnoDB;

CREATE TABLE `Admin` (
    AdminID INT AUTO_INCREMENT PRIMARY KEY,
    Email VARCHAR(120) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    PhoneNumber VARCHAR(30) NULL,
    AdminLevel VARCHAR(30) NOT NULL DEFAULT 'Moderator'
) ENGINE=InnoDB;

CREATE TABLE `Match` (
    MatchID INT AUTO_INCREMENT PRIMARY KEY,
    GameMode VARCHAR(50) NOT NULL,
    PlayedAt DATETIME NOT NULL,
    Duration INT NOT NULL,
    Result VARCHAR(20) NULL
) ENGINE=InnoDB;

CREATE TABLE `Media` (
    MediaID INT AUTO_INCREMENT PRIMARY KEY,
    FilePath VARCHAR(255) NOT NULL,
    Description VARCHAR(500) NULL,
    CreationDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UserID INT NOT NULL,
    DeletedByAdminID INT NULL,
    CONSTRAINT fk_media_user
        FOREIGN KEY (UserID) REFERENCES `User`(UserID)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_media_deleted_admin
        FOREIGN KEY (DeletedByAdminID) REFERENCES `Admin`(AdminID)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE `Comment` (
    CommentID INT AUTO_INCREMENT PRIMARY KEY,
    CommentText VARCHAR(500) NOT NULL,
    CreationDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UserID INT NOT NULL,
    MediaID INT NOT NULL,
    CONSTRAINT fk_comment_user
        FOREIGN KEY (UserID) REFERENCES `User`(UserID)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_comment_media
        FOREIGN KEY (MediaID) REFERENCES `Media`(MediaID)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `Like` (
    LikeID INT AUTO_INCREMENT PRIMARY KEY,
    CreationDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UserID INT NOT NULL,
    MediaID INT NOT NULL,
    CONSTRAINT uq_like_user_media UNIQUE (UserID, MediaID),
    CONSTRAINT fk_like_user
        FOREIGN KEY (UserID) REFERENCES `User`(UserID)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_like_media
        FOREIGN KEY (MediaID) REFERENCES `Media`(MediaID)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `Report` (
    ReportID INT AUTO_INCREMENT PRIMARY KEY,
    Reason VARCHAR(500) NOT NULL,
    CreationDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    UserID INT NOT NULL,
    MediaID INT NOT NULL,
    ReviewerAdminID INT NULL,
    CONSTRAINT fk_report_user
        FOREIGN KEY (UserID) REFERENCES `User`(UserID)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_report_media
        FOREIGN KEY (MediaID) REFERENCES `Media`(MediaID)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_report_reviewer
        FOREIGN KEY (ReviewerAdminID) REFERENCES `Admin`(AdminID)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE `PlayerStat` (
    PlayerStatID INT AUTO_INCREMENT PRIMARY KEY,
    Champion VARCHAR(80) NOT NULL,
    DamageDealt INT NOT NULL DEFAULT 0,
    DamageTaken INT NOT NULL DEFAULT 0,
    WinOrLoss VARCHAR(20) NOT NULL,
    Level INT NOT NULL DEFAULT 1,
    GoldEarned INT NOT NULL DEFAULT 0,
    Kills INT NOT NULL DEFAULT 0,
    Deaths INT NOT NULL DEFAULT 0,
    Assists INT NOT NULL DEFAULT 0,
    UserID INT NOT NULL,
    MatchID INT NOT NULL,
    CONSTRAINT uq_playerstat_user_match UNIQUE (UserID, MatchID),
    CONSTRAINT fk_playerstat_user
        FOREIGN KEY (UserID) REFERENCES `User`(UserID)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_playerstat_match
        FOREIGN KEY (MatchID) REFERENCES `Match`(MatchID)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- Shadow/history table used by the explicit Report triggers.
-- ReportID is intentionally not a foreign key so the audit row remains
-- even after the original Report row is deleted.
CREATE TABLE ReportHistory (
    HistoryID INT AUTO_INCREMENT PRIMARY KEY,
    ReportID INT NOT NULL,
    ActionType VARCHAR(20) NOT NULL,
    OldStatus VARCHAR(30) NULL,
    NewStatus VARCHAR(30) NULL,
    ChangedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
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
USE stattrax;

DROP TRIGGER IF EXISTS ReportAfterInsert;
DROP TRIGGER IF EXISTS ReportAfterUpdate;
DROP TRIGGER IF EXISTS ReportAfterDelete;

DELIMITER $$

CREATE TRIGGER ReportAfterInsert
AFTER INSERT ON `Report`
FOR EACH ROW
BEGIN
    INSERT INTO ReportHistory
        (ReportID, ActionType, OldStatus, NewStatus, ChangedAt)
    VALUES
        (NEW.ReportID, 'INSERT', NULL, NEW.Status, NOW());
END$$

CREATE TRIGGER ReportAfterUpdate
AFTER UPDATE ON `Report`
FOR EACH ROW
BEGIN
    INSERT INTO ReportHistory
        (ReportID, ActionType, OldStatus, NewStatus, ChangedAt)
    VALUES
        (NEW.ReportID, 'UPDATE', OLD.Status, NEW.Status, NOW());
END$$

CREATE TRIGGER ReportAfterDelete
AFTER DELETE ON `Report`
FOR EACH ROW
BEGIN
    INSERT INTO ReportHistory
        (ReportID, ActionType, OldStatus, NewStatus, ChangedAt)
    VALUES
        (OLD.ReportID, 'DELETE', OLD.Status, NULL, NOW());
END$$

DELIMITER ;
USE stattrax;

INSERT INTO `User` (Username, Password, Email, PUUID, RiotGameName, RiotTagLine) VALUES
('Joshua', '$2y$10$demo.hash.only', 'joshua@example.com', 'PUUID-JOSHUA', 'Joshua', 'NA1'),
('Jared',  '$2y$10$demo.hash.only', 'jared@example.com',  'PUUID-JARED',  'Jared',  'NA1'),
('Jordon', '$2y$10$demo.hash.only', 'jordon@example.com', 'PUUID-JORDON', 'Jordon', 'NA1'),
('Emilio', '$2y$10$demo.hash.only', 'emilio@example.com', 'PUUID-EMILIO', 'Emilio', 'NA1');

INSERT INTO `Admin` (Email, Password, PhoneNumber, AdminLevel) VALUES
('admin@stattrax.local', '$2y$10$demo.hash.only', '555-0100', 'Administrator');

INSERT INTO `Match` (GameMode, PlayedAt, Duration, Result) VALUES
('Ranked Solo', '2026-10-03 19:15:00', 1890, 'Win'),
('Ranked Solo', '2026-10-11 20:05:00', 2140, 'Loss'),
('ARAM',        '2026-10-18 21:40:00', 1325, 'Win'),
('Ranked Solo', '2026-10-27 18:25:00', 2010, 'Win');

INSERT INTO PlayerStat
(Champion, DamageDealt, DamageTaken, WinOrLoss, Level, GoldEarned, Kills, Deaths, Assists, UserID, MatchID) VALUES
('Ahri',   24120, 14320, 'Win',  18, 14200, 12, 4, 10, 1, 1),
('Lux',    19880, 11250, 'Loss', 17, 12450,  7, 7, 13, 1, 2),
('Jinx',   30200, 15400, 'Win',  18, 15100, 15, 5,  8, 1, 3),
('Yasuo',  28750, 20120, 'Win',  18, 14950, 11, 6,  9, 1, 4);

INSERT INTO `Media` (FilePath, Description, UserID) VALUES
('/uploads/ahri_pentakill.mp4', 'Ahri ranked highlight', 1),
('/uploads/aram_jinx.png', 'ARAM Jinx scoreboard', 1),
('/uploads/lux_clip.mp4', 'Lux combo clip', 2);

INSERT INTO `Comment` (CommentText, UserID, MediaID) VALUES
('Clean play!', 2, 1),
('That charm was perfect.', 3, 1),
('Huge damage.', 4, 2),
('Demo comment that can be deleted.', 1, 3);

INSERT INTO `Like` (UserID, MediaID) VALUES
(2, 1), (3, 1), (4, 1), (1, 3), (2, 2);

-- These inserts intentionally fire ReportAfterInsert.
INSERT INTO `Report` (Reason, Status, UserID, MediaID, ReviewerAdminID) VALUES
('Possible inappropriate caption', 'Pending', 2, 1, NULL),
('Possible spam', 'Resolved', 3, 3, 1);
