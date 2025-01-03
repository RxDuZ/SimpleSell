-- #! mysql

-- #{ tables
-- #{ players
CREATE TABLE IF NOT EXISTS players (
    playerName VARCHAR(32) PRIMARY KEY NOT NULL,
    autosell BOOLEAN NOT NULL DEFAULT false
);
-- #}
-- #}
-- #{ request
-- #{ insert
-- #  :playerName string
-- #  :autosell bool
INSERT INTO players(playerName, autosell) VALUES (:playerName, :autosell) ON DUPLICATE KEY UPDATE autosell = :autosell;
-- #}
-- #{ get
-- #  :playerName string
SELECT * FROM players WHERE playerName = :playerName;
-- #}
-- #}
-- #