# ormframeworkV2
version 2 de ormframework - restructuration, refactoration, et reconception du framework

**Installation :**
  -
  - git clone https://github.com/nicolachoquet06250/ormframeworkV2 \<repertoire\>
  - ouvrir le projet avec PhpStorm
  - ouvir une console dans le projet et lancer la commande:
    - php ormframework.php initialize do dependencies

**À tester**
-
- générer une commande : module_conf => 
  - php ormframework.php add do command -p script_name=module_conf
  - php ormframework.php add do method -p name=save command=module_conf
  - utiliser le manager de services pour récuprer la conf des modules et récupérer le module qui vous interesse.
  - utiliser $this->get_by_name($variable) pour récupérer les paramètres de la commande qui vous interesse.

**Voir ce(s) site(s) pour:**
  - **La spec OAuth2:** 
 	- http://www.bubblecode.net/fr/2016/01/22/comprendre-oauth2