<?php

declare(strict_types=1);

class Commande implements JsonSerializable
{
    public function __construct(
        public readonly int     $id_commande,
        public readonly int     $id_client,
        public readonly int     $id_adresse_livraison,
        public readonly int     $id_adresse_facturation,
        public readonly int     $id_transporteur,
        public readonly ?int    $id_code_promo,
        public readonly string  $date_commande,
        public readonly float   $total_commande,
        public readonly string  $methode_paiement,
        public readonly bool    $statut_paiement,
        public readonly string  $statut_commande,
        public readonly ?string $numero_suivi
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
