USE stattrax;

INSERT INTO `User` (Username, Password, Email, PUUID, RiotGameName, RiotTagLine) VALUES
('Joshua', '$2y$12$78z3L5T8l5CMUw6khywaTuUIcdrALMzZJEJpPB0pcQhXZ2Df14aQ.', 'joshua@example.com', 'PUUID-JOSHUA', 'Joshua', 'NA1'),
('Jared',  '$2y$12$78z3L5T8l5CMUw6khywaTuUIcdrALMzZJEJpPB0pcQhXZ2Df14aQ.', 'jared@example.com',  'PUUID-JARED',  'Jared',  'NA1'),
('Jordon', '$2y$12$78z3L5T8l5CMUw6khywaTuUIcdrALMzZJEJpPB0pcQhXZ2Df14aQ.', 'jordon@example.com', 'PUUID-JORDON', 'Jordon', 'NA1'),
('Emilio', '$2y$12$78z3L5T8l5CMUw6khywaTuUIcdrALMzZJEJpPB0pcQhXZ2Df14aQ.', 'emilio@example.com', 'PUUID-EMILIO', 'Emilio', 'NA1');

INSERT INTO `Admin` (Email, Password, PhoneNumber, AdminLevel) VALUES
('admin@stattrax.local', '$2y$12$IwnAE6kpHVin9QJnHhLYre0C9CDjFX2ADjSVf/Bj5WxF9UMO3GJT6', '555-0100', 'Administrator');

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
