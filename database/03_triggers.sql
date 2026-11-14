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
