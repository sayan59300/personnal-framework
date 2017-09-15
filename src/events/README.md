# README #

### Contient le fichier contenant les listeners du système d'évènement - Les nouveaux listeners seront à mettre à la fin du fichier ###

### Pour ajouter un listener

```php
$emitter->on('exemple.added', function () {
    // YOUR LOGIC HERE
});
```