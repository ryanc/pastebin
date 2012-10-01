CREATE TABLE doctrine_migration_versions (version VARCHAR(255) NOT NULL, PRIMARY KEY("version"));
CREATE TABLE paste_content (id INTEGER PRIMARY KEY NOT NULL, content TEXT NOT NULL, digest CHAR(32));
CREATE TABLE pastes (id INTEGER PRIMARY KEY NOT NULL, timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, token VARCHAR(50), filename VARCHAR(100), ip BLOB(16), content_id INTEGER, FOREIGN KEY(content_id) REFERENCES paste_content(id));
