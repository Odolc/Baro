# Description

Ce plugin permet de calculer la tendance météo à venir en se basant sur les évolutions de la pression atmosphérique des dernières heures

# Configuration

Le plugin ne comporte pas de configuration générale.
Il faut ajouter un équipement pour la pression atmosphérique.
>Cet équipement doit avoir l'historique activé

# Configuration des équipements

La configuration des équipements virtuels est accessible à partir du
menu plugin :

# Tendance Météo
> Sources :
> - <a href="http://www.freescale.com/files/sensors/doc/app_note/AN3914.pdf">http://www.freescale.com/files/sensors/doc/app_note/AN3914.pdf</a>
> - <a href="https://www.parallax.com/sites/default/files/downloads/29124-Altimeter-Application-Note-501.pdf">https://www.parallax.com/sites/default/files/downloads/29124-Altimeter-Application-Note-501.pdf</a>

Le plugin calcule 6 niveaux d'information
- Niveau 0 :
    >- Tendance : Forte dégradation, instable
    >- Tendance numérique : 0
- Niveau 1 :
    >- Tendance : Dégradation, mauvais temps durable
    >- Tendance numérique : 1
- Niveau 2 :
    >- Tendance : Lente dégradation, temps stable
    >- Tendance numérique : 2
- Niveau 3 :
    >- Tendance : Lente amélioration, temps stable
    >- Tendance numérique : 3
- Niveau 4 :
    >- Tendance : Amélioration, beau temps durable
    >- Tendance numérique : 4
- Niveau 5 :
    >- Tendance : Forte embellie, instable
    >- Tendance numérique : 5

Le plugin affecte par défaut un widget (uniquement pour le core V4) :
![Visu Tendance](../images/visu_tendance.png)

# FAQ

-   Est-ce que le plugin s'appuie sur des API tiers ?

>Non, le plugin fait le calcul en interne par rapport à la pression atmosphérique

-   A quoi sert le plugin ?

>Le plugin calcule une tendance météo en se basant sur l'évolution de la pression atmosphérique sur les dernières heures

# Exemple de configuration
## Vue de la liste des équipements

![exemple vue d'ensemble](../images/vue.png)

## Exemple de configuration

![exemple équipement](../images/equipement.png)
![exemple commandes](../images/commandes.png)


# Troubleshotting
- Je n'ai pas d'informations qui remontent

> Il faut bien indiquer l'équipement pression pour que le plugin fonctionne correctement. L'équipement doit être aussi historisé pour pouvoir fonctionner
>
> On peut rechercher les équipements grace au bouton de recherche de l’équipement.