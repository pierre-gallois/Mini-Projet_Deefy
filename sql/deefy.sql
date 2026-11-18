

DROP TABLE IF EXISTS user2playlist;
DROP TABLE IF EXISTS playlist2track;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS track;
DROP TABLE IF EXISTS playlist;

CREATE TABLE playlist (
id INT NOT NULL AUTO_INCREMENT,
nom VARCHAR(100) NOT NULL,
PRIMARY KEY (id)
);

CREATE TABLE track (
id INT NOT NULL AUTO_INCREMENT,
titre VARCHAR(100) NOT NULL,
genre VARCHAR(30) DEFAULT NULL,
duree INT DEFAULT 0,
filename VARCHAR(100) NOT NULL,
type CHAR(1) NOT NULL,
artiste_album VARCHAR(100) DEFAULT NULL,
titre_album VARCHAR(100) DEFAULT NULL,
annee_album INT DEFAULT NULL,
numero_album INT DEFAULT NULL,
auteur_podcast VARCHAR(100) DEFAULT NULL,
date_posdcast DATE DEFAULT NULL,
PRIMARY KEY (id)
);

CREATE TABLE playlist2track (
id_pl INT NOT NULL,
id_track INT NOT NULL,
no_piste_dans_liste INT NOT NULL,
PRIMARY KEY (id_pl, id_track),
FOREIGN KEY (id_pl) REFERENCES playlist (id),
FOREIGN KEY (id_track) REFERENCES track (id)
);


CREATE TABLE user (
id INT NOT NULL AUTO_INCREMENT,
email VARCHAR(256) NOT NULL,
passwd VARCHAR(256) NOT NULL,
role INT NOT NULL DEFAULT 1,
PRIMARY KEY (id)
);

CREATE TABLE user2playlist (
id_user INT NOT NULL,
id_pl INT NOT NULL,
PRIMARY KEY (id_user, id_pl),
FOREIGN KEY (id_user) REFERENCES user (id),
FOREIGN KEY (id_pl) REFERENCES playlist (id)
);

-- données des test
INSERT INTO playlist (id, nom) VALUES
(1, 'Lucille');

INSERT INTO track (id, titre, genre, duree, filename, type, artiste_album, titre_album, annee_album, numero_album, auteur_podcast, date_posdcast) VALUES
(1, 'Im with you', 'Jazz', 271, 'audio/01-Im_with_you_BB-King-Lucille.mp3', 'A', 'BB King', 'Lucille', 1968, 1, NULL, NULL),
(2, 'I Need your Love', 'Jazz', 334, 'audio/02-I_Need_Your_Love-BB_King-Lucille.mp3', 'A', 'BB King', 'Lucille', 1968, 1, NULL, NULL),
(3, 'Country Gril', 'Jazz', 187, 'audio/03-Country_Girl-BB_King-Lucille.mp3', 'A', 'BB King', 'Lucille', 1968, 1, NULL, NULL);


INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES
(1, 1, 1),
(1, 2, 2),
(1, 3, 3);



