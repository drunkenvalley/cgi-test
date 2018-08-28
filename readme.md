# CGI teknisk test

En teknisk test basert på å lese av strømmåler, og hente ut verdiene som er registrert.

## Oppstart

* En må ha noe apache-php-sql setup. Enkelt alternativ er wamp for windows, lamp for linux, etc.

## Installasjon

* Denne mappens innhold må være tilgjengelig via nettleser. Med f.eks wamp trenger den ligge i wamps 'www' mappe.
* SQL koden må importeres. SQL koden er pakket i en vedlagt .sql fil.
* Avhengig av din SQL konfigurasjon må 'api/db.php' bli oppdatert med rett info.

Etter dette burde det i teorien funke greit å fyre opp sida. 

## Forbedringer

Så som noen som tegner, så trenger en noen ganger ta et par trinn tilbake fra arbeidet og tenke om noe er snodig. Etter at kravene ble oppfylt ser jeg at det er en del ting å gjøre for å forbedre dette.

* Oversikt tilsvarende header-filer. Slikt det er nå er det knotete å navigere.
* Flere funksjoner bør få navn endret til standardiserte navn.
* Noen funksjoner har ingen reell funksjon (f.eks meter klassens sin 'get'), og bør sammenslås med den reelle funksjonen.
* En funksjon burde vært i meter-class.php, i klassen, men ble lagd utenfor.

## Features som bør implementeres

Prosjektet oppfyller kravene som ble presentert, men det savner et par ting.

* user.php - det burde være login og mer.
* styles.css - siden er stort sett ikke gitt noen visuell stil.
