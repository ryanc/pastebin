CREATE TABLE pastes (
    id INTEGER PRIMARY KEY,
    timestamp datetime DEFAULT CURRENT_TIMESTAMP,
    paste TEXT,
    token VARCHAR(50)
);
