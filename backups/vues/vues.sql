-- Stone-Shop — Vues SQL
-- Extrait du dump complet (backups/Dumps/stone_shop.sql)
-- 6 vues : vue_avis_approuves, vue_catalogue, vue_commande_detail,
--          vue_conversations, vue_panier_complet, vue_stock_critique

-- Name: vue_avis_approuves; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vue_avis_approuves AS
 SELECT a.id_avis,
    a.note,
    a.titre,
    a.commentaire,
    a.date_avis,
    a.id_variante,
    cl.nom_client,
    cl.prenom_client,
    v.nom_variante,
    p.nom_produit
   FROM (((public.avis a
     JOIN public.client cl ON ((cl.id_client = a.id_client)))
     JOIN public.variante_produit v ON ((v.id_variante = a.id_variante)))
     JOIN public.produit p ON ((p.id_produit = v.id_produit)))
  WHERE ((a.modere)::text = 'approuve'::text);


ALTER VIEW public.vue_avis_approuves OWNER TO postgres;

--
-- Name: vue_catalogue; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vue_catalogue AS
 SELECT p.id_produit,
    p.nom_produit,
    p.description_courte,
    p.actif,
    c.id_categorie,
    c.nom_categorie,
    v.id_variante,
    v.nom_variante,
    v.sku,
    v.prix,
    v.stock,
    v.couleur,
    v.capacite,
    (v.stock > 0) AS disponible,
        CASE
            WHEN (promo.taux_reduction IS NOT NULL) THEN round((v.prix * ((1)::numeric - (promo.taux_reduction / (100)::numeric))), 2)
            ELSE v.prix
        END AS prix_final,
    promo.taux_reduction,
    img.url_image AS image_principale,
    img.alt_text,
    COALESCE(av.note_moyenne, (0)::numeric) AS note_moyenne,
    COALESCE(av.nb_avis, (0)::bigint) AS nb_avis
   FROM (((((public.produit p
     JOIN public.categorie c ON ((c.id_categorie = p.id_categorie)))
     JOIN public.variante_produit v ON ((v.id_produit = p.id_produit)))
     LEFT JOIN ( SELECT pv.id_variante,
            max(pr.taux_reduction) AS taux_reduction
           FROM (public.promotion_variante pv
             JOIN public.promotion pr ON ((pr.id_promotion = pv.id_promotion)))
          WHERE ((pr.actif = true) AND ((now() >= pr.date_debut) AND (now() <= pr.date_fin)))
          GROUP BY pv.id_variante) promo ON ((promo.id_variante = v.id_variante)))
     LEFT JOIN LATERAL ( SELECT image_produit.url_image,
            image_produit.alt_text
           FROM public.image_produit
          WHERE (image_produit.id_variante = v.id_variante)
          ORDER BY image_produit.ordre
         LIMIT 1) img ON (true))
     LEFT JOIN ( SELECT avis.id_variante,
            round(avg(avis.note), 1) AS note_moyenne,
            count(*) AS nb_avis
           FROM public.avis
          WHERE ((avis.modere)::text = 'approuve'::text)
          GROUP BY avis.id_variante) av ON ((av.id_variante = v.id_variante)))
  WHERE (p.actif = true);


ALTER VIEW public.vue_catalogue OWNER TO postgres;

--
-- Name: vue_commande_detail; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vue_commande_detail AS
 SELECT co.id_commande,
    co.date_commande,
    co.total_commande,
    co.methode_paiement,
    co.statut_paiement,
    co.statut_commande,
    co.numero_suivi,
    co.id_client,
    t.nom_transporteur,
    t.frais_livraison,
    t.delai_estime,
    cp.code AS code_promo_utilise,
    cv.quantite,
    cv.prix_unitaire,
    v.id_variante,
    v.nom_variante,
    v.couleur,
    v.capacite,
    p.nom_produit
   FROM (((((public.commande co
     JOIN public.transporteur t ON ((t.id_transporteur = co.id_transporteur)))
     LEFT JOIN public.code_promo cp ON ((cp.id_code_promo = co.id_code_promo)))
     JOIN public.commande_variante cv ON ((cv.id_commande = co.id_commande)))
     JOIN public.variante_produit v ON ((v.id_variante = cv.id_variante)))
     JOIN public.produit p ON ((p.id_produit = v.id_produit)));


ALTER VIEW public.vue_commande_detail OWNER TO postgres;

--
-- Name: vue_conversations; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vue_conversations AS
 SELECT conv.id_conversation,
    conv.sujet,
    conv.statut,
    conv.date_creation,
    conv.date_fermeture,
    cl.id_client,
    cl.nom_client,
    cl.prenom_client,
    cl.email_client,
    s.id_support,
    s.nom_support,
    s.prenom_support,
    ( SELECT count(*) AS count
           FROM public.message_chat mc
          WHERE ((mc.id_conversation = conv.id_conversation) AND (mc.lu = false) AND ((mc.expediteur_type)::text = 'client'::text))) AS messages_non_lus
   FROM ((public.conversation conv
     JOIN public.client cl ON ((cl.id_client = conv.id_client)))
     LEFT JOIN public.support s ON ((s.id_support = conv.id_support)));


ALTER VIEW public.vue_conversations OWNER TO postgres;

--
-- Name: vue_panier_complet; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vue_panier_complet AS
 SELECT pa.id_panier,
    pa.id_session,
    pa.id_client,
    pv.quantite,
    v.id_variante,
    v.nom_variante,
    v.prix,
    v.stock,
    v.couleur,
    v.capacite,
    p.id_produit,
    p.nom_produit,
    img.url_image AS image_principale,
    ((pv.quantite)::numeric * v.prix) AS sous_total
   FROM ((((public.panier pa
     JOIN public.panier_variante pv ON ((pv.id_panier = pa.id_panier)))
     JOIN public.variante_produit v ON ((v.id_variante = pv.id_variante)))
     JOIN public.produit p ON ((p.id_produit = v.id_produit)))
     LEFT JOIN LATERAL ( SELECT image_produit.url_image
           FROM public.image_produit
          WHERE (image_produit.id_variante = v.id_variante)
          ORDER BY image_produit.ordre
         LIMIT 1) img ON (true));


ALTER VIEW public.vue_panier_complet OWNER TO postgres;

--
-- Name: vue_stock_critique; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vue_stock_critique AS
 SELECT v.id_variante,
    v.nom_variante,
    v.sku,
    v.stock,
    v.prix,
    p.id_produit,
    p.nom_produit,
    c.nom_categorie
   FROM ((public.variante_produit v
     JOIN public.produit p ON ((p.id_produit = v.id_produit)))
     JOIN public.categorie c ON ((c.id_categorie = p.id_categorie)))
  WHERE ((v.stock <= 5) AND (p.actif = true))
  ORDER BY v.stock;


ALTER VIEW public.vue_stock_critique OWNER TO postgres;
