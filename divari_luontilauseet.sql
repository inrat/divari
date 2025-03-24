-- Tietokantaohjelmointi 2025 kevät
-- Lauri Niemelä, Inka Ratia, Marjo Perttu
-- Harjoitustyö luontilauseet

-- Muutokset 17.2:
-- Korjaus: asiakas.puhelinumero -> int vaihdettu varchar, koska puh nro ei ole mitään tekemistä
-- 									matematiikan kanssa.
-- Lisäys: Taulu postikuluille.
-- Nimeäminen: tilatut_tuotteet.tilattuja_kpl -> kpl vaihdettu pitempään nimeen.
-- Lisäys: nide.tila -> Tiedetään onko kyseinen kirja varattu.
-- Nimeäminen: tilaus.tila -> Vaihdettu tilojen nimet.
-- Nimeäminen: nide.kpl_myytavana -> vaihdettu kpl pitempään nimeen.
-- Lisäys: tilaus.hintaluokka_id -> lisätty id jolla saadaan postikulu taulusta oikea hinta.

-- Muutokset 19.2:
-- Korjaus: tilatut_tuotteet -> poistettu divari_id, 
--								tilattuja_kpl, myyntihinta ja tilatut_tuotteet_id
-- Poistettu: nide.kpl_myytavana
-- Poistettu: tilaus.tila
-- Nimeäminen: hintaluokka_id -> postikulu_id
-- Lisätty: asiakas.salasana kirjautumista varten
-- Lisätty: divarit.salasana kirjautumista varten

-- Muutokset 19.3:
-- Korjaus: Tilaus -> poistettu postikulu_id
-- Lisätty: Tilauksen_postikulut taulu

-- Muutokset 20.3:
-- Korjaus: Poistettu ylimääräiset ","

-- Taulu asiakkaan tiedoista
CREATE TABLE asiakas (
	asiakas_id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	nimi VARCHAR(255) NOT NULL,
	osoite VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL,
	salasana VARCHAR(255) NOT NULL,
	puhelinnumero VARCHAR(20)
);

-- Taulu divareista
CREATE TABLE divarit (
	divari_id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	nimi VARCHAR(255) NOT NULL,
	osoite VARCHAR(255) NOT NULL,
    salasana VARCHAR(255) NOT NULL
);

-- Taulu teoksista
CREATE TABLE teokset ( 
	teos_id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	tekija VARCHAR(255) NOT NULL,
	nimi VARCHAR(255) NOT NULL,
	isbn CHAR(13) UNIQUE,
	julkaisuvuosi SMALLINT CHECK (julkaisuvuosi BETWEEN 0 AND EXTRACT(YEAR FROM CURRENT_DATE)),
	tyyppi VARCHAR(255) NOT NULL,
	luokka VARCHAR(255) NOT NULL
);

-- Taulu postikulujen hinnoista
CREATE TABLE postikulut (
	postikulu_id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	max_paino INT NOT NULL UNIQUE CHECK (max_paino > 0),
	hinta NUMERIC(10, 2) NOT NULL CHECK (hinta >= 0)
);

-- Taulu myytävistä kirjoista
CREATE TABLE nide (
	nide_id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	teos_id INT NOT NULL REFERENCES teokset(teos_id),
	divari_id INT NOT NULL REFERENCES divarit(divari_id),
	tila VARCHAR(10) NOT NULL CHECK (tila IN ('myynnissä', 'varattu', 'myyty')),
	hinta NUMERIC(10, 2) CHECK (hinta >= 0),
	sisaanostohinta NUMERIC(10, 2) CHECK (sisaanostohinta >= 0),
	paino SMALLINT NOT NULL CHECK (paino >= 0)
);

-- Taulu tilauksesta
CREATE TABLE tilaus (
	tilaus_id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	asiakas_id INT NOT NULL REFERENCES asiakas(asiakas_id),
	tilauspvm TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Taulu tilauksen yhdistämiseen
CREATE TABLE tilatut_tuotteet (
    tilaus_id INT NOT NULL REFERENCES tilaus(tilaus_id),
    nide_id INT NOT NULL REFERENCES nide(nide_id),
	PRIMARY KEY (tilaus_id, nide_id)
);

-- Taulu tilauksen postikuluille
CREATE TABLE tilauksen_postikulut (
    postikulu_id INT NOT NULL REFERENCES postikulut(postikulu_id),
    tilaus_id INT NOT NULL REFERENCES tilaus(tilaus_id),
	PRIMARY KEY (postikulu_id, tilaus_id)
);
