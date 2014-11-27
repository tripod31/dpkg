CREATE TABLE installed (
  status text NOT NULL,
  name text NOT NULL,
  version text NOT NULL,
  description text NOT NULL
) ;
CREATE TABLE installed_org (
  status text NOT NULL,
  name text NOT NULL,
  version text NOT NULL,
  description text NOT NULL
) ;
CREATE TABLE versions (
    name text NOT NULL,
    repo text NOT NULL
);
CREATE TABLE info (
    name text NOT NULL,
    info text NOT NULL
);
INSERT INTO info VALUES('saved_time_cur','');
INSERT INTO info VALUES('saved_time_org','');
