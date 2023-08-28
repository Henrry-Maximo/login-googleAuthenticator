# SELECT:
select * from users;

# INSERT:
INSERT INTO users (name, password)
VALUES ("user01", "@123"),
       ("user02", "@123"),
       ("user03", "@123"),
       ("user04", "@123"),
       ("user05", "@123");

INSERT INTO users (name, password)
VALUES ("Henrique Maximo", "@123");

# UPDATE:
UPDATE users SET type = 1 WHERE id = 6;