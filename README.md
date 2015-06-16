# HtmlCache
Contao Cache, speichert ausgewählte Contao Seiten als HTML ab. InsertTags werden bei Augabe ersetzt.

Nach der Installation der Erweiterung, muss die .htaccess entsprechend der cache.htaccess angepasst werden. In die erste Zeile muss zwingend `DirectoryIndex cache.php index.php` stehen. Damit der Cache entsprechend genutzt werden kann. Um den Cache auch auf Unterseiten nutzen zu können, muss am Ende der .htaccess index.php in cache.php geändert werden:

```
##
# By default, Contao adds ".html" to the generated URLs to simulate static
# HTML documents. If you change the URL suffix in the back end settings, make
# sure to change it here accordingly!
#
#   RewriteRule .*\.html$ index.php [L]   # URL suffix .html
#   RewriteRule .*\.txt$ index.php [L]    # URL suffix .txt
#   RewriteRule .*\.json$ index.php [L]   # URL suffix .json
#
# If you do not want to use an URL suffix at all, you have to add a second
# line to prevent URLs that point to folders from being rewritten (see #4031).
#
#   RewriteCond %{REQUEST_FILENAME} !-d
#   RewriteRule .* index.php [L]
#
# If you are using mod_cache, it is recommended to use the RewriteRule below,
# which adds the query string to the internal URL:
#
#   RewriteRule (.*\.html)$ index.php/$1 [L]
#
# Note that not all environments support mod_rewrite and mod_cache.
##
#RewriteRule .*\.html$ index.php [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule .*\.html$ cache.php [L]
```

##Anleitung

Es werden nur Reguläre Seiten gesichert und auch nur dann, wenn sie einen Alias besitzen. Jede Seite muss in der Sektion 'Cache' im Backend / Seitenstruktur die neue Option `Html Cache` aktiviert haben. Diese Option ist wichtig, damit nur Seiten ausgewählt werden, die sich nicht ohne Aktion des Benutzers ändern. 

Seiten die einen laufend dynamischen Inhalt haben, weil sie eine externe Datenquelle oder zeitabhängige Inhalte ausgeben, dürfen **nicht** gesichert werden. Die gesicherten Seiten sind pures HTMl mit InsertTags. Die InsertTags sind dabei das einzig dynamische dass der Cache noch hat.

##Future Features

- Dynamische Inhaltselemente / Module durch InsertTags ersetzen
- Pagination von News Cachen