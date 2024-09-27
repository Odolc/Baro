---
layout: default
title: Plugin Baro - changelog
lang: fr_FR
pluginId: baro
---

# Info

> **_Pour rappel_**, s'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de corrections de bugs mineurs.

## 2024

### 27/09/2024

- Correction bug installation depuis Market

#### 25/09/2024

- Correction bug setConfiguration sur la création des commandes

### 22/09/2024

- Tendance : en cas d'absence de données dans l'historique, les données de tendance et tendance numérique ne sont pas mis à jour
- Traduction

### 13/09/2024

- Amélioration log plugin
- Reprise de la création des commandes
- Correction bug sur la mise à jour (2 crons actifs en même temps)
- Traduction merci a @Mips
- Tendance : plus de calcul si l'historique est null
- Correction warning PHP8

### 04/02/2024

- Suppression lien community suite changement core 4.4
- Amélioration barre de recherche

## 2023 

### 08/10/2023

- Amélioration info vers Community pour le Core 4.4
- Typo

### 02/04/2023

- Correction pour core 4.4
- Version mini Core pour le plugin est 4.2

### Version 20230327

- Typo

## 2022 

### Version 20220909

- Ajout fonctionnalité Core V4.3

### Version 20220205

- Affichage Core v4.2

## 2021

### Version 20210728

- Affichage tableau Core v4.2 (beta)
- Correction Objet Parent
- Amélioration générale de l'affichage

## 2020

### Version 20201129

- Amélioration de l'affichage, ajout info bulle sur les commandes
- Amélioration mise à jour des commandes

### Version 20201027

- Amélioration Visu sur dashboard
- Amélioration création des commandes
- Amélioration mise à jour des commandes
- Modification affichage des commandes
- Ajout BP reset de recherche
- Clean Log + code
- Correction bug suppression commande Refresh
- Correction Warning PHP
- Correction variable non définis
- Correction Bug création des commandes
- Amélioration de la liste des objets parents

### Version 20200525

- Correction bug recréation des commandes
- Fin clean suite à déplacement de la documentation
- Amélioration code (gestion affichage de paramètres suivant le mode de calcul)

### Version 20200523

- Update documentations

### Version 20200512

- Correction bug enregistrement individuel de chaque équipement
- Enregistrement des équipements après chaque mise à jour
- Modification widget pour la tendance

### Version 20200418

- Ajout widget core pour les commandes sauf pour tendance
- Ajout widget pour la tendance (uniquement pour le Core V4)
- Mise à jour de la doc
- Modification des variables internes aux calculs

### Version 20200414

- Résolution Bug calcul tendance

### Version 20200413

- Ajout de log supplémentaire en mode DEBUG
- Nettoyage log
- Résolution Bug calcul tendance
- Ajout bouton pour recréer les commandes
- Modification création des commandes
- Affectation valeur Min et Max sur la "Tendance numérique"
- Amélioration affichage V4

> \*Info : Penser à sauvegarder chaque équipement

## <2020

### Version 2.2

- Ajout d’un cron 30
- Amélioration de l'affichage pour le Core V4
- Possibilité de renommer les commandes
- Correction des historiques
- Commande Refresh (sur la tuile, scénario etc)
- Amélioration des logs
- Correction type de generique
- Correction Bug : l'actualisation des données ne se fait plus si l'équipement est désactivé
- Nettoyage des dossiers

> _Remarque : Il est conseillé de supprimer le plugin et ensuite le réinstaller_

### Version 2.1

- Support de PHP 7.3
- Migration vers font-awesome 5
- Migration affichage au format core V4

### Version 2.0

- Mise à jour pour compatibilité Jeedom V3

### Version 1.0

- Création du plugin
