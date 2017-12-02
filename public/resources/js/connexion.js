/**
 * Affiche ou cache l'élément connexion
 *
 * @returns {undefined}
 */
$('.volet').click(
    function () {
        $volet = $(".connexion");
        if ($volet.css('right') === '-250px') {
            $volet.css({'right': '0'});
        } else {
            $volet.css({'right': '-250px'});
        }
    }
);
