# projet-12-collexion-back

### Cloner le repo <br>
```bash
git clone git@github.com:O-clock-Falafel/projet-12-collexion-back.git
```
## Sur votre projet, commandes a executés :

###  Installer vos dépendances 
```bash
php bin/console composer install
```
### Configurer votre .env
```php
DATABASE_URL="mysql://"utilisateur":"motdepasse"@127.0.0.1:3306/"nombdd"?serverVersion="version"-MariaDB&charset=utf8mb4"
```
Cette commande ci-dessous va lire le fichier .env, plus précisément la ligne ou il y a `DATABASE_URL=...` (la config de la bdd), et va créer la base de donnée si besoin.
```bash
php bin/console doctrine:database:create
```
### Lancer les migrations
C'est une sorte de 'script' qui va lire tout le contenu des entités (nom de l'entité ainsi que les propriétés) et va en faire une grosse commande SQL qui va créer le nécessaire. Cette commande aura seulement pour consequence de creer un fichier Version dans votre dossier migrations, elle ne rajoute rien en bdd (pour l'instant).
```bash
php bin/console make:migration
```
La seconde commande va éxécuter ce fichier de migration pour que le tout soit bien créer dans notre bdd
```bash
php bin/console doctrine:migrations:migrate
```
### Lancer les Fixtures
On va maintenant lancé les fixtures pour remplir un peu notre bdd, pour cela :
```bash
php bin/console doctrine:fixtures:load
```
### JWT Token
Nous avons un bundle qui se nomme "lexik/jwt-authentication-bundle" pour tout ce qui concerne l' authentification et notament pour envoyer des token lorsque un user se logue mais pour cela il faut générer des clés SSL et la commande est la suivante :
```bash
php bin/console lexik:jwt:generate-keypair
```
Vos clés atterriront dans votre dossier config/jwt/private.pemet config/jwt/public.pem (sauf si vous avez configuré un chemin différent).

