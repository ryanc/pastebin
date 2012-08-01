CREATE TABLE pastes (
    id INTEGER PRIMARY KEY,
    timestamp datetime DEFAULT CURRENT_TIMESTAMP,
    paste TEXT,
    token VARCHAR(50),
	filename VARCHAR(100),
	preview TEXT,
	ip BINARY(16)
);
