RETRO Planning - v 0.5.0
===

Install
---

* Créer à la racine un fichier nommé local-planning.xml

```xml
<?xml version="1.0"?>
<planning>
    <settings>
        <max_hours_per_day>7</max_hours_per_day>
        <holidays>
            <date>01/01</date>
            <date>01/05</date>
            <date>08/05</date>
        </holidays>
    </settings>
    <projects>
        <project_test>
            <color>#33C</color>
            <name>Projet test</name>
            <time_remaining>5</time_remaining>
            <hours_per_day>1</hours_per_day>
            <start_time>2016-11-15</start_time>
            <can_bonus>0</can_bonus>
        </project_test>
        <project_id>
            <start_time>2016-11-16</start_time>
        </project_id>
    </projects>
</planning>
```

* OU Créer à la racine un fichier nommé local-planning.json

```json
{
    "settings": {
        "holidays": ["11/11","12/11","13/11"],
        "max_hours_per_day": "8",
    },
    "projects": {
        "project_test": {
            "start_time": "2016-11-16",
            "name": "Test",
            "hours_per_day" : "5",
            "can_bonus" : "0",
            "time_remaining" : "50"
        },
        "project_id": {
            "start_time": "2016-11-16"
        }
    }
}
```


TODO
---

* [ ] Gérer avancement de la journée en cours.
* [ ] Créer projets à la volée.
* [ ] Générer iCal.
* [ ] Gérer API toggl ?
* [ ] Gérer demi-journées non travaillées (jours avec moins d'heures dispo).
* [ ] License adaptée ?
* [ ] Monétiser.
* [ ] Multi-utilisateurs.
* [ ] Tests unitaires.
* [ ] Traduction.

DONE
---

* [x] Congés
* [x] Gérer la config heures par jours.
* [x] Afficher la fin prévue du projet.
* [x] Gérer projets qui ne peuvent pas passer en bonus time (uniquement la limite d'heure).
* [x] Utiliser une ref à l'id du projet plutôt qu'au projet
