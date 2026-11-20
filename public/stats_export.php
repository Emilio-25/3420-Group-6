<?php
require_once __DIR__ . '/bootstrap.php';
requireLogin();

$userId = currentRole() === 'user' ? currentUserId() : (int)($_GET['user_id'] ?? 1);
$start = $_GET['start_date'] ?? '2026-10-01 00:00:00';
$end = $_GET['end_date'] ?? '2026-10-31 23:59:59';

$rows = allRows(
    'SELECT ps.Champion,
            COUNT(*) AS Matches,
            ROUND(AVG(ps.Kills),2) AS AverageKills,
            ROUND(AVG(ps.Deaths),2) AS AverageDeaths,
            ROUND(AVG(ps.Assists),2) AS AverageAssists,
            SUM(CASE WHEN ps.WinOrLoss = \'Win\' THEN 1 ELSE 0 END) AS Wins,
            ROUND(100 * SUM(CASE WHEN ps.WinOrLoss = \'Win\' THEN 1 ELSE 0 END) / COUNT(*),2) AS WinRate
     FROM PlayerStat ps
     JOIN `Match` m ON m.MatchID = ps.MatchID
     WHERE ps.UserID = ? AND m.PlayedAt BETWEEN ? AND ?
     GROUP BY ps.Champion
     ORDER BY Matches DESC, AverageKills DESC',
    [$userId, $start, $end]
);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="stattrax_champion_report.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['Champion', 'Matches', 'AverageKills', 'AverageDeaths', 'AverageAssists', 'Wins', 'WinRate']);
foreach ($rows as $row) {
    fputcsv($out, $row);
}
fclose($out);
exit;
