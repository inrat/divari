# Divari
Divarin tietokanta ja käyttöliittymä. Toteutettu osana Tampereen yliopiston Tietokantaohjelmointi-kurssia. 
Käytössä ei ole yheisiä tunnuksia, joten kaikki ylläpitävät omia kopiota yhteisestä projektista. Ennen työstöä on tärkeää hakea viimeisin versio reposta (ja vastaavasti työntää muokkaukset repoon) jotta työskennellään samoilla versioilla. 

## Ohjelman pyörittäminen

### PHP-palvelin (WinSCP)
Seurataan kurssin ohjeita kirjautumiseen PHP-palvelimelle. 
palvelinosoitteessa kohtaan /~omatunnus/ syötetään oma tuni-tunnus jonka kautta kirjautuminen tapahtuu. 
Kurssin ohjeista: "Alihakemisto public_html on tyhjä ja sinne sijoitetaan PHP-tiedostot" ➡ aina käynnistäessä palvelin haetaan ensin omaan kotihakemistoon tiedostot, ja sitten päivitetään ne palvelimelle (WinSCPissä vasemmalta kotihakemistosta oikealle palvelimen public_html-hakemistoon). 

### SQL-palvelin (Putty) 

Toimiakseen ohjelma vaatii [konfigurointi-tiedostoon](https://github.com/inrat/divari/blob/main/config/config.php) (config.php) toimivat tietokantatunnukset. Jos käytetään ohjelmaa /~hcmape/ käyttäjänä, tulee ```$y_tiedot = "dbname=hcmape user=hcmape password=salasana_tulee_syottaa";``` kohtaan password syöttää toimiva salasana. 

#### Keskustietokanta
Haetaan keskusdivarin tietokannan luontilauseet [tästä tiedostosta](https://github.com/inrat/divari/blob/main/divari_luontilauseet.sql) (divari_luontilauseet.sql) yhteisestä reposta ja syötetään omalle palvelimelle. Kirjautuessa esim. Puttyn kautta voi halutessaan varmistaa manuaalisesti että tiedosto täsmää, tai tehdä varman kautta ja käyttää komentoa DROP TABLE *taulu*, jonka jälkeen voi hakea taulut (ja esimerkkidatan) uudestaan. Tietokantaan voi syöttää esimerkkidataa [tästä tiedostosta](https://github.com/inrat/divari/blob/main/esimerkkidataa.sql) (esimerkkidataa.sql). Datan syötössä tärkeää on huomioida, että SQL generoi id-numerot, joten data on syötettävä oikeassa järjestyksessä. Ohjelma olettaa, että keskusdivarin id on 1, ja osatietokannan 2 (jos syöttää seuraavassa osiossa esitellyn osatietokannan).

#### Oma (osa)tietokanta
Divarin oma tietokanta on toteutettu SCHEMA-toiminnolla. Luontilauseet osatietokannan luomiseen löytyvät [tästä tiedostosta](https://github.com/inrat/divari/blob/main/Ohjeet-osatietokannan-luomiseen.txt) (Ohjeet-osatietokannan-luomiseen.txt). Jos osatietokannan tietoja ei muuta, löytyy testattava näkymä ainoastaan kirjautumalla divari_id=2 tunnuksilla. Salasanat asiakkaiden ja divarien tunnuksille löytyvät esimerkkidataa.sql-tiedostosta.

## Ohjelman käyttö ja testaus

### Asiakkaana

Linkki kirjautumissivulle: https://tie-tkannat.it.tuni.fi/~hcmape/divari/public/index.php

Asiakkaana voit:
- Rekisteröityä (ohjelma tarkistaa, onko sähköposti jo käytössä)
- Kirjautua (sisään ja ulos)
  Esimerkkitunnukset (~hcmape -tietokannasta):
  * sähköposti: belva369@yahoo.com
  * salasana: microsoft
    
Kirjautumisen jälkeen:
- Pääset kotisivulle, jossa on hakutoiminto
- Hakutoiminnolla voi hakea teoksia tekijän, nimen, tyypin tai luokan perusteella
- Voit suodattaa hakua luokan ja tyypin mukaan
- Teoksen nimeä klikkaamalla pääset näkemään teoksen tarkemmat tiedot ja niteet
- Niteen tilan mukaan voit lisätä sen ostoskoriin tai saat ilmoituksen, miksi et voi (jos nide on varattu tai myyty)

Ostoskori -näkymässä:
- Voit tehdä tilauksen
- Tyhjentää ostoskorin (vapauttaa niteet takaisin myyntiin)
- Palata takaisin hakuun

Tilauksen suorittaminen: 
- Tilauksen yhteenveto näyttää kokonaishinnan postikuluineen ja asiakastiedot
- Vahvista ja maksa -näkymässä simuloidaan lähetettyä sähköpostiviestiä
- Tilauksen vahvistamisen jälkeen niteen tila muuttuu myydyksi

### Ylläpitäjänä (Admin)

Linkki kirjautumissivulle: https://tie-tkannat.it.tuni.fi/~hcmape/divari/public/admin_login_popup.php

- Admin-kirjautumiseen linkki myös aloitussivulla (index.php)
- Ylläpitäjän toimintoja voi simuloida kolmilla eri tunnuksella (~hcmape tietokannasta):
    - Gallein Galle, keskustietokannan hallinnointinäkymä. Keskusdivari voi myös myydä omia teoksia. 
        Kirjautuminen: 
        * Divarin ID: 1 
        * salasana: Keskus
    - Lassen Lehti, osatietokanta eli käyttää omaa tietokantaa, josta kopio keskustietokannassa
        Kirjautuminen:
        * Divarin ID: 2
        * salasana: Lasse
    - Divari x, hyödyntää keskusdivarin tietokantaa.
         Kirjautuminen:
         * Divarin ID: 3
         * salasana: purple
           
Kirjautumisen jälkeen:
- Etusivulla linkki omien niteiden lisäykseen ja tarkasteluun

Niteen lisäys:
- Hae teos ID:llä tai nimellä
- Täydennä tiedot: myyntihinta, sisäänostohinta, paino
- Onnistuneesta lisäyksestä tulee ilmoitus

Teoksen lisäys:
- Jos teosta ei löydy hausta, ehdotetaan käyttäjälle teoksen lisäystä
- Täydennä tiedot: tekijä, nimi, (isbn), julkaisuvuosi, tyyppi ja luokka
- Onnistuneesta lisäyksestä tulee ilmoitus

Keskusdivarin (Divarin ID 1) ylläpitäjänä laajemmat oikeudet:
- Voi tarkastella asiakkaita ja nähdä kaikki niteet (linkki Admin-etusivulla kun kirjautuneena divari_id 1)
- Voi nähdä kaikki myynnissä olevat teokset (linkki Admin-etusivulla kun kirjautuneena divari_id 1)

Osatietokannan omaavan (Divarin ID 2) ylläpitäjän laajemmat oikeudet:
- Voi tarkastella oman osatietokannan ja keskustietokannan välistä synkronoinnin tilaa
