RETRO Planning - v 0.2
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
        <can_bonus>0</can_bonus>
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
        "can_bonus" : "0", // Cant work more than #hours_per_day.
        "time_remaining" : "50" // Total project hours
    },
    "project_id": {
        "start_time": "2016-11-16"
    }
}
```


TODO
---

* [ ] Congés
* [ ] Créer projets à la volée.
* [ ] Générer iCal.
* [ ] Gérer API toggl ?
* [ ] Gérer demi-journées non travaillées (jours avec moins d'heures dispo).
* [ ] Gérer la config heures par jours.
* [ ] Gérer les projets à la volée.
* [ ] License adaptée ?
* [ ] Monétiser.
* [ ] Multi-utilisateurs.
* [ ] Tests unitaires.
* [ ] Traduction.

DONE
---

* [x] Afficher la fin prévue du projet.
* [x] Gérer projets qui ne peuvent pas passer en bonus time (uniquement la limite d'heure).
* [x] Utiliser une ref à l'id du projet plutôt qu'au projet
