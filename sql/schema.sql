CREATE TABLE pastes (
    id INTEGER PRIMARY KEY NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    token VARCHAR(50),
    filename VARCHAR(100),
    ip BLOB(16) NOT NULL,
    content_id INTEGER NOT NULL,
    highlight BOOLEAN NOT NULL DEFAULT 1,
    FOREIGN KEY(content_id) REFERENCES paste_content(id)
);

CREATE TABLE paste_content (
    id INTEGER PRIMARY KEY NOT NULL,
    content TEXT NOT NULL,
    digest CHAR(32) NOT NULL
);
