<?php
namespace App\Libraries\Calendrier\Evenements;

/**
 * Evenements de weekend
 *
 * @since  1.9
 * @author Prytoegrian <prytoegrian@protonmail.com>
 * @author Wouldsmina
 *
 * Ne doit être contacté que par \App\Libraries\Calendrier\Evenements
 * @TODO supprimer le requétage à la migration vers le MVC REST
 */
class Weekend
{
    /**
     * @var int Identifiant du samedi dans les fonctions de date
     */
    const JOUR_SAMEDI = 6;

    /**
     * @var int Identifiant du dimanche dans les fonctions de date
     */
    const JOUR_DIMANCHE = 0;

    public function __construct(\includes\SQL $db)
    {
        $this->db = $db;
    }

    /**
    * @var \includes\SQL Objet de DB
    */
    private $db;

    /**
     * Retourne la liste des jours fériés relative à la période demandée
     *
     * @param \DateTimeInterface $dateDebut
     * @param \DateTimeInterface $dateFin
     *
     * @return array
     */
    public function getListe(\DateTimeInterface $dateDebut, \DateTimeInterface $dateFin)
    {
        return array_merge($this->getListeSamedi($dateDebut, $dateFin), $this->getListeDimanche($dateDebut, $dateFin));
    }

    /**
     * Retourne la liste des occurences des samedi
     *
     * @return array
     */
    private function getListeSamedi(\DateTimeInterface $dateDebut, \DateTimeInterface $dateFin)
    {
        if (!$this->isSamediTravaille()) {
            return $this->getListeJourSemaine(static::JOUR_SAMEDI, $dateDebut, $dateFin);
        }

        return [];
    }

    /**
     * Retourne la liste des occurences des dimanche
     *
     * @return array
     */
    private function getListeDimanche(\DateTimeInterface $dateDebut, \DateTimeInterface $dateFin)
    {
        if (!$this->isDimancheTravaille()) {
            return $this->getListeJourSemaine(static::JOUR_DIMANCHE, $dateDebut, $dateFin);
        }

        return [];
    }

    /**
     * Retourne la liste des occurences des jours de la semaine dans une période donnée
     *
     * @param int $jourSemaine
     *
     * @return array
     */
    private function getListeJourSemaine($jourSemaine, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin)
    {
        $debut = $dateDebut->getTimestamp();
        $fin = $dateFin->getTimestamp();
        if ($debut > $fin) { // ce devrait être une assertion
            throw new \Exception('Date de début supérieure à date de fin');
        }

        $listeJourSemaine = [];
        while ($debut <= $fin) {
            /* Si on est sur le jour clé, on append la liste et on tape une semaine au dessus */
            if (date('w', $debut) == $jourSemaine) {
                $listeJourSemaine[] = date('Y-m-d', $debut);
                $debut = strtotime('+1 week', $debut);
            } else {
                $debut = strtotime('+1 day', $debut);
            }
        }
        sort($listeJourSemaine);

        return $listeJourSemaine;
    }


    /*
     * SQL
     * @TODO dégager lors de la mise en place de l'objet configuration
     */


    private function isSamediTravaille()
    {
        $req = 'SELECT *
            FROM conges_config
            WHERE conf_nom = "samedi_travail" LIMIT 1';

        $res = $this->db->query($req)->fetch_assoc();

        return 'TRUE' === $res['conf_valeur'];
    }

    private function isDimancheTravaille()
    {
        $req = 'SELECT *
                FROM conges_config
                WHERE conf_nom = "dimanche_travail" LIMIT 1';

        $res = $this->db->query($req)->fetch_assoc();

        return 'TRUE' === $res['conf_valeur'];
    }
}
