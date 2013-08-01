DROP TABLE IF EXISTS posts;
CREATE TABLE posts
(
  id                   smallint unsigned NOT NULL auto_increment,
  post_date            date NOT NULL,                              # When the article was published
  post_title           varchar(255) NOT NULL,                     # Full title of the article
  post_excerpt         text NOT NULL,                              # A short summary of the article
  post_content         mediumtext NOT NULL,                        # The HTML content of the article

  PRIMARY KEY     (id)
);
