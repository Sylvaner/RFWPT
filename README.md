# RFWPT (Really Free Wordpress Theme)

Thème Wordpress gratuit et libre sans mauvaise surprise.

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