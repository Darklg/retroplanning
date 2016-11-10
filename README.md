RETRO Planning
===

Install
---

* Créer à la racine un fichier nommé local-planning.xml

```xml
<?php xmlversion="1.0";?>
<planning>
    <project_test>
        <color>#33C</color>
        <name>Projet test</name>
        <time_remaining>5</time_remaining>
        <hours_per_day>1</hours_per_day>
        <start_time>2016-11-15</start_time>
    </project_test>
    <project_id>
        <start_time>2016-11-16</start_time>
    </project_id>
</planning>
```

* OU Créer à la racine un fichier nommé local-planning.json

```json
{
    "project_test": {
        "start_time": "2016-11-16",
        "name": "Test",
        "hours_per_day" : "5", // Hours to work per day
        "time_remaining" : "50" // Total project hours
    },
    "project_id": {
        "start_time": "2016-11-16"
    }
}
```


TODO
---

* Gérer demi-journées non travaillées (jours avec moins d'heures dispo).
* Gérer projets qui ne peuvent pas passer en bonus time (uniquement la limite d'heure).
* Gérer API toggl ?
* Générer iCal.
* Traduction.
* Gérer la config heures par jours.
* Gérer les projets à la volée.
* Multi-utilisateurs.
* Monétiser.
