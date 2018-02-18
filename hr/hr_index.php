<?php

define('ROOT_PATH', '../');
require ROOT_PATH . 'define.php';
defined( '_PHP_CONGES' ) or die( 'Restricted access' );

include_once ROOT_PATH .'fonctions_conges.php' ;
include_once INCLUDE_PATH .'fonction.php';
include_once INCLUDE_PATH .'session.php';
include_once ROOT_PATH .'fonctions_calcul.php';

// verif des droits du user à afficher la page
verif_droits_user("is_hr");


/*************************************/
// recup des parametres reçus :
// SERVER
$PHP_SELF = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
// GET / POST
$onglet = getpost_variable('onglet', "page_principale");


/*********************************/
/*   COMPOSITION DES ONGLETS...  */
/*********************************/

$onglets = array();


$onglets['page_principale'] = _('resp_menu_button_retour_main');

if( $_SESSION['config']['user_saisie_demande'] )
    $onglets['traitement_demandes'] = _('resp_menu_button_traite_demande');

// if( $_SESSION['config']['resp_ajoute_conges'] )
    $onglets['ajout_conges'] = _('resp_ajout_conges_titre');
    $onglets['jours_chomes'] = _('admin_button_jours_chomes_1');

$onglets['cloture_year'] = _('resp_cloture_exercice_titre');
$onglets['liste_planning'] = _('hr_liste_planning');
//$onglets['ajout_planning'] = _('hr_ajout_planning');

if ( !isset($onglets[ $onglet ]) && !in_array($onglet, ['traite_user', 'modif_planning', 'ajout_planning']))
    $onglet = 'page_principale';

/*********************************/
/*   COMPOSITION DU HEADER...    */
/*********************************/

$add_css = '<style>#onglet_menu .onglet{ width: '. (str_replace(',', '.', 100 / count($onglets) )).'% ;}</style>';
header_menu('', 'Libertempo : '._('resp_menu_button_mode_hr'),$add_css);

/*********************************/
/*   AFFICHAGE DES ONGLETS...  */
/*********************************/

echo '<div id="onglet_menu">';
foreach($onglets as $key => $title) {
    echo '<div class="onglet '.($onglet == $key ? ' active': '').'" >
        <a href="'.$PHP_SELF.'?onglet='.$key.'">'. $title .'</a>
    </div>';
}
echo '</div>';


/*********************************/
/*   AFFICHAGE DE L'ONGLET ...    */
/*********************************/


/** initialisation des tableaux des types de conges/absences  **/
// recup du tableau des types de conges (seulement les conges)
$tab_type_cong=recup_tableau_types_conges();

// recup du tableau des types de conges exceptionnels (seulement les conges exceptionnels)
//    if ($_SESSION['config']['gestion_conges_exceptionnels'])
$tab_type_conges_exceptionnels=recup_tableau_types_conges_exceptionnels();

echo '<div class="'.$onglet.' main-content">';
    include_once ROOT_PATH . 'hr/hr_'.$onglet.'.php';
echo '</div>';

/*********************************/
/*   AFFICHAGE DU BOTTOM ...   */
/*********************************/

bottom();
exit;
