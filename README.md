# Divari
Divarin tietokanta ja käyttöliittymä. Toteutettu osana Tampereen yliopiston Tietokantaohjelmointi-kurssia. 
Käytössä ei ole yheisiä tunnuksia, joten kaikki ylläpitävät omia kopiota yhteisestä projektista. Ennen työstöä on tärkeää hakea viimeisin versio reposta (ja vastaavasti työntää muokkaukset repoon) jotta työskennellään samoilla versioilla. 

### PHP-palvelin (WinSCP)
Seurataan kurssin ohjeita kirjautumiseen PHP-palvelimelle. 
palvelinosoitteessa kohtaan /~omatunnus/ syötetään oma tuni-tunnus jonka kautta kirjautuminen tapahtuu. 
Kurssin ohjeista: "Alihakemisto public_html on tyhjä ja sinne sijoitetaan PHP-tiedostot" ➡ aina käynnistäessä palvelin haetaan ensin omaan kotihakemistoon tiedostot, ja sitten päivitetään ne palvelimelle (WinSCPissä vasemmalta kotihakemistosta oikealle palvelimen public_html-hakemistoon). 

### SQL-palvelin (Putty) 
Haetaan tietokannan luontilauseet .sql tiedostosta yhteisestä reposta ja syötetään omalle palvelimelle. Kirjautuessa esim. Puttyn kautta voi halutessaan varmistaa manuaalisesti että tiedosto täsmää, tai tehdä varman kautta ja käyttää komentoa DROP TABLE *taulu*, jonka jälkeen voi hakea taulut (ja esimerkkidatan) uudestaan. 

## Ohjelman testaus

### Asiakkaana

Linkki kirjautumissivulle: https://tie-tkannat.it.tuni.fi/~hcmape/divari/public/index.php

Asiakkaana voit:
- Rekisteröityä (ohjelma tarkistaa, onko sähköposti jo käytössä)
- Kirjautua

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

Linkki kirjautumissivulle: https://tie-tkannat.it.tuni.fi/~hcmape/divari/public/index.php

- Admin-kirjautumiseen linkki aloitussivulla
- Ylläpitäjän toimintoja voi simuloida kahdella eri tunnuksella:
    - Gallein Galle, käyttää keskusdivarin tietokantaa 
        Kirjautuminen: 
        * Divarin ID: 1 
        * salasana: Keskus
    - Lassen Lehti, osatietokanta eli käyttää omaa tietokantaa, josta kopio keskustietokannassa
        Kirjautuminen:
        * Divarin ID: 2
        * salasana: Lasse

Kirjautumisen jälkeen:
- Etusivulla linkki omien niteiden lisäykseen ja tarkasteluun

Niteen lisäys:
- Hae teos ID:llä tai nimellä
- Täydennä tiedot: myyntihinta, sisäänostohinta, paino
- Onnistuneesta lisäyksestä tulee ilmoitus

Keskusdivarin (Divarin ID 1) ylläpitäjänä laajemmat oikeudet:
- Voi tarkastella asiakkaita ja nähdä ostetut niteet (linkki etusivulla)
- Voi nähdä kaikki myynnissä olevat teokset (linkki etusivulla)



