# SELECT:
<<<<<<< HEAD
SELECT * FROM users;
=======
select * from users;
>>>>>>> 49b805e47a591624e7f94d6df867af5590dc3caa

# INSERT:
INSERT INTO users (name, password)
VALUES ("user01", "@123"),
       ("user02", "@123"),
       ("user03", "@123"),
       ("user04", "@123"),
       ("user05", "@123");

INSERT INTO users (name, password)
<<<<<<< HEAD
VALUES ("Marco", "@123");
=======
VALUES ("Henrique Maximo", "@123");
>>>>>>> 49b805e47a591624e7f94d6df867af5590dc3caa

# UPDATE:
UPDATE users SET type = 1 WHERE id = 6;