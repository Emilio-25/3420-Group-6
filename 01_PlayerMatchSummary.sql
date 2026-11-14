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
FROM `User` u
JOIN PlayerStat ps
    ON u.UserID = ps.UserID
JOIN `Match` m
    ON ps.MatchID = m.MatchID;

-- Test
SELECT * FROM PlayerMatchSummary;
