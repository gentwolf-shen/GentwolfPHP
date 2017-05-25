sqlite3 gentwolf

CREATE TABLE admin (
  id INTEGER NOT NULL PRIMARY KEY,
  username VARCHAR(45) NOT NULL DEFAULT '',
  password CHAR(32) NOT NULL DEFAULT '',
  add_time INTEGER DEFAULT 0
);
CREATE UNIQUE INDEX idx_admin_username ON admin (username);

INSERT INTO admin(username,password,add_time) VALUES('admin', '96e79218965eb72c92a549dd5a330112', '1338271777');

CREATE TABLE category (
  id INTEGER PRIMARY KEY,
  parent_id INTEGER DEFAULT 0,
  top_id INTEGER DEFAULT 0,
  name VARCHAR(50) DEFAULT '',
  keywords VARCHAR(200) DEFAULT '',
  description VARCHAR(200) DEFAULT '',
  show_order INTEGER  DEFAULT 0,
  is_show INTEGER DEFAULT 0,
  is_nav INTEGER  DEFAULT 0
);
CREATE INDEX idx_category_parent_id ON category (parent_id);
CREATE INDEX idx_category_top_id ON category (top_id);
CREATE INDEX idx_category_is_show ON category (is_show);
CREATE INDEX idx_category_is_nav ON category (is_nav);

CREATE TABLE article (
  id INTEGER PRIMARY KEY,
  category_id INTEGER DEFAULT 0,
  title VARCHAR(200) NOT NULL DEFAULT '',
  keywords VARCHAR(200) NOT NULL DEFAULT '',
  description VARCHAR(1000) NOT NULL DEFAULT '',
  is_show INTEGER  NOT NULL DEFAULT 0,
  content TEXT NOT NULL,
  add_time INTEGER DEFAULT 0,
  edit_time INTEGER DEFAULT 0,
  tags VARCHAR(255) DEFAULT ''
);
CREATE INDEX idx_article_category_id ON article (category_id);
CREATE INDEX idx_article_is_show ON article (is_show);
