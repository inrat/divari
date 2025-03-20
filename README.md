# Divari
Divarin tietokanta ja käyttöliittymä. Toteutettu osana Tampereen yliopiston Tietokantaohjelmointi-kurssia. 
Käytössä ei ole yheisiä tunnuksia, joten kaikki ylläpitävät omia kopiota yhteisestä projektista. Ennen työstöä on tärkeää hakea viimeisin versio reposta (ja vastaavasti työntää muokkaukset repoon) jotta työskennellään samoilla versioilla. 

## Yhteyden luominen

### PHP-palvelin (WinSCP)
Seurataan kurssin ohjeita kirjautumiseen PHP-palvelimelle. 
palvelinosoitteessa kohtaan /~omatunnus/ syötetään oma tuni-tunnus jonka kautta kirjautuminen tapahtuu. 
Kurssin ohjeista: "Alihakemisto public_html on tyhjä ja sinne sijoitetaan PHP-tiedostot" ➡ aina käynnistäessä palvelin haetaan ensin omaan kotihakemistoon tiedostot, ja sitten päivitetään ne palvelimelle (WinSCPissä vasemmalta kotihakemistosta oikealle palvelimen public_html-hakemistoon). 

### SQL-palvelin (Putty) 
Haetaan tietokannan luontilauseet .sql tiedostosta yhteisestä reposta ja syötetään omalle palvelimelle. Kirjautuessa esim. Puttyn kautta voi halutessaan varmistaa manuaalisesti että tiedosto täsmää, tai tehdä varman kautta ja käyttää komentoa DROP TABLE *taulu*, jonka jälkeen voi hakea taulut (ja esimerkkidatan) uudestaan. 
