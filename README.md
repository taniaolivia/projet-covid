# Projet-Covid-19-Tania_OLIVIA-LP2

- D'abord, lancez le docker avec la commande "docker-compose up"
- Ensuite, lancez votre navigateur et tapez ce lien "http://localhost:8080" pour aller sur le site de Covid
- Dans ce projet, la base de données est vite. C'est pourquoi, il faut d'abord créer/s'inscrire au moins deux comptes avant de pouvoir utiliser toutes les fonctionnalités en cliquant le bouton "Register" et surtout pour pouvoir faire un message privé entre ami.
- Pour voir le phpmyadmin, vous devez lancer "http://localhost:8081" dans votre navigateur.

**ATTENTION!**
- Pour tester la localisation, il faut enlever la sécurité dans le fichier index.php sinon vous allez avoir un erreur à cause de la sécurité. Commentez cette ligne "$app->add('csrf')" dans le fichier public/index.php.
- Ensuite, après la création de compte, dans la page "my profile", il faut que d'abord vous cliquez le bouton "Get my location" afin que votre position est ajoutée dans la base de données de la liste des personnes infectées. 
- Si, vous n'êtes pas infectés, vous ne pouvez pas voir votre position dans le map de "personnes infectées".
