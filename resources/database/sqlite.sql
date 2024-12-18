-- #! sqlite

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
    INSERT OR REPLACE INTO players(playerName, autosell) VALUES (:playerName, :autosell);
-- #}
-- #{ get
-- #  :playerName string
SELECT * FROM players WHERE playerName = :playerName;
-- #}
-- #}
-- #