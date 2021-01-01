# RFWPT (Really Free Wordpress Theme)

Thème Wordpress gratuit et libre sans mauvaise surprise.

## Usage

### Menu de navigation

Le menu de navigation horizontal peut avoir des sous-menus qui seront affichés au passage de la souris.
Il en est de même pour le pied de page.

### Eléments mis en avant

Pour mettre un icône, il faut rajouter le code Font Awesome (https://fontawesome.com/icons) devant le texte et mettre un pipe (|) entre les deux : 
```
global|Site internet
```

## Frameworks utilisés
- CSS : Bulma (https://bulma.io/)
- Fonts : Font Awesome 5 (https://fontawesome.com/)

## Tester sur docker
```
docker-compose up -d
```
http://localhost:9090

## Générer fichier .pot
```
mkdir tools
cd tools
svn checkout http://i18n.svn.wordpress.org/tools/trunk .
php makepot.php wp-theme ../
mv wp-theme.pot ../languages/rfwpt.pot
```