-- Stone-Shop — Fonctions PL/pgSQL
-- Extrait du dump complet (backups/Dumps/stone_shop.sql)
-- 57 fonctions : ajout_*, get_*, modifier_*, effacer_*, etc.

--
-- Name: ajout_admin(character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_admin(p_nom character varying, p_prenom character varying, p_email character varying, p_mdp character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO admin(nom_admin, prenom_admin, email_admin, mot_de_passe)
    VALUES(p_nom, p_prenom, p_email, p_mdp)
    ON CONFLICT (email_admin) DO NOTHING
    RETURNING id_admin INTO retour;
    IF retour IS NULL THEN RETURN -1; END IF;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_admin(p_nom character varying, p_prenom character varying, p_email character varying, p_mdp character varying) OWNER TO postgres;

--
-- Name: ajout_adresse(integer, character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_adresse(p_id_client integer, p_type character varying, p_nom_dest character varying, p_rue character varying, p_num character varying, p_boite character varying, p_cp character varying, p_ville character varying, p_pays character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO adresse(id_client, type_adresse, nom_destinataire, rue, numero, boite, code_postal, ville, pays)
    VALUES(p_id_client, p_type, p_nom_dest, p_rue, p_num, p_boite, p_cp, p_ville, p_pays)
    RETURNING id_adresse INTO retour;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_adresse(p_id_client integer, p_type character varying, p_nom_dest character varying, p_rue character varying, p_num character varying, p_boite character varying, p_cp character varying, p_ville character varying, p_pays character varying) OWNER TO postgres;

--
-- Name: ajout_avis(integer, integer, integer, character varying, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_avis(p_id_client integer, p_id_variante integer, p_note integer, p_titre character varying, p_commentaire text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO avis(id_client, id_variante, note, titre, commentaire)
    VALUES(p_id_client, p_id_variante, p_note, p_titre, p_commentaire)
    RETURNING id_avis INTO retour;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_avis(p_id_client integer, p_id_variante integer, p_note integer, p_titre character varying, p_commentaire text) OWNER TO postgres;

--
-- Name: ajout_categorie(character varying, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_categorie(p_nom character varying, p_parent integer, p_image character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO categorie(nom_categorie, id_categorie_parent, image_categorie)
    VALUES(p_nom, p_parent, p_image)
    ON CONFLICT (nom_categorie) DO NOTHING
    RETURNING id_categorie INTO retour;
    IF retour IS NULL THEN RETURN -1; END IF;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_categorie(p_nom character varying, p_parent integer, p_image character varying) OWNER TO postgres;

--
-- Name: ajout_client(character varying, character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_client(p_nom character varying, p_prenom character varying, p_email character varying, p_mdp character varying, p_tel character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO client(nom_client, prenom_client, email_client, mot_de_passe, telephone)
    VALUES(p_nom, p_prenom, p_email, p_mdp, p_tel)
    ON CONFLICT (email_client) DO NOTHING
    RETURNING id_client INTO retour;
    IF retour IS NULL THEN RETURN -1; END IF;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_client(p_nom character varying, p_prenom character varying, p_email character varying, p_mdp character varying, p_tel character varying) OWNER TO postgres;

--
-- Name: ajout_code_promo(character varying, numeric, timestamp without time zone, timestamp without time zone, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_code_promo(p_code character varying, p_taux numeric, p_debut timestamp without time zone, p_fin timestamp without time zone, p_usage_max integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO code_promo(code, taux_reduction, date_debut, date_fin, usage_max)
    VALUES(p_code, p_taux, p_debut, p_fin, p_usage_max)
    ON CONFLICT (code) DO NOTHING
    RETURNING id_code_promo INTO retour;
    IF retour IS NULL THEN RETURN -1; END IF;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_code_promo(p_code character varying, p_taux numeric, p_debut timestamp without time zone, p_fin timestamp without time zone, p_usage_max integer) OWNER TO postgres;

--
-- Name: ajout_conversation(integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_conversation(p_id_client integer, p_sujet character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO conversation(id_client, sujet) VALUES(p_id_client, p_sujet)
    RETURNING id_conversation INTO retour;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_conversation(p_id_client integer, p_sujet character varying) OWNER TO postgres;

--
-- Name: ajout_image(integer, character varying, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_image(p_id_variante integer, p_url character varying, p_ordre integer, p_alt character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO image_produit(id_variante, url_image, ordre, alt_text)
    VALUES(p_id_variante, p_url, p_ordre, p_alt)
    RETURNING id_image INTO retour;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_image(p_id_variante integer, p_url character varying, p_ordre integer, p_alt character varying) OWNER TO postgres;

--
-- Name: ajout_liste_envie(character varying, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_liste_envie(p_session character varying, p_id_variante integer, p_id_client integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO liste_envie(id_session, id_variante, id_client) VALUES(p_session, p_id_variante, p_id_client);
    RETURN 1;
EXCEPTION
    WHEN unique_violation THEN RETURN -1;
    WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_liste_envie(p_session character varying, p_id_variante integer, p_id_client integer) OWNER TO postgres;

--
-- Name: ajout_message_chat(integer, character varying, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_message_chat(p_id_conv integer, p_exp_type character varying, p_contenu text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO message_chat(id_conversation, expediteur_type, contenu)
    VALUES(p_id_conv, p_exp_type, p_contenu)
    RETURNING id_message INTO retour;
    IF p_exp_type = 'support' THEN
        UPDATE conversation SET statut = 'en_cours' WHERE id_conversation = p_id_conv AND statut = 'ouverte';
    END IF;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_message_chat(p_id_conv integer, p_exp_type character varying, p_contenu text) OWNER TO postgres;

--
-- Name: ajout_message_contact(integer, character varying, character varying, character varying, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_message_contact(p_id_client integer, p_nom character varying, p_email character varying, p_sujet character varying, p_contenu text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO message_contact(id_client, nom_contact, email_contact, sujet, contenu)
    VALUES(p_id_client, p_nom, p_email, p_sujet, p_contenu)
    RETURNING id_message INTO retour;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_message_contact(p_id_client integer, p_nom character varying, p_email character varying, p_sujet character varying, p_contenu text) OWNER TO postgres;

--
-- Name: ajout_panier(character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_panier(p_session character varying, p_id_client integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO panier(id_session, id_client) VALUES(p_session, p_id_client)
    ON CONFLICT (id_session) DO NOTHING
    RETURNING id_panier INTO retour;
    IF retour IS NULL THEN SELECT id_panier INTO retour FROM panier WHERE id_session = p_session; END IF;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_panier(p_session character varying, p_id_client integer) OWNER TO postgres;

--
-- Name: ajout_produit(integer, character varying, text, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_produit(p_id_cat integer, p_nom character varying, p_desc text, p_fiche character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO produit(id_categorie, nom_produit, description_courte, fiche_technique_url)
    VALUES(p_id_cat, p_nom, p_desc, p_fiche)
    RETURNING id_produit INTO retour;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_produit(p_id_cat integer, p_nom character varying, p_desc text, p_fiche character varying) OWNER TO postgres;

--
-- Name: ajout_promotion(character varying, numeric, timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_promotion(p_nom character varying, p_taux numeric, p_debut timestamp without time zone, p_fin timestamp without time zone) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO promotion(nom_promotion, taux_reduction, date_debut, date_fin)
    VALUES(p_nom, p_taux, p_debut, p_fin)
    RETURNING id_promotion INTO retour;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_promotion(p_nom character varying, p_taux numeric, p_debut timestamp without time zone, p_fin timestamp without time zone) OWNER TO postgres;

--
-- Name: ajout_root(character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_root(p_login character varying, p_mdp character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO root(login_root, mot_de_passe)
    VALUES(p_login, p_mdp)
    ON CONFLICT (login_root) DO NOTHING
    RETURNING id_root INTO retour;
    IF retour IS NULL THEN RETURN -1; END IF;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_root(p_login character varying, p_mdp character varying) OWNER TO postgres;

--
-- Name: ajout_support(character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_support(p_nom character varying, p_prenom character varying, p_email character varying, p_mdp character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO support(nom_support, prenom_support, email_support, mot_de_passe)
    VALUES(p_nom, p_prenom, p_email, p_mdp)
    ON CONFLICT (email_support) DO NOTHING
    RETURNING id_support INTO retour;
    IF retour IS NULL THEN RETURN -1; END IF;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_support(p_nom character varying, p_prenom character varying, p_email character varying, p_mdp character varying) OWNER TO postgres;

--
-- Name: ajout_transporteur(character varying, character varying, numeric); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_transporteur(p_nom character varying, p_delai character varying, p_frais numeric) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO transporteur(nom_transporteur, delai_estime, frais_livraison)
    VALUES(p_nom, p_delai, p_frais)
    ON CONFLICT (nom_transporteur) DO NOTHING
    RETURNING id_transporteur INTO retour;
    IF retour IS NULL THEN RETURN -1; END IF;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_transporteur(p_nom character varying, p_delai character varying, p_frais numeric) OWNER TO postgres;

--
-- Name: ajout_variante(integer, character varying, character varying, numeric, integer, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_variante(p_id_produit integer, p_nom character varying, p_sku character varying, p_prix numeric, p_stock integer, p_couleur character varying, p_capacite character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO variante_produit(id_produit, nom_variante, sku, prix, stock, couleur, capacite)
    VALUES(p_id_produit, p_nom, p_sku, p_prix, p_stock, p_couleur, p_capacite)
    ON CONFLICT (sku) DO NOTHING
    RETURNING id_variante INTO retour;
    IF retour IS NULL THEN RETURN -1; END IF;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_variante(p_id_produit integer, p_nom character varying, p_sku character varying, p_prix numeric, p_stock integer, p_couleur character varying, p_capacite character varying) OWNER TO postgres;

--
-- Name: ajout_variante_panier(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajout_variante_panier(p_id_panier integer, p_id_variante integer, p_qte integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO panier_variante(id_panier, id_variante, quantite) VALUES(p_id_panier, p_id_variante, p_qte)
    ON CONFLICT (id_panier, id_variante) DO UPDATE SET quantite = panier_variante.quantite + EXCLUDED.quantite;
    UPDATE panier SET date_modification = now() WHERE id_panier = p_id_panier;
    RETURN 1;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajout_variante_panier(p_id_panier integer, p_id_variante integer, p_qte integer) OWNER TO postgres;

--
-- Name: ajouter_ligne_commande(integer, integer, integer, numeric); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ajouter_ligne_commande(p_id_commande integer, p_id_variante integer, p_qte integer, p_prix numeric) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO commande_variante(id_commande, id_variante, quantite, prix_unitaire)
    VALUES(p_id_commande, p_id_variante, p_qte, p_prix);
    UPDATE variante_produit SET stock = stock - p_qte WHERE id_variante = p_id_variante;
    RETURN 1;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.ajouter_ligne_commande(p_id_commande integer, p_id_variante integer, p_qte integer, p_prix numeric) OWNER TO postgres;

--
-- Name: assigner_support(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.assigner_support(p_id_conv integer, p_id_support integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE conversation SET id_support = p_id_support, statut = 'en_cours' WHERE id_conversation = p_id_conv;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.assigner_support(p_id_conv integer, p_id_support integer) OWNER TO postgres;

--
-- Name: creer_commande(integer, integer, integer, integer, integer, numeric, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.creer_commande(p_id_client integer, p_id_addr_liv integer, p_id_addr_fact integer, p_id_transp integer, p_id_code_promo integer, p_total numeric, p_methode character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE retour INT;
BEGIN
    INSERT INTO commande(id_client, id_adresse_livraison, id_adresse_facturation, id_transporteur, id_code_promo, total_commande, methode_paiement)
    VALUES(p_id_client, p_id_addr_liv, p_id_addr_fact, p_id_transp, p_id_code_promo, p_total, p_methode)
    RETURNING id_commande INTO retour;
    RETURN retour;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.creer_commande(p_id_client integer, p_id_addr_liv integer, p_id_addr_fact integer, p_id_transp integer, p_id_code_promo integer, p_total numeric, p_methode character varying) OWNER TO postgres;

--
-- Name: effacer_produit(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.effacer_produit(p_id integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    DELETE FROM produit WHERE id_produit = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.effacer_produit(p_id integer) OWNER TO postgres;

--
-- Name: effacer_variante(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.effacer_variante(p_id integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    DELETE FROM variante_produit WHERE id_variante = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.effacer_variante(p_id integer) OWNER TO postgres;

--
-- Name: fermer_conversation(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fermer_conversation(p_id integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE conversation SET statut = 'fermee', date_fermeture = now() WHERE id_conversation = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.fermer_conversation(p_id integer) OWNER TO postgres;

--
-- Name: fusionner_panier(character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fusionner_panier(p_session character varying, p_id_client integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_panier_anon   INT;
    v_panier_client INT;
BEGIN
    SELECT id_panier INTO v_panier_anon FROM panier WHERE id_session = p_session AND id_client IS NULL;
    IF v_panier_anon IS NULL THEN RETURN 0; END IF;

    SELECT id_panier INTO v_panier_client FROM panier WHERE id_client = p_id_client;
    IF v_panier_client IS NULL THEN
        UPDATE panier SET id_client = p_id_client WHERE id_panier = v_panier_anon;
        RETURN 1;
    END IF;

    INSERT INTO panier_variante(id_panier, id_variante, quantite)
    SELECT v_panier_client, id_variante, quantite FROM panier_variante WHERE id_panier = v_panier_anon
    ON CONFLICT (id_panier, id_variante) DO UPDATE SET quantite = GREATEST(panier_variante.quantite, EXCLUDED.quantite);

    DELETE FROM panier WHERE id_panier = v_panier_anon;
    RETURN 1;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.fusionner_panier(p_session character varying, p_id_client integer) OWNER TO postgres;

--
-- Name: get_admin(character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.get_admin(p_email character varying, p_mdp character varying) RETURNS TABLE(id_admin integer, nom_admin character varying, prenom_admin character varying, email_admin character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY SELECT a.id_admin, a.nom_admin, a.prenom_admin, a.email_admin
    FROM admin a WHERE a.email_admin = p_email AND a.mot_de_passe = p_mdp;
END; $$;


ALTER FUNCTION public.get_admin(p_email character varying, p_mdp character varying) OWNER TO postgres;

--
-- Name: get_admin_complet_par_email(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.get_admin_complet_par_email(p_email character varying) RETURNS TABLE(id_admin integer, nom_admin character varying, prenom_admin character varying, email_admin character varying, mot_de_passe character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY SELECT a.id_admin, a.nom_admin, a.prenom_admin, a.email_admin, a.mot_de_passe
    FROM admin a WHERE a.email_admin = p_email;
END; $$;


ALTER FUNCTION public.get_admin_complet_par_email(p_email character varying) OWNER TO postgres;

--
-- Name: get_client(character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.get_client(p_email character varying, p_mdp character varying) RETURNS TABLE(id_client integer, nom_client character varying, prenom_client character varying, email_client character varying, telephone character varying, date_inscription timestamp without time zone)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY SELECT c.id_client, c.nom_client, c.prenom_client, c.email_client, c.telephone, c.date_inscription
    FROM client c WHERE c.email_client = p_email AND c.mot_de_passe = p_mdp;
END; $$;


ALTER FUNCTION public.get_client(p_email character varying, p_mdp character varying) OWNER TO postgres;

--
-- Name: get_client_complet_par_email(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.get_client_complet_par_email(p_email character varying) RETURNS TABLE(id_client integer, nom_client character varying, prenom_client character varying, email_client character varying, mot_de_passe character varying, telephone character varying, date_inscription timestamp without time zone)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY SELECT c.id_client, c.nom_client, c.prenom_client, c.email_client,
                        c.mot_de_passe, c.telephone, c.date_inscription
    FROM client c WHERE c.email_client = p_email;
END; $$;


ALTER FUNCTION public.get_client_complet_par_email(p_email character varying) OWNER TO postgres;

--
-- Name: get_client_par_email(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.get_client_par_email(p_email character varying) RETURNS TABLE(id_client integer, prenom_client character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY SELECT c.id_client, c.prenom_client
    FROM client c WHERE c.email_client = p_email;
END; $$;


ALTER FUNCTION public.get_client_par_email(p_email character varying) OWNER TO postgres;

--
-- Name: get_root_complet_par_login(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.get_root_complet_par_login(p_login character varying) RETURNS TABLE(id_root integer, login_root character varying, mot_de_passe character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY SELECT r.id_root, r.login_root, r.mot_de_passe
    FROM root r WHERE r.login_root = p_login;
END; $$;


ALTER FUNCTION public.get_root_complet_par_login(p_login character varying) OWNER TO postgres;

--
-- Name: get_support(character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.get_support(p_email character varying, p_mdp character varying) RETURNS TABLE(id_support integer, nom_support character varying, prenom_support character varying, email_support character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY SELECT s.id_support, s.nom_support, s.prenom_support, s.email_support
    FROM support s WHERE s.email_support = p_email AND s.mot_de_passe = p_mdp;
END; $$;


ALTER FUNCTION public.get_support(p_email character varying, p_mdp character varying) OWNER TO postgres;

--
-- Name: get_support_complet_par_email(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.get_support_complet_par_email(p_email character varying) RETURNS TABLE(id_support integer, nom_support character varying, prenom_support character varying, email_support character varying, mot_de_passe character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY SELECT s.id_support, s.nom_support, s.prenom_support, s.email_support, s.mot_de_passe
    FROM support s WHERE s.email_support = p_email;
END; $$;


ALTER FUNCTION public.get_support_complet_par_email(p_email character varying) OWNER TO postgres;

--
-- Name: incrementer_usage_code_promo(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.incrementer_usage_code_promo(p_id integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE code_promo SET usage_actuel = usage_actuel + 1 WHERE id_code_promo = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.incrementer_usage_code_promo(p_id integer) OWNER TO postgres;

--
-- Name: lier_promotion_variante(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.lier_promotion_variante(p_id_promo integer, p_id_variante integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO promotion_variante(id_promotion, id_variante) VALUES(p_id_promo, p_id_variante)
    ON CONFLICT DO NOTHING;
    RETURN 1;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.lier_promotion_variante(p_id_promo integer, p_id_variante integer) OWNER TO postgres;

--
-- Name: marquer_contact_traite(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.marquer_contact_traite(p_id integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE message_contact SET traite = true WHERE id_message = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.marquer_contact_traite(p_id integer) OWNER TO postgres;

--
-- Name: moderer_avis(integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.moderer_avis(p_id integer, p_statut character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE avis SET modere = p_statut WHERE id_avis = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.moderer_avis(p_id integer, p_statut character varying) OWNER TO postgres;

--
-- Name: modifier_categorie(integer, character varying, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.modifier_categorie(p_id integer, p_nom character varying, p_parent integer, p_image character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE categorie SET nom_categorie=p_nom, id_categorie_parent=p_parent, image_categorie=p_image
    WHERE id_categorie = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.modifier_categorie(p_id integer, p_nom character varying, p_parent integer, p_image character varying) OWNER TO postgres;

--
-- Name: modifier_client(integer, character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.modifier_client(p_id integer, p_nom character varying, p_prenom character varying, p_email character varying, p_tel character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE client SET nom_client=p_nom, prenom_client=p_prenom, email_client=p_email, telephone=p_tel
    WHERE id_client = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.modifier_client(p_id integer, p_nom character varying, p_prenom character varying, p_email character varying, p_tel character varying) OWNER TO postgres;

--
-- Name: modifier_mdp_client(integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.modifier_mdp_client(p_id integer, p_mdp character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE client SET mot_de_passe = p_mdp WHERE id_client = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.modifier_mdp_client(p_id integer, p_mdp character varying) OWNER TO postgres;

--
-- Name: modifier_produit(integer, integer, character varying, text, character varying, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.modifier_produit(p_id integer, p_id_cat integer, p_nom character varying, p_desc text, p_fiche character varying, p_actif boolean) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE produit SET id_categorie=p_id_cat, nom_produit=p_nom, description_courte=p_desc,
                       fiche_technique_url=p_fiche, actif=p_actif WHERE id_produit = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.modifier_produit(p_id integer, p_id_cat integer, p_nom character varying, p_desc text, p_fiche character varying, p_actif boolean) OWNER TO postgres;

--
-- Name: retirer_liste_envie(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.retirer_liste_envie(p_id integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    DELETE FROM liste_envie WHERE id_liste_envie = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.retirer_liste_envie(p_id integer) OWNER TO postgres;

--
-- Name: retirer_liste_envie_var(character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.retirer_liste_envie_var(p_session character varying, p_variante integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$ BEGIN DELETE FROM liste_envie WHERE id_session = p_session AND id_variante = p_variante; IF FOUND THEN RETURN 1; END IF; RETURN 0; EXCEPTION WHEN OTHERS THEN RETURN 0; END; $$;


ALTER FUNCTION public.retirer_liste_envie_var(p_session character varying, p_variante integer) OWNER TO postgres;

--
-- Name: retirer_variante_panier(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.retirer_variante_panier(p_id_panier integer, p_id_variante integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    DELETE FROM panier_variante WHERE id_panier = p_id_panier AND id_variante = p_id_variante;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.retirer_variante_panier(p_id_panier integer, p_id_variante integer) OWNER TO postgres;

--
-- Name: supprimer_adresse(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.supprimer_adresse(p_id integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    DELETE FROM adresse WHERE id_adresse = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.supprimer_adresse(p_id integer) OWNER TO postgres;

--
-- Name: supprimer_categorie(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.supprimer_categorie(p_id integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    DELETE FROM categorie WHERE id_categorie = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.supprimer_categorie(p_id integer) OWNER TO postgres;

--
-- Name: supprimer_image(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.supprimer_image(p_id integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    DELETE FROM image_produit WHERE id_image = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.supprimer_image(p_id integer) OWNER TO postgres;

--
-- Name: supprimer_promotion(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.supprimer_promotion(p_id integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    DELETE FROM promotion WHERE id_promotion = p_id;
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.supprimer_promotion(p_id integer) OWNER TO postgres;

--
-- Name: update_champ_client(integer, character varying, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_champ_client(p_id integer, p_champ character varying, p_valeur text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    EXECUTE format('UPDATE client SET %I = %L WHERE id_client = %L', p_champ, p_valeur, p_id);
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.update_champ_client(p_id integer, p_champ character varying, p_valeur text) OWNER TO postgres;

--
-- Name: update_champ_commande(integer, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_champ_commande(p_id integer, p_champ character varying, p_valeur character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    EXECUTE format('UPDATE commande SET %I = %L WHERE id_commande = %L', p_champ, p_valeur, p_id);
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.update_champ_commande(p_id integer, p_champ character varying, p_valeur character varying) OWNER TO postgres;

--
-- Name: update_champ_produit(integer, character varying, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_champ_produit(p_id integer, p_champ character varying, p_valeur text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    EXECUTE format('UPDATE produit SET %I = %L WHERE id_produit = %L', p_champ, p_valeur, p_id);
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.update_champ_produit(p_id integer, p_champ character varying, p_valeur text) OWNER TO postgres;

--
-- Name: update_champ_transporteur(integer, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_champ_transporteur(p_id integer, p_champ character varying, p_valeur character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    EXECUTE format('UPDATE transporteur SET %I = %L WHERE id_transporteur = %L', p_champ, p_valeur, p_id);
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.update_champ_transporteur(p_id integer, p_champ character varying, p_valeur character varying) OWNER TO postgres;

--
-- Name: update_champ_variante(integer, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_champ_variante(p_id integer, p_champ character varying, p_valeur character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    EXECUTE format('UPDATE variante_produit SET %I = %L WHERE id_variante = %L', p_champ, p_valeur, p_id);
    IF FOUND THEN RETURN 1; END IF; RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.update_champ_variante(p_id integer, p_champ character varying, p_valeur character varying) OWNER TO postgres;

--
-- Name: update_quantite_panier(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_quantite_panier(p_id_panier integer, p_id_variante integer, p_qte integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE panier_variante SET quantite = p_qte WHERE id_panier = p_id_panier AND id_variante = p_id_variante;
    IF FOUND THEN UPDATE panier SET date_modification = now() WHERE id_panier = p_id_panier; RETURN 1; END IF;
    RETURN 0;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.update_quantite_panier(p_id_panier integer, p_id_variante integer, p_qte integer) OWNER TO postgres;

--
-- Name: valider_commande(integer, integer, integer, integer, integer, numeric, character varying, character varying, jsonb); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.valider_commande(p_id_client integer, p_id_addr_liv integer, p_id_addr_fact integer, p_id_transp integer, p_id_code_promo integer, p_total numeric, p_methode character varying, p_id_session character varying, p_lignes jsonb) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_id_commande INT;
    v_id_panier   INT;
    v_ligne       JSONB;
BEGIN
    INSERT INTO commande(id_client, id_adresse_livraison, id_adresse_facturation,
                         id_transporteur, id_code_promo, total_commande, methode_paiement)
    VALUES(p_id_client, p_id_addr_liv, p_id_addr_fact, p_id_transp,
           p_id_code_promo, p_total, p_methode)
    RETURNING id_commande INTO v_id_commande;

    FOR v_ligne IN SELECT * FROM jsonb_array_elements(p_lignes) LOOP
        INSERT INTO commande_variante(id_commande, id_variante, quantite, prix_unitaire)
        VALUES(v_id_commande,
               (v_ligne->>'id_variante')::INT,
               (v_ligne->>'qte')::INT,
               (v_ligne->>'prix')::DECIMAL);
        UPDATE variante_produit
           SET stock = stock - (v_ligne->>'qte')::INT
         WHERE id_variante = (v_ligne->>'id_variante')::INT;
    END LOOP;

    SELECT id_panier INTO v_id_panier FROM panier WHERE id_session = p_id_session;
    IF v_id_panier IS NOT NULL THEN
        DELETE FROM panier_variante WHERE id_panier = v_id_panier;
    END IF;

    RETURN v_id_commande;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.valider_commande(p_id_client integer, p_id_addr_liv integer, p_id_addr_fact integer, p_id_transp integer, p_id_code_promo integer, p_total numeric, p_methode character varying, p_id_session character varying, p_lignes jsonb) OWNER TO postgres;

--
-- Name: vider_panier(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.vider_panier(p_session character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE v_id_panier INT;
BEGIN
    SELECT id_panier INTO v_id_panier FROM panier WHERE id_session = p_session;
    IF v_id_panier IS NULL THEN RETURN 0; END IF;
    DELETE FROM panier_variante WHERE id_panier = v_id_panier;
    RETURN 1;
EXCEPTION WHEN OTHERS THEN RETURN 0;
END; $$;


ALTER FUNCTION public.vider_panier(p_session character varying) OWNER TO postgres;

