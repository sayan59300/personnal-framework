/**
 * Affiche ou cache l'élément connexion
 *
 * @returns {undefined}
 */
$('.volet').click(
    function () {
        if ($(".connexion").css('right') === '-250px') {
            $(".connexion").css({'right': '0'});
        } else {
            $(".connexion").css({'right': '-250px'});
        }
    }
);
