INSERT INTO asiakas (nimi, osoite, email, salasana, puhelinnumero)
VALUES ('Enriqueta Albritton', '3288 Molesworth Lane', 'latricia3@hotmail.com', 'dexter','123456');
INSERT INTO asiakas (nimi, osoite, email, salasana, puhelinnumero)
VALUES ('Meridith Rose', '6723 White Road', 'tommy_shafer93897@yahoo.com', 'hotdog','123457');
INSERT INTO asiakas (nimi, osoite, email, salasana, puhelinnumero)
VALUES ('Chery Nielsen', '0235 Rindle', 'lavernewitcher01@fragrances.id.us', 'atlantis','123458');
INSERT INTO asiakas (nimi, osoite, email, salasana, puhelinnumero)
VALUES ('Noriko Coughlin', '0979 Ashbridge Avenue', 'lupe_mackey5@style.edu.krd', 'gemini','123459');
INSERT INTO asiakas (nimi, osoite, email, salasana, puhelinnumero)
VALUES ('Joya Davies', '2449 Askwith Road', 'gema-leggett@malta.com', 'alison','123460');
INSERT INTO asiakas (nimi, osoite, email, salasana, puhelinnumero)
VALUES ('Eric Hammer', '4032 Masefield Street', 'evelynnbowens@gmail.com', 'donkey','123461');
INSERT INTO asiakas (nimi, osoite, email, salasana, puhelinnumero)
VALUES ('Brandee Wilder', '4558 Caim Lane', 'tran_butterfield5116@brighton.revista.bo', 'killer','123462');
INSERT INTO asiakas (nimi, osoite, email, salasana, puhelinnumero)
VALUES ('Lavenia Baggett', '7463 Cherry', 'mikakrauss@lenders.com', 'claire','123463');
INSERT INTO asiakas (nimi, osoite, email, salasana, puhelinnumero)
VALUES ('Callie Nabors', '1463 Adswood', 'hank.beverly@florida.com', 'stephanie','123464');
INSERT INTO asiakas (nimi, osoite, email, salasana, puhelinnumero)
VALUES ('Jessie Churchill', '9208 Back', 'belva369@yahoo.com', 'microsoft','123465');

INSERT INTO divarit (nimi, osoite, salasana)
VALUES ('Hodgson Divari', '8345 Rhine Avenue', '000000');
INSERT INTO divarit (nimi, osoite, salasana)
VALUES ('Fischer-Beals Divari', '2482 Calder Circle', 'claudia');
INSERT INTO divarit (nimi, osoite, salasana)
VALUES ('Loera Divari', '0452 Bucklow Lane', 'purple');
INSERT INTO divarit (nimi, osoite, salasana)
VALUES ('Duckworth Divari', '0938 Woolley Lane', '232323');
INSERT INTO divarit (nimi, osoite, salasana)
VALUES ('Rivera Divari', '7145 Coltsfood', 'kristina');
INSERT INTO divarit (nimi, osoite, salasana)
VALUES ('Galvez Divari', '0821 Calver', '00000000');
INSERT INTO divarit (nimi, osoite, salasana)
VALUES ('Tran Divari', '1373 Langland', 'corvette');
INSERT INTO divarit (nimi, osoite, salasana)
VALUES ('Dutton Divari', '6102 Padfield Road', 'cookie');
INSERT INTO divarit (nimi, osoite, salasana)
VALUES ('Mccallum Divari', '5065 Goughs Road', 'sweety');
INSERT INTO divarit (nimi, osoite, salasana)
VALUES ('Greiner Divari', '2001 Beaford Circle', '123qwe');

INSERT INTO teokset (tekija, nimi, isbn, julkaisuvuosi, tyyppi, luokka)
VALUES ('Johan Ludvig Runeberg', 'Vänrikki Stoolin tarinat', '9788726797084', '1848', 'romaani', 'historia');
INSERT INTO teokset (tekija, nimi, isbn, julkaisuvuosi, tyyppi, luokka)
VALUES ('J.K. Rowling', 'Harry Potter ja viisasten kivi', '9789513187413', '1997', 'romaani', 'fantasia');
INSERT INTO teokset (tekija, nimi, isbn, julkaisuvuosi, tyyppi, luokka)
VALUES ('Yuval Noah Harari', 'Sapiens: Ihmisen lyhyt historia', '9789522342345', '2011', 'tietokirja', 'historia');
INSERT INTO teokset (tekija, nimi, isbn, julkaisuvuosi, tyyppi, luokka)
VALUES ('Agatha Christie', 'Eikä yksikään pelastunut', '9789513187420', '1939', 'romaani', 'dekkari');
INSERT INTO teokset (tekija, nimi, isbn, julkaisuvuosi, tyyppi, luokka)
VALUES ('Bill Watterson', 'Lassi ja Leevi: Kootut sarjakuvat', '9789513187437', '1985', 'sarjakuva', 'huumori');
INSERT INTO teokset (tekija, nimi, isbn, julkaisuvuosi, tyyppi, luokka)
VALUES ('Stephen King', 'Se', '9789513187444', '1986', 'romaani', 'kauhu');
INSERT INTO teokset (tekija, nimi, isbn, julkaisuvuosi, tyyppi, luokka)
VALUES ('Jane Austen', 'Ylpeys ja ennakkoluulo', '9789513187451', '1813', 'romaani', 'romantiikka');
INSERT INTO teokset (tekija, nimi, isbn, julkaisuvuosi, tyyppi, luokka)
VALUES ('George Orwell', '1984', '9789513187468', '1949', 'romaani', 'dystopia');
INSERT INTO teokset (tekija, nimi, isbn, julkaisuvuosi, tyyppi, luokka)
VALUES ('J.R.R. Tolkien', 'Taru sormusten herrasta', '9789513187475', '1954', 'romaani', 'fantasia');
INSERT INTO teokset (tekija, nimi, isbn, julkaisuvuosi, tyyppi, luokka)
VALUES ('Margaret Atwood', 'Orjattaresi', '9789513187482', '1985', 'romaani', 'dystopia');

INSERT INTO postikulut (max_paino, hinta)
VALUES ('50', '2.50');
INSERT INTO postikulut (max_paino, hinta)
VALUES ('250', '5.00');
INSERT INTO postikulut (max_paino, hinta)
VALUES ('1000', '10.00');
INSERT INTO postikulut (max_paino, hinta)
VALUES ('2000', '15.00');

INSERT INTO nide (teos_id, divari_id, tila, hinta, sisaanostohinta, paino)
VALUES (1, 1, 'myynnissä', 15.00, 10.00, 500);
INSERT INTO nide (teos_id, divari_id, tila, hinta, sisaanostohinta, paino)
VALUES (2, 1, 'varattu', 20.00, 12.00, 600);
INSERT INTO nide (teos_id, divari_id, tila, hinta, sisaanostohinta, paino)
VALUES (3, 2, 'myyty', 25.00, 15.00, 700);
INSERT INTO nide (teos_id, divari_id, tila, hinta, sisaanostohinta, paino)
VALUES (4, 2, 'myynnissä', 30.00, 20.00, 800);
INSERT INTO nide (teos_id, divari_id, tila, hinta, sisaanostohinta, paino)
VALUES (5, 3, 'varattu', 35.00, 25.00, 900);
INSERT INTO nide (teos_id, divari_id, tila, hinta, sisaanostohinta, paino)
VALUES (6, 3, 'myyty', 40.00, 30.00, 1000);

INSERT INTO tilaus (asiakas_id, tilauspvm)
VALUES (1, '2025-03-28 15:30:00');
INSERT INTO tilaus (asiakas_id, tilauspvm)
VALUES (2, '2025-03-27 14:00:00');
INSERT INTO tilaus (asiakas_id, tilauspvm)
VALUES (3, '2025-03-26 13:45:00');
INSERT INTO tilaus (asiakas_id, tilauspvm)
VALUES (4, '2025-03-25 12:30:00');
INSERT INTO tilaus (asiakas_id, tilauspvm)
VALUES (5, '2025-03-24 11:15:00');
INSERT INTO tilaus (asiakas_id, tilauspvm)
VALUES (6, '2025-03-23 10:00:00');

INSERT INTO tilatut_tuotteet (tilaus_id, nide_id)
VALUES (1, 1);
INSERT INTO tilatut_tuotteet (tilaus_id, nide_id)
VALUES (1, 2);
INSERT INTO tilatut_tuotteet (tilaus_id, nide_id)
VALUES (2, 3);
INSERT INTO tilatut_tuotteet (tilaus_id, nide_id)
VALUES (2, 4);
INSERT INTO tilatut_tuotteet (tilaus_id, nide_id)
VALUES (3, 5);
INSERT INTO tilatut_tuotteet (tilaus_id, nide_id)
VALUES (3, 6);

INSERT INTO tilauksen_postikulut (postikulu_id, tilaus_id)
VALUES (1, 1);
INSERT INTO tilauksen_postikulut (postikulu_id, tilaus_id)
VALUES (2, 2);
INSERT INTO tilauksen_postikulut (postikulu_id, tilaus_id)
VALUES (3, 3);
INSERT INTO tilauksen_postikulut (postikulu_id, tilaus_id)
VALUES (2, 4);
INSERT INTO tilauksen_postikulut (postikulu_id, tilaus_id)
VALUES (2, 5);
INSERT INTO tilauksen_postikulut (postikulu_id, tilaus_id)
VALUES (1, 6);