--
-- PostgreSQL database dump
--

\restrict CEzoTfXRG11egxEiuvZzfWntgzrmaUGlWLffXhgKQiCldPvOSGelTBsslx6aoVa

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: admin; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.admin (
    id_admin integer NOT NULL,
    nom_admin character varying(100) NOT NULL,
    prenom_admin character varying(100) NOT NULL,
    email_admin character varying(255) NOT NULL,
    mot_de_passe character varying(255) NOT NULL
);


ALTER TABLE public.admin OWNER TO postgres;

--
-- Name: admin_id_admin_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.admin_id_admin_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.admin_id_admin_seq OWNER TO postgres;

--
-- Name: admin_id_admin_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.admin_id_admin_seq OWNED BY public.admin.id_admin;


--
-- Name: adresse; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.adresse (
    id_adresse integer NOT NULL,
    id_client integer,
    type_adresse character varying(20) NOT NULL,
    nom_destinataire character varying(200) NOT NULL,
    rue character varying(200) NOT NULL,
    numero character varying(20) NOT NULL,
    boite character varying(20),
    code_postal character varying(20) NOT NULL,
    ville character varying(100) NOT NULL,
    pays character varying(100) DEFAULT 'Belgique'::character varying,
    CONSTRAINT adresse_type_adresse_check CHECK (((type_adresse)::text = ANY ((ARRAY['livraison'::character varying, 'facturation'::character varying])::text[])))
);


ALTER TABLE public.adresse OWNER TO postgres;

--
-- Name: adresse_id_adresse_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.adresse_id_adresse_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.adresse_id_adresse_seq OWNER TO postgres;

--
-- Name: adresse_id_adresse_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.adresse_id_adresse_seq OWNED BY public.adresse.id_adresse;


--
-- Name: avis; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.avis (
    id_avis integer NOT NULL,
    id_client integer NOT NULL,
    id_variante integer NOT NULL,
    note integer NOT NULL,
    titre character varying(255),
    commentaire text NOT NULL,
    date_avis timestamp without time zone DEFAULT now(),
    modere character varying(20) DEFAULT 'en_attente'::character varying,
    CONSTRAINT avis_modere_check CHECK (((modere)::text = ANY ((ARRAY['en_attente'::character varying, 'approuve'::character varying, 'refuse'::character varying])::text[]))),
    CONSTRAINT avis_note_check CHECK (((note >= 1) AND (note <= 5)))
);


ALTER TABLE public.avis OWNER TO postgres;

--
-- Name: avis_id_avis_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.avis_id_avis_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.avis_id_avis_seq OWNER TO postgres;

--
-- Name: avis_id_avis_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.avis_id_avis_seq OWNED BY public.avis.id_avis;


--
-- Name: categorie; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categorie (
    id_categorie integer NOT NULL,
    nom_categorie character varying(100) NOT NULL,
    id_categorie_parent integer,
    image_categorie character varying(255)
);


ALTER TABLE public.categorie OWNER TO postgres;

--
-- Name: categorie_id_categorie_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categorie_id_categorie_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categorie_id_categorie_seq OWNER TO postgres;

--
-- Name: categorie_id_categorie_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categorie_id_categorie_seq OWNED BY public.categorie.id_categorie;


--
-- Name: client; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.client (
    id_client integer NOT NULL,
    nom_client character varying(100) NOT NULL,
    prenom_client character varying(100) NOT NULL,
    email_client character varying(255) NOT NULL,
    mot_de_passe character varying(255) NOT NULL,
    telephone character varying(20),
    date_inscription timestamp without time zone DEFAULT now()
);


ALTER TABLE public.client OWNER TO postgres;

--
-- Name: client_id_client_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.client_id_client_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.client_id_client_seq OWNER TO postgres;

--
-- Name: client_id_client_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.client_id_client_seq OWNED BY public.client.id_client;


--
-- Name: code_promo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.code_promo (
    id_code_promo integer NOT NULL,
    code character varying(50) NOT NULL,
    taux_reduction numeric(5,2) NOT NULL,
    date_debut timestamp without time zone NOT NULL,
    date_fin timestamp without time zone NOT NULL,
    usage_max integer,
    usage_actuel integer DEFAULT 0,
    actif boolean DEFAULT true,
    CONSTRAINT code_promo_taux_reduction_check CHECK (((taux_reduction >= (0)::numeric) AND (taux_reduction <= (100)::numeric)))
);


ALTER TABLE public.code_promo OWNER TO postgres;

--
-- Name: code_promo_id_code_promo_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.code_promo_id_code_promo_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.code_promo_id_code_promo_seq OWNER TO postgres;

--
-- Name: code_promo_id_code_promo_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.code_promo_id_code_promo_seq OWNED BY public.code_promo.id_code_promo;


--
-- Name: commande; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.commande (
    id_commande integer NOT NULL,
    id_client integer NOT NULL,
    id_adresse_livraison integer NOT NULL,
    id_adresse_facturation integer NOT NULL,
    id_transporteur integer NOT NULL,
    id_code_promo integer,
    date_commande timestamp without time zone DEFAULT now(),
    total_commande numeric(10,2) NOT NULL,
    methode_paiement character varying(10) NOT NULL,
    statut_paiement boolean DEFAULT false,
    statut_commande character varying(20) DEFAULT 'en_attente'::character varying,
    numero_suivi character varying(100),
    CONSTRAINT commande_methode_paiement_check CHECK (((methode_paiement)::text = ANY ((ARRAY['carte'::character varying, 'paypal'::character varying])::text[]))),
    CONSTRAINT commande_statut_commande_check CHECK (((statut_commande)::text = ANY ((ARRAY['en_attente'::character varying, 'confirmee'::character varying, 'en_preparation'::character varying, 'expediee'::character varying, 'livree'::character varying, 'annulee'::character varying])::text[])))
);


ALTER TABLE public.commande OWNER TO postgres;

--
-- Name: commande_id_commande_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.commande_id_commande_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.commande_id_commande_seq OWNER TO postgres;

--
-- Name: commande_id_commande_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.commande_id_commande_seq OWNED BY public.commande.id_commande;


--
-- Name: commande_variante; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.commande_variante (
    id_commande integer NOT NULL,
    id_variante integer NOT NULL,
    quantite integer NOT NULL,
    prix_unitaire numeric(10,2) NOT NULL,
    CONSTRAINT commande_variante_quantite_check CHECK ((quantite >= 1))
);


ALTER TABLE public.commande_variante OWNER TO postgres;

--
-- Name: conversation; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.conversation (
    id_conversation integer NOT NULL,
    id_client integer NOT NULL,
    id_support integer,
    sujet character varying(255) NOT NULL,
    statut character varying(20) DEFAULT 'ouverte'::character varying,
    date_creation timestamp without time zone DEFAULT now(),
    date_fermeture timestamp without time zone,
    CONSTRAINT conversation_statut_check CHECK (((statut)::text = ANY ((ARRAY['ouverte'::character varying, 'en_cours'::character varying, 'fermee'::character varying])::text[])))
);


ALTER TABLE public.conversation OWNER TO postgres;

--
-- Name: conversation_id_conversation_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.conversation_id_conversation_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.conversation_id_conversation_seq OWNER TO postgres;

--
-- Name: conversation_id_conversation_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.conversation_id_conversation_seq OWNED BY public.conversation.id_conversation;


--
-- Name: image_produit; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.image_produit (
    id_image integer NOT NULL,
    id_variante integer NOT NULL,
    url_image character varying(500) NOT NULL,
    ordre integer DEFAULT 1,
    alt_text character varying(255)
);


ALTER TABLE public.image_produit OWNER TO postgres;

--
-- Name: image_produit_id_image_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.image_produit_id_image_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.image_produit_id_image_seq OWNER TO postgres;

--
-- Name: image_produit_id_image_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.image_produit_id_image_seq OWNED BY public.image_produit.id_image;


--
-- Name: liste_envie; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.liste_envie (
    id_liste_envie integer NOT NULL,
    id_session character varying(255) NOT NULL,
    id_client integer,
    id_variante integer,
    date_ajout timestamp without time zone DEFAULT now()
);


ALTER TABLE public.liste_envie OWNER TO postgres;

--
-- Name: liste_envie_id_liste_envie_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.liste_envie_id_liste_envie_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.liste_envie_id_liste_envie_seq OWNER TO postgres;

--
-- Name: liste_envie_id_liste_envie_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.liste_envie_id_liste_envie_seq OWNED BY public.liste_envie.id_liste_envie;


--
-- Name: message_chat; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.message_chat (
    id_message integer NOT NULL,
    id_conversation integer NOT NULL,
    expediteur_type character varying(10) NOT NULL,
    contenu text NOT NULL,
    date_envoi timestamp without time zone DEFAULT now(),
    lu boolean DEFAULT false,
    CONSTRAINT message_chat_expediteur_type_check CHECK (((expediteur_type)::text = ANY ((ARRAY['client'::character varying, 'support'::character varying])::text[])))
);


ALTER TABLE public.message_chat OWNER TO postgres;

--
-- Name: message_chat_id_message_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.message_chat_id_message_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.message_chat_id_message_seq OWNER TO postgres;

--
-- Name: message_chat_id_message_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.message_chat_id_message_seq OWNED BY public.message_chat.id_message;


--
-- Name: message_contact; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.message_contact (
    id_message integer NOT NULL,
    id_client integer,
    nom_contact character varying(200) NOT NULL,
    email_contact character varying(255) NOT NULL,
    sujet character varying(255) NOT NULL,
    contenu text NOT NULL,
    date_envoi timestamp without time zone DEFAULT now(),
    traite boolean DEFAULT false
);


ALTER TABLE public.message_contact OWNER TO postgres;

--
-- Name: message_contact_id_message_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.message_contact_id_message_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.message_contact_id_message_seq OWNER TO postgres;

--
-- Name: message_contact_id_message_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.message_contact_id_message_seq OWNED BY public.message_contact.id_message;


--
-- Name: panier; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.panier (
    id_panier integer NOT NULL,
    id_session character varying(255) NOT NULL,
    id_client integer,
    date_creation timestamp without time zone DEFAULT now(),
    date_modification timestamp without time zone DEFAULT now()
);


ALTER TABLE public.panier OWNER TO postgres;

--
-- Name: panier_id_panier_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.panier_id_panier_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.panier_id_panier_seq OWNER TO postgres;

--
-- Name: panier_id_panier_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.panier_id_panier_seq OWNED BY public.panier.id_panier;


--
-- Name: panier_variante; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.panier_variante (
    id_panier integer NOT NULL,
    id_variante integer NOT NULL,
    quantite integer NOT NULL,
    CONSTRAINT panier_variante_quantite_check CHECK ((quantite >= 1))
);


ALTER TABLE public.panier_variante OWNER TO postgres;

--
-- Name: produit; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.produit (
    id_produit integer NOT NULL,
    id_categorie integer,
    nom_produit character varying(255) NOT NULL,
    description_courte text,
    fiche_technique_url character varying(500),
    actif boolean DEFAULT true
);


ALTER TABLE public.produit OWNER TO postgres;

--
-- Name: produit_id_produit_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.produit_id_produit_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.produit_id_produit_seq OWNER TO postgres;

--
-- Name: produit_id_produit_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.produit_id_produit_seq OWNED BY public.produit.id_produit;


--
-- Name: promotion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.promotion (
    id_promotion integer NOT NULL,
    nom_promotion character varying(255) NOT NULL,
    taux_reduction numeric(5,2) NOT NULL,
    date_debut timestamp without time zone NOT NULL,
    date_fin timestamp without time zone NOT NULL,
    actif boolean DEFAULT true,
    CONSTRAINT promotion_taux_reduction_check CHECK (((taux_reduction >= (0)::numeric) AND (taux_reduction <= (100)::numeric)))
);


ALTER TABLE public.promotion OWNER TO postgres;

--
-- Name: promotion_id_promotion_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.promotion_id_promotion_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.promotion_id_promotion_seq OWNER TO postgres;

--
-- Name: promotion_id_promotion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.promotion_id_promotion_seq OWNED BY public.promotion.id_promotion;


--
-- Name: promotion_variante; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.promotion_variante (
    id_promotion integer NOT NULL,
    id_variante integer NOT NULL
);


ALTER TABLE public.promotion_variante OWNER TO postgres;

--
-- Name: root; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.root (
    id_root integer NOT NULL,
    login_root character varying(50) NOT NULL,
    mot_de_passe character varying(255) NOT NULL
);


ALTER TABLE public.root OWNER TO postgres;

--
-- Name: root_id_root_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.root_id_root_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.root_id_root_seq OWNER TO postgres;

--
-- Name: root_id_root_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.root_id_root_seq OWNED BY public.root.id_root;


--
-- Name: support; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.support (
    id_support integer NOT NULL,
    nom_support character varying(100) NOT NULL,
    prenom_support character varying(100) NOT NULL,
    email_support character varying(255) NOT NULL,
    mot_de_passe character varying(255) NOT NULL
);


ALTER TABLE public.support OWNER TO postgres;

--
-- Name: support_id_support_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.support_id_support_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.support_id_support_seq OWNER TO postgres;

--
-- Name: support_id_support_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.support_id_support_seq OWNED BY public.support.id_support;


--
-- Name: transporteur; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.transporteur (
    id_transporteur integer NOT NULL,
    nom_transporteur character varying(100) NOT NULL,
    delai_estime character varying(100),
    frais_livraison numeric(10,2) NOT NULL,
    actif boolean DEFAULT true
);


ALTER TABLE public.transporteur OWNER TO postgres;

--
-- Name: transporteur_id_transporteur_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.transporteur_id_transporteur_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.transporteur_id_transporteur_seq OWNER TO postgres;

--
-- Name: transporteur_id_transporteur_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.transporteur_id_transporteur_seq OWNED BY public.transporteur.id_transporteur;


--
-- Name: variante_produit; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.variante_produit (
    id_variante integer NOT NULL,
    id_produit integer NOT NULL,
    nom_variante character varying(255) NOT NULL,
    sku character varying(100) NOT NULL,
    prix numeric(10,2) NOT NULL,
    stock integer DEFAULT 0,
    couleur character varying(50),
    capacite character varying(50),
    CONSTRAINT variante_produit_stock_check CHECK ((stock >= 0))
);


ALTER TABLE public.variante_produit OWNER TO postgres;

--
-- Name: variante_produit_id_variante_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.variante_produit_id_variante_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.variante_produit_id_variante_seq OWNER TO postgres;

--
-- Name: variante_produit_id_variante_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.variante_produit_id_variante_seq OWNED BY public.variante_produit.id_variante;


--
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

--
-- Name: admin id_admin; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin ALTER COLUMN id_admin SET DEFAULT nextval('public.admin_id_admin_seq'::regclass);


--
-- Name: adresse id_adresse; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.adresse ALTER COLUMN id_adresse SET DEFAULT nextval('public.adresse_id_adresse_seq'::regclass);


--
-- Name: avis id_avis; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.avis ALTER COLUMN id_avis SET DEFAULT nextval('public.avis_id_avis_seq'::regclass);


--
-- Name: categorie id_categorie; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorie ALTER COLUMN id_categorie SET DEFAULT nextval('public.categorie_id_categorie_seq'::regclass);


--
-- Name: client id_client; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.client ALTER COLUMN id_client SET DEFAULT nextval('public.client_id_client_seq'::regclass);


--
-- Name: code_promo id_code_promo; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.code_promo ALTER COLUMN id_code_promo SET DEFAULT nextval('public.code_promo_id_code_promo_seq'::regclass);


--
-- Name: commande id_commande; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.commande ALTER COLUMN id_commande SET DEFAULT nextval('public.commande_id_commande_seq'::regclass);


--
-- Name: conversation id_conversation; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversation ALTER COLUMN id_conversation SET DEFAULT nextval('public.conversation_id_conversation_seq'::regclass);


--
-- Name: image_produit id_image; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.image_produit ALTER COLUMN id_image SET DEFAULT nextval('public.image_produit_id_image_seq'::regclass);


--
-- Name: liste_envie id_liste_envie; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.liste_envie ALTER COLUMN id_liste_envie SET DEFAULT nextval('public.liste_envie_id_liste_envie_seq'::regclass);


--
-- Name: message_chat id_message; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.message_chat ALTER COLUMN id_message SET DEFAULT nextval('public.message_chat_id_message_seq'::regclass);


--
-- Name: message_contact id_message; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.message_contact ALTER COLUMN id_message SET DEFAULT nextval('public.message_contact_id_message_seq'::regclass);


--
-- Name: panier id_panier; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.panier ALTER COLUMN id_panier SET DEFAULT nextval('public.panier_id_panier_seq'::regclass);


--
-- Name: produit id_produit; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produit ALTER COLUMN id_produit SET DEFAULT nextval('public.produit_id_produit_seq'::regclass);


--
-- Name: promotion id_promotion; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.promotion ALTER COLUMN id_promotion SET DEFAULT nextval('public.promotion_id_promotion_seq'::regclass);


--
-- Name: root id_root; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.root ALTER COLUMN id_root SET DEFAULT nextval('public.root_id_root_seq'::regclass);


--
-- Name: support id_support; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.support ALTER COLUMN id_support SET DEFAULT nextval('public.support_id_support_seq'::regclass);


--
-- Name: transporteur id_transporteur; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transporteur ALTER COLUMN id_transporteur SET DEFAULT nextval('public.transporteur_id_transporteur_seq'::regclass);


--
-- Name: variante_produit id_variante; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.variante_produit ALTER COLUMN id_variante SET DEFAULT nextval('public.variante_produit_id_variante_seq'::regclass);


--
-- Data for Name: admin; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.admin (id_admin, nom_admin, prenom_admin, email_admin, mot_de_passe) FROM stdin;
1	Stone	Admin	admin@stoneshop.be	$argon2id$v=19$m=65536,t=4,p=1$R1luR3N1WDNjMnloZnpuUg$mL73VrO1DEGIsDut14sDltUWKA+ySNH9ERdQ47J3jec
3	TestNom	TestAdmin	admintest@stoneshop.be	$argon2id$v=19$m=65536,t=4,p=1$MjVnYVBVaGg2cHc2TW5oWA$EaUEZnCDWsAuLV5roJFwnVz/Bl2X0UM/X2O4fQUxMDM
5	PC2Test	Auto	pc2admin_1777984431@stoneshop.be	$argon2id$v=19$m=65536,t=4,p=1$Vk1oS1QwaUt3c2svWXIwdw$pbPrJ7VH0XXzu8uX5I4IUsCRBSAmmlZyQCGzTJCeruw
6	PC5	Adm	pc5adm_1777990392@stone.dev	$argon2id$v=19$m=65536,t=4,p=1$TERrYUp0YjdrbjdUTHk2Qw$+qSxbsj3ndCrsKC7sjqI0fPzI+J5oxspzqqTSYgpYLs
\.


--
-- Data for Name: adresse; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.adresse (id_adresse, id_client, type_adresse, nom_destinataire, rue, numero, boite, code_postal, ville, pays) FROM stdin;
1	1	livraison	Art De	Rue de Test	42	\N	1000	Bruxelles	Belgique
2	2	livraison	QSESQSQD	SDQDSQDQS	SQDDSQQDS	\N	DSQDSQQDS	DSQDSQ	Belgique
3	6	livraison	PC1 Test	Rue Test	12	\N	1000	Bruxelles	Belgique
\.


--
-- Data for Name: avis; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.avis (id_avis, id_client, id_variante, note, titre, commentaire, date_avis, modere) FROM stdin;
1	1	19	5	Excellent rapport qualite/prix	Je l'utilise tous les jours pour bosser, fluide et silencieux. Rien a redire.	2026-05-04 14:20:37.182713	approuve
2	2	19	4	Tres bon laptop	Bon ecran, bonne autonomie. Le clavier aurait pu etre un peu mieux retroeclaire.	2026-05-04 14:20:37.182713	approuve
3	1	19	5	Parfait pour le teletravail	Leger, rapide a demarrer, finitions soignees. Je recommande.	2026-05-04 14:20:37.182713	approuve
\.


--
-- Data for Name: categorie; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categorie (id_categorie, nom_categorie, id_categorie_parent, image_categorie) FROM stdin;
1	Téléphones	\N	categories/telephones.jpg
2	Télévisions	\N	categories/televisions.jpg
3	Ordinateurs	\N	categories/ordinateurs.jpg
4	Accessoires	\N	categories/accessoires.jpg
5	Domotique	\N	categories/domotique.jpg
7	Téléphones reconditionnés	\N	\N
\.


--
-- Data for Name: client; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.client (id_client, nom_client, prenom_client, email_client, mot_de_passe, telephone, date_inscription) FROM stdin;
2	TestNomCli	TestCli	clitest@stoneshop.be	$argon2id$v=19$m=65536,t=4,p=1$RTVnLzNjNHZsakhYWTFydQ$NERMIanQXaIxGXEYKcKfjG6pt9Rrw4KW+2iBt8B6vWU	0488112233	2026-05-04 13:50:21.829794
1	De	Art	art@gmail.com	$argon2id$v=19$m=65536,t=4,p=1$cjdTOUY4WTcvTnlZSk1yQw$wUWIYuqXLjYNbHm+bo9OohIKdcx5QF+PtOZkDYd2UH4	0476123456	2026-04-30 16:20:26.035644
5	Test	PC2	pc2b_1777984342@stone.dev	$argon2id$v=19$m=65536,t=4,p=1$VnFSc3h4bXZhblZadk1lVw$tJXDtxwG4za0Ae16y61DICCwGY8hLHSwPe+X5R1vHio	0123456789	2026-05-05 14:32:23.249557
6	PC5	Cli	pc5cli_1777990335@stone.dev	$argon2id$v=19$m=65536,t=4,p=1$QktwTXNwcDRkM1FkcHdVSw$9d5CKy9JOsFQ4ReW/naSyahkbsSkH9qyK0EEiaR2A4o	0102030405	2026-05-05 16:12:16.393472
\.


--
-- Data for Name: code_promo; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.code_promo (id_code_promo, code, taux_reduction, date_debut, date_fin, usage_max, usage_actuel, actif) FROM stdin;
1	TEST20	20.00	2026-01-01 00:00:00	2026-12-31 00:00:00	50	0	t
\.


--
-- Data for Name: commande; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.commande (id_commande, id_client, id_adresse_livraison, id_adresse_facturation, id_transporteur, id_code_promo, date_commande, total_commande, methode_paiement, statut_paiement, statut_commande, numero_suivi) FROM stdin;
1	1	1	1	1	\N	2026-05-04 13:23:06.318085	259.96	carte	f	confirmee	\N
2	2	2	2	1	\N	2026-05-04 14:15:33.365064	789.99	carte	f	en_attente	\N
3	2	2	2	1	\N	2026-05-05 16:20:20.366092	199.98	carte	f	en_attente	\N
6	6	3	3	1	\N	2026-05-05 16:22:41.136547	999.97	carte	f	en_attente	\N
\.


--
-- Data for Name: commande_variante; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.commande_variante (id_commande, id_variante, quantite, prix_unitaire) FROM stdin;
1	30	3	59.99
1	1	1	79.99
2	19	1	789.99
3	2	1	99.99
3	11	1	99.99
6	2	2	99.99
6	11	1	799.99
\.


--
-- Data for Name: conversation; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.conversation (id_conversation, id_client, id_support, sujet, statut, date_creation, date_fermeture) FROM stdin;
1	2	\N	Bonjour	fermee	2026-05-04 13:55:45.999165	2026-05-04 14:10:00.733079
2	2	\N	Test conversation 2	ouverte	2026-05-04 14:13:17.859683	\N
\.


--
-- Data for Name: image_produit; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.image_produit (id_image, id_variante, url_image, ordre, alt_text) FROM stdin;
13	30	assets/images/produits/img_69f8a342770120.58275749.jpg	1	\N
14	31	assets/images/produits/img_69f8a34409b663.00971312.jpg	1	\N
15	22	assets/images/produits/img_6a08512c396704.66351283.jpg	1	\N
16	17	assets/images/produits/img_6a085155c831a0.84272891.jpg	1	\N
17	18	assets/images/produits/img_6a08515eb77302.56155575.jpg	1	\N
18	33	assets/images/produits/img_6a0851d77c3180.12609354.jpg	1	\N
19	32	assets/images/produits/img_6a0851dd531432.67342316.png	1	\N
20	5	assets/images/produits/img_6a08520410e0d1.54145380.jpg	1	\N
21	20	assets/images/produits/img_6a0852145d8480.25009153.jpg	1	\N
22	21	assets/images/produits/img_6a0852182e8135.93485566.jpg	1	\N
23	8	assets/images/produits/img_6a085254ce8211.64566199.jpg	1	\N
24	9	assets/images/produits/img_6a0852bd542e89.83680428.jpg	1	\N
25	25	assets/images/produits/img_6a085358a01e48.49636042.jpg	1	\N
26	26	assets/images/produits/img_6a08535fb9b314.02599399.png	1	\N
27	13	assets/images/produits/img_6a085391bd6959.95520524.jpg	1	\N
28	12	assets/images/produits/img_6a0853974c9fa5.19763971.jpg	1	\N
29	23	assets/images/produits/img_6a0853a8b40e59.73979496.jpg	1	\N
30	24	assets/images/produits/img_6a0853aea1b860.15249224.jpg	1	\N
31	14	assets/images/produits/img_6a0853c97b5d84.76770996.jpg	1	\N
32	29	assets/images/produits/img_6a0853d6583d06.37394203.jpg	1	\N
33	15	assets/images/produits/img_6a0853e7638cc8.16348203.jpg	1	\N
34	16	assets/images/produits/img_6a0853e8740e74.90824873.jpg	1	\N
35	1	assets/images/produits/img_6a0853fe7f1b85.53104742.jpg	1	\N
36	2	assets/images/produits/img_6a085401d5dac8.01312639.jpg	1	\N
37	3	assets/images/produits/img_6a085414110a18.32352814.jpg	1	\N
40	4	assets/images/produits/img_6a08546b9d71d3.57596618.jpg	1	\N
41	11	assets/images/produits/img_6a085476021615.10584482.jpg	1	\N
42	28	assets/images/produits/img_6a085485354b58.09612507.jpg	1	\N
43	27	assets/images/produits/img_6a08548a492990.22460100.jpg	1	\N
44	7	assets/images/produits/img_6a0856b59cc310.73500420.jpg	1	\N
45	6	assets/images/produits/img_6a0856bd28cc20.94383614.jpg	1	\N
46	10	assets/images/produits/img_6a0856ecb25825.51391628.png	1	\N
47	19	assets/images/produits/img_6a085a95c19d39.77926594.jpg	1	\N
\.


--
-- Data for Name: liste_envie; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.liste_envie (id_liste_envie, id_session, id_client, id_variante, date_ajout) FROM stdin;
1	giq3rloo05r9kvgqgmmfh45ptu	2	30	2026-05-04 15:23:47.035814
\.


--
-- Data for Name: message_chat; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.message_chat (id_message, id_conversation, expediteur_type, contenu, date_envoi, lu) FROM stdin;
1	1	client	Hello	2026-05-04 13:55:50.29848	f
2	1	client	Comment allez vous ?	2026-05-04 13:55:54.897804	f
3	1	support	Je vais bien et vous ?	2026-05-04 14:07:12.553256	f
4	1	client	ddd	2026-05-04 14:13:00.24692	f
5	2	client	Bonjour	2026-05-04 14:13:30.126058	f
\.


--
-- Data for Name: message_contact; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.message_contact (id_message, id_client, nom_contact, email_contact, sujet, contenu, date_envoi, traite) FROM stdin;
2	\N	sqdsqdsqdsqdqs	dsqdsqdq@gmail.com	sdqs	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam dui metus, suscipit ac leo a, feugiat tempor tortor. Etiam non lacus vel massa sodales semper non in dui. Sed ligula sem, malesuada ac bibendum at, tincidunt sed ipsum. Fusce porttitor ligula in justo tempor, et sollicitudin orci malesuada. Phasellus vitae tincidunt lacus. Curabitur vitae tincidunt augue. Aenean eu porttitor sapien, egestas mollis orci. Morbi sed feugiat justo, sit amet luctus sem. Quisque ac tempor libero, sed imperdiet magna.\r\n\r\nPraesent gravida mi nulla, tempor sodales ex posuere vitae. Donec tempor, eros sed ullamcorper molestie, nunc mauris pulvinar elit, accumsan interdum justo tortor a lectus. Nulla maximus nisl leo, ut congue orci dignissim fringilla. Donec fringilla dolor ut diam pellentesque, quis imperdiet risus mattis. Nulla facilisi. Fusce tristique magna mattis nunc accumsan finibus. Suspendisse vitae accumsan dolor, non porttitor augue. In ornare ultrices lacinia. Duis cursus, ex sit amet rutrum feugiat, sem nisl ornare velit, vitae convallis tortor diam in metus.\r\n\r\nMorbi a ligula metus. Maecenas ipsum lectus, pellentesque ut finibus eu, accumsan vel libero. Nullam malesuada diam ac efficitur sagittis. Cras consectetur ornare accumsan. Duis id diam nec tellus molestie interdum. Aenean congue, odio ac accumsan scelerisque, eros erat ullamcorper arcu, vitae consequat dui tortor porttitor magna. Cras vestibulum iaculis felis, ut ullamcorper ipsum condimentum eu. Donec imperdiet purus eget sem maximus lacinia nec et sem. Integer enim sem, fringilla ut lorem nec, venenatis finibus est. Nulla sagittis varius sapien. Curabitur eu congue turpis.\r\n\r\nPraesent vitae aliquam nulla, sed ultricies diam. Vestibulum non aliquet augue, in egestas ligula. Etiam nec malesuada justo. Ut varius quam eu diam rhoncus blandit. Ut ut erat lacus. Curabitur non accumsan sem, id rutrum nunc. Maecenas cursus, eros ac ornare varius, felis dolor tincidunt nulla, at vehicula mauris tellus et tortor. Morbi ullamcorper finibus fermentum. Proin a urna vitae risus pharetra molestie.\r\n\r\nAenean dapibus pretium enim nec mattis. Nullam ut orci tristique, luctus risus a, faucibus mi. Mauris at felis massa. Sed consectetur leo ligula, at dignissim nibh fringilla sed. Fusce consequat non ligula ullamcorper interdum. Suspendisse iaculis elit nec orci consequat egestas. Fusce varius egestas diam, et finibus libero tristique luctus. Donec fringilla ut orci aliquet cursus. Mauris ut turpis felis. Aenean molestie, nulla vel efficitur maximus, ipsum purus euismod odio, nec malesuada mi velit ac ex. Donec vel erat ac mauris maximus consectetur non quis magna.	2026-05-01 11:08:59.071965	t
1	\N	Arthur	art@gmail.com	test	test	2026-04-30 16:19:13.114364	t
3	1	Arthur PC4	art@gmail.com	Test CSRF PC4	Verification CSRF sur contact form PC-4.	2026-05-05 16:03:01.560212	f
\.


--
-- Data for Name: panier; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.panier (id_panier, id_session, id_client, date_creation, date_modification) FROM stdin;
1	84dhhv46unk5vpqj586ive3ubl	\N	2026-04-30 12:40:02.395498	2026-04-30 12:40:02.407406
2	d007evt8itauq4i4ethunpo0eg	\N	2026-04-30 12:49:01.487576	2026-04-30 12:49:01.490314
3	uvkt39ssetomg63rh2cqlc8qlq	\N	2026-04-30 12:51:30.334573	2026-04-30 12:51:30.336227
4	vnc84v91qo1b74goith9qadqn1	\N	2026-04-30 12:51:56.332033	2026-04-30 12:51:56.333388
5	p2q4u4jd144qte0bqj7cg57j56	\N	2026-04-30 12:53:58.323112	2026-04-30 15:00:37.902524
7	l8hs29979cpa3joespsfkq8k71	\N	2026-04-30 15:04:32.59982	2026-04-30 15:04:32.602536
8	torn5ce87b5c1jhcvurgmstadc	\N	2026-04-30 16:10:06.941594	2026-04-30 16:10:06.944749
9	51fj3oljqahjrj9aku49mcdvf8	1	2026-04-30 16:14:09.753049	2026-04-30 16:14:09.756253
10	kg0jbcdc1tdi8sfi96frmj5oq7	1	2026-05-04 11:25:41.869922	2026-05-04 13:20:21.373032
12	giq3rloo05r9kvgqgmmfh45ptu	2	2026-05-04 14:15:00.721428	2026-05-04 14:15:00.723825
13	96ndob5u8bk44pr9iekc4cc84u	\N	2026-05-05 16:03:29.630961	2026-05-05 16:03:29.636587
14	li4spdmgnbi9arntppqdvugl6b	6	2026-05-05 16:22:22.665869	2026-05-05 16:22:22.959084
16	ff75qp98aa0b3tpn6905fqtj78	6	2026-05-05 16:23:11.572012	2026-05-05 16:23:11.574522
17	k1d70eq1c32ci2641eq4qbhvkn	6	2026-05-05 16:34:05.672447	2026-05-05 16:34:14.877757
\.


--
-- Data for Name: panier_variante; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.panier_variante (id_panier, id_variante, quantite) FROM stdin;
1	2	1
2	1	1
3	1	1
4	1	1
5	1	2
7	1	1
8	1	1
13	2	2
16	11	99999
\.


--
-- Data for Name: produit; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.produit (id_produit, id_categorie, nom_produit, description_courte, fiche_technique_url, actif) FROM stdin;
8	2	LG OLED C4 65"	TV OLED evo 4K 120Hz avec Dolby Vision et webOS 24	\N	t
15	4	Logitech MX Master 3S	Souris sans fil ergonomique avec capteur 8000 DPI	\N	t
17	4	Sony WH-1000XM5	Casque Bluetooth ANC premium avec 30h d'autonomie	\N	t
7	2	Samsung Neo QLED 55"	Téléviseur 4K Neo QLED avec processeur Neural Quantum	\N	t
9	2	Philips Ambilight 50"	TV LED 4K UHD avec technologie Ambilight 3 cÃ´tés	\N	t
16	4	Keychron K2 Pro	Clavier mécanique compact 75% Bluetooth / USB-C	\N	t
18	5	Philips Hue Starter Kit	Pack 3 ampoules LED connectées E27 + pont Hue	\N	t
20	5	Google Nest Thermostat	Thermostat connecté avec apprentissage automatique et contrÃ´le Ã  distance	\N	t
19	5	Amazon Echo Dot 5	Enceinte connectée Alexa avec son amélioré et capteur de température	\N	t
5	7	iPhone 12 Reconditionné	iPhone 12 remis Ã  neuf — Grade A	\N	t
6	7	Samsung Galaxy S21 Reconditionné	Galaxy S21 remis Ã  neuf — Grade B	\N	t
4	1	iPhone 15	Smartphone Apple avec puce A16 Bionic et Dynamic Island	\N	t
2	1	Samsung Galaxy A55	Smartphone milieu de gamme avec écran AMOLED 6.6"	\N	t
3	1	Google Pixel 8a	Smartphone Google avec IA intégrée et appareil photo 64 Mpx	\N	t
1	3	Raspberry Pi 5	Le nano-ordinateur le plus puissant de la gamme	\N	t
10	3	MacBook Air M3	Laptop ultra-fin Apple avec puce M3 et 18h d'autonomie	\N	t
13	3	Intel NUC 13 Pro	Mini PC compact avec Intel Core i5-1340P et Wi-Fi 6E	\N	t
14	3	Beelink SER6 Pro	Mini PC AMD Ryzen 9 6900HX avec 32 Go RAM et Wi-Fi 6	\N	t
11	3	Dell XPS 15	Laptop premium 15" avec écran OLED et Intel Core Ultra 7	\N	t
12	3	ASUS VivoBook 16	Laptop polyvalent 16" avec AMD Ryzen 7 et écran OLED	\N	t
\.


--
-- Data for Name: promotion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.promotion (id_promotion, nom_promotion, taux_reduction, date_debut, date_fin, actif) FROM stdin;
1	Soldes Pi 5	15.00	2026-01-01 00:00:00	2026-06-30 00:00:00	t
\.


--
-- Data for Name: promotion_variante; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.promotion_variante (id_promotion, id_variante) FROM stdin;
1	1
\.


--
-- Data for Name: root; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.root (id_root, login_root, mot_de_passe) FROM stdin;
1	root	$argon2id$v=19$m=65536,t=4,p=1$YjR3bm1CVVZtYlNja0NXSg$VoXmDUEspRPgO7gGuMVt8FsiK9wjFGVfbmfKJPnut1Y
2	pc5root_1777990478	$argon2id$v=19$m=65536,t=4,p=1$dUs4S2Y0ZWFLcmE3MEZOUQ$XBVshnoOEjbpd/9h48parQm+i5RPADZsh3mkj5iuXQE
\.


--
-- Data for Name: support; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.support (id_support, nom_support, prenom_support, email_support, mot_de_passe) FROM stdin;
1	Dupont	Julie	support@stoneshop.be	$argon2id$v=19$m=65536,t=4,p=1$aS90c0UuMS9weWwzeE1ROA$1jmvXOaxmcmV6fN4TrMd31rw7HVi/mhviy7PKMazTfY
2	Test	Support2	support2@stoneshop.be	$argon2id$v=19$m=65536,t=4,p=1$d3JzVUtkTXljRHBrSE96WA$znRcFTQD5+XJWwaJuGhuNkgxu4oF1XpvslPZdKtTDpk
3	TestNomSup	TestSup	suptest@stoneshop.be	$argon2id$v=19$m=65536,t=4,p=1$NE1Pc3JlRjZpWjA3Rk1MQg$WhmR+msplIl0P4GK6VQxWpj1+DulngpZIEoUQQkij58
5	PC5	Sup	pc5sup_1777990392@stone.dev	$argon2id$v=19$m=65536,t=4,p=1$TERrYUp0YjdrbjdUTHk2Qw$+qSxbsj3ndCrsKC7sjqI0fPzI+J5oxspzqqTSYgpYLs
\.


--
-- Data for Name: transporteur; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.transporteur (id_transporteur, nom_transporteur, delai_estime, frais_livraison, actif) FROM stdin;
1	bpost	2-3 jours ouvrables	4.95	t
2	Colis Privé	1-2 jours ouvrables	6.50	t
3	TestExpress	24h	9.95	t
\.


--
-- Data for Name: variante_produit; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.variante_produit (id_variante, id_produit, nom_variante, sku, prix, stock, couleur, capacite) FROM stdin;
12	8	LG OLED C4 65" 4K	LG-OLEDC4-65-4K	1299.99	6	\N	65 pouces
13	8	LG OLED C4 55" 4K	LG-OLEDC4-55-4K	999.99	8	\N	55 pouces
14	9	Philips Ambilight 50" 4K	PHI-AMB-50-4K	549.99	14	\N	50 pouces
29	18	Hue Starter Kit White & Color	PHI-HUE-SK-WC	139.99	25	\N	White & Color Ambiance
3	2	Galaxy A55 — 128 Go Noir	SAM-A55-128-BLK	329.99	40	\N	128 Go
4	2	Galaxy A55 — 256 Go Bleu	SAM-A55-256-BLU	379.99	25	\N	256 Go
5	3	Pixel 8a — 128 Go Obsidian	GOO-P8A-128-OBS	549.99	20	\N	128 Go
6	4	iPhone 15 — 128 Go Noir	APL-IP15-128-BLK	899.99	15	\N	128 Go
7	4	iPhone 15 — 256 Go Rose	APL-IP15-256-PNK	999.99	10	\N	256 Go
8	5	iPhone 12 — 64 Go Noir Grade A	REC-IP12-64-BLK	349.99	12	\N	64 Go
9	5	iPhone 12 — 128 Go Blanc Grade A	REC-IP12-128-WHT	399.99	8	\N	128 Go
10	6	Galaxy S21 — 128 Go Gris Grade B	REC-S21-128-GRY	279.99	18	\N	128 Go
15	10	MacBook Air M3 — 8 Go / 256 Go	APL-MBA-M3-8-256	1299.99	10	\N	8 Go RAM / 256 Go SSD
16	10	MacBook Air M3 — 16 Go / 512 Go	APL-MBA-M3-16-512	1699.99	6	\N	16 Go RAM / 512 Go SSD
18	11	XPS 15 — 32 Go / 1 To	DEL-XPS15-32-1TB	1899.99	4	\N	32 Go RAM / 1 To SSD
20	13	NUC 13 Pro — Barebone	INT-NUC13-BARE	349.99	15	\N	Sans RAM ni SSD
21	13	NUC 13 Pro — 16 Go / 512 Go	INT-NUC13-16-512	649.99	8	\N	16 Go RAM / 512 Go SSD
22	14	SER6 Pro — 32 Go / 500 Go	BEE-SER6-32-500	459.99	12	\N	32 Go RAM / 500 Go SSD
23	15	MX Master 3S — Noir	LOG-MXM3S-BLK	99.99	35	\N	Noir
24	15	MX Master 3S — Blanc	LOG-MXM3S-WHT	99.99	30	\N	Blanc
25	16	K2 Pro — Red Switch	KEY-K2PRO-RED	109.99	20	\N	Switch Red
26	16	K2 Pro — Brown Switch	KEY-K2PRO-BRN	109.99	18	\N	Switch Brown
27	17	WH-1000XM5 — Noir	SON-XM5-BLK	279.99	22	\N	Noir
28	17	WH-1000XM5 — Blanc	SON-XM5-WHT	279.99	18	\N	Blanc
31	19	Echo Dot 5 — Blanc Glacier	AMZ-EDOT5-WHT	59.99	35	\N	Blanc Glacier
32	20	Nest Thermostat — Neige	GOO-NEST-T-WHT	129.99	15	\N	Coloris Neige
33	20	Nest Thermostat — Charbon	GOO-NEST-T-BLK	129.99	12	\N	Coloris Charbon
17	11	XPS 15 — 16 Go / 512 Go	DEL-XPS15-16-512	1500.00	5	\N	16 Go RAM / 512 Go SSD
30	19	Echo Dot 5 — Anthracite	AMZ-EDOT5-ANT	59.99	37	\N	Anthracite
19	12	VivoBook 16 — 16 Go / 512 Go	ASU-VB16-16-512	789.99	99	\N	16 Go RAM / 512 Go SSD
1	1	Raspberry Pi 5 — 4 Go RAM	RPI5-4GB	79.99	49	\N	4 Go
2	1	Raspberry Pi 5 — 8 Go RAM	RPI5-8GB	99.99	32	\N	8 Go
11	7	Neo QLED 55" 4K 2024	SAM-NQLED-55-4K	799.99	8	\N	55 pouces
\.


--
-- Name: admin_id_admin_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.admin_id_admin_seq', 6, true);


--
-- Name: adresse_id_adresse_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.adresse_id_adresse_seq', 3, true);


--
-- Name: avis_id_avis_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.avis_id_avis_seq', 3, true);


--
-- Name: categorie_id_categorie_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categorie_id_categorie_seq', 11, true);


--
-- Name: client_id_client_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.client_id_client_seq', 6, true);


--
-- Name: code_promo_id_code_promo_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.code_promo_id_code_promo_seq', 1, true);


--
-- Name: commande_id_commande_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.commande_id_commande_seq', 7, true);


--
-- Name: conversation_id_conversation_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.conversation_id_conversation_seq', 2, true);


--
-- Name: image_produit_id_image_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.image_produit_id_image_seq', 47, true);


--
-- Name: liste_envie_id_liste_envie_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.liste_envie_id_liste_envie_seq', 2, true);


--
-- Name: message_chat_id_message_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.message_chat_id_message_seq', 5, true);


--
-- Name: message_contact_id_message_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.message_contact_id_message_seq', 3, true);


--
-- Name: panier_id_panier_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.panier_id_panier_seq', 17, true);


--
-- Name: produit_id_produit_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.produit_id_produit_seq', 20, true);


--
-- Name: promotion_id_promotion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.promotion_id_promotion_seq', 1, true);


--
-- Name: root_id_root_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.root_id_root_seq', 2, true);


--
-- Name: support_id_support_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.support_id_support_seq', 5, true);


--
-- Name: transporteur_id_transporteur_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.transporteur_id_transporteur_seq', 3, true);


--
-- Name: variante_produit_id_variante_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.variante_produit_id_variante_seq', 33, true);


--
-- Name: admin admin_email_admin_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin
    ADD CONSTRAINT admin_email_admin_key UNIQUE (email_admin);


--
-- Name: admin admin_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin
    ADD CONSTRAINT admin_pkey PRIMARY KEY (id_admin);


--
-- Name: adresse adresse_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.adresse
    ADD CONSTRAINT adresse_pkey PRIMARY KEY (id_adresse);


--
-- Name: avis avis_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.avis
    ADD CONSTRAINT avis_pkey PRIMARY KEY (id_avis);


--
-- Name: categorie categorie_nom_categorie_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorie
    ADD CONSTRAINT categorie_nom_categorie_key UNIQUE (nom_categorie);


--
-- Name: categorie categorie_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorie
    ADD CONSTRAINT categorie_pkey PRIMARY KEY (id_categorie);


--
-- Name: client client_email_client_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.client
    ADD CONSTRAINT client_email_client_key UNIQUE (email_client);


--
-- Name: client client_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.client
    ADD CONSTRAINT client_pkey PRIMARY KEY (id_client);


--
-- Name: code_promo code_promo_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.code_promo
    ADD CONSTRAINT code_promo_code_key UNIQUE (code);


--
-- Name: code_promo code_promo_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.code_promo
    ADD CONSTRAINT code_promo_pkey PRIMARY KEY (id_code_promo);


--
-- Name: commande commande_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.commande
    ADD CONSTRAINT commande_pkey PRIMARY KEY (id_commande);


--
-- Name: commande_variante commande_variante_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.commande_variante
    ADD CONSTRAINT commande_variante_pkey PRIMARY KEY (id_commande, id_variante);


--
-- Name: conversation conversation_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversation
    ADD CONSTRAINT conversation_pkey PRIMARY KEY (id_conversation);


--
-- Name: image_produit image_produit_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.image_produit
    ADD CONSTRAINT image_produit_pkey PRIMARY KEY (id_image);


--
-- Name: liste_envie liste_envie_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.liste_envie
    ADD CONSTRAINT liste_envie_pkey PRIMARY KEY (id_liste_envie);


--
-- Name: message_chat message_chat_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.message_chat
    ADD CONSTRAINT message_chat_pkey PRIMARY KEY (id_message);


--
-- Name: message_contact message_contact_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.message_contact
    ADD CONSTRAINT message_contact_pkey PRIMARY KEY (id_message);


--
-- Name: panier panier_id_session_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.panier
    ADD CONSTRAINT panier_id_session_key UNIQUE (id_session);


--
-- Name: panier panier_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.panier
    ADD CONSTRAINT panier_pkey PRIMARY KEY (id_panier);


--
-- Name: panier_variante panier_variante_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.panier_variante
    ADD CONSTRAINT panier_variante_pkey PRIMARY KEY (id_panier, id_variante);


--
-- Name: produit produit_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produit
    ADD CONSTRAINT produit_pkey PRIMARY KEY (id_produit);


--
-- Name: promotion promotion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.promotion
    ADD CONSTRAINT promotion_pkey PRIMARY KEY (id_promotion);


--
-- Name: promotion_variante promotion_variante_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.promotion_variante
    ADD CONSTRAINT promotion_variante_pkey PRIMARY KEY (id_promotion, id_variante);


--
-- Name: root root_login_root_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.root
    ADD CONSTRAINT root_login_root_key UNIQUE (login_root);


--
-- Name: root root_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.root
    ADD CONSTRAINT root_pkey PRIMARY KEY (id_root);


--
-- Name: support support_email_support_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.support
    ADD CONSTRAINT support_email_support_key UNIQUE (email_support);


--
-- Name: support support_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.support
    ADD CONSTRAINT support_pkey PRIMARY KEY (id_support);


--
-- Name: transporteur transporteur_nom_transporteur_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transporteur
    ADD CONSTRAINT transporteur_nom_transporteur_key UNIQUE (nom_transporteur);


--
-- Name: transporteur transporteur_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transporteur
    ADD CONSTRAINT transporteur_pkey PRIMARY KEY (id_transporteur);


--
-- Name: variante_produit variante_produit_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.variante_produit
    ADD CONSTRAINT variante_produit_pkey PRIMARY KEY (id_variante);


--
-- Name: variante_produit variante_produit_sku_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.variante_produit
    ADD CONSTRAINT variante_produit_sku_key UNIQUE (sku);


--
-- Name: adresse adresse_id_client_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.adresse
    ADD CONSTRAINT adresse_id_client_fkey FOREIGN KEY (id_client) REFERENCES public.client(id_client) ON DELETE CASCADE;


--
-- Name: avis avis_id_client_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.avis
    ADD CONSTRAINT avis_id_client_fkey FOREIGN KEY (id_client) REFERENCES public.client(id_client);


--
-- Name: avis avis_id_variante_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.avis
    ADD CONSTRAINT avis_id_variante_fkey FOREIGN KEY (id_variante) REFERENCES public.variante_produit(id_variante);


--
-- Name: categorie categorie_id_categorie_parent_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorie
    ADD CONSTRAINT categorie_id_categorie_parent_fkey FOREIGN KEY (id_categorie_parent) REFERENCES public.categorie(id_categorie) ON DELETE SET NULL;


--
-- Name: commande commande_id_adresse_facturation_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.commande
    ADD CONSTRAINT commande_id_adresse_facturation_fkey FOREIGN KEY (id_adresse_facturation) REFERENCES public.adresse(id_adresse);


--
-- Name: commande commande_id_adresse_livraison_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.commande
    ADD CONSTRAINT commande_id_adresse_livraison_fkey FOREIGN KEY (id_adresse_livraison) REFERENCES public.adresse(id_adresse);


--
-- Name: commande commande_id_client_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.commande
    ADD CONSTRAINT commande_id_client_fkey FOREIGN KEY (id_client) REFERENCES public.client(id_client);


--
-- Name: commande commande_id_code_promo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.commande
    ADD CONSTRAINT commande_id_code_promo_fkey FOREIGN KEY (id_code_promo) REFERENCES public.code_promo(id_code_promo);


--
-- Name: commande commande_id_transporteur_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.commande
    ADD CONSTRAINT commande_id_transporteur_fkey FOREIGN KEY (id_transporteur) REFERENCES public.transporteur(id_transporteur);


--
-- Name: commande_variante commande_variante_id_commande_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.commande_variante
    ADD CONSTRAINT commande_variante_id_commande_fkey FOREIGN KEY (id_commande) REFERENCES public.commande(id_commande) ON DELETE CASCADE;


--
-- Name: commande_variante commande_variante_id_variante_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.commande_variante
    ADD CONSTRAINT commande_variante_id_variante_fkey FOREIGN KEY (id_variante) REFERENCES public.variante_produit(id_variante);


--
-- Name: conversation conversation_id_client_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversation
    ADD CONSTRAINT conversation_id_client_fkey FOREIGN KEY (id_client) REFERENCES public.client(id_client);


--
-- Name: conversation conversation_id_support_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversation
    ADD CONSTRAINT conversation_id_support_fkey FOREIGN KEY (id_support) REFERENCES public.support(id_support);


--
-- Name: image_produit image_produit_id_variante_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.image_produit
    ADD CONSTRAINT image_produit_id_variante_fkey FOREIGN KEY (id_variante) REFERENCES public.variante_produit(id_variante) ON DELETE CASCADE;


--
-- Name: liste_envie liste_envie_id_client_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.liste_envie
    ADD CONSTRAINT liste_envie_id_client_fkey FOREIGN KEY (id_client) REFERENCES public.client(id_client) ON DELETE CASCADE;


--
-- Name: liste_envie liste_envie_id_variante_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.liste_envie
    ADD CONSTRAINT liste_envie_id_variante_fkey FOREIGN KEY (id_variante) REFERENCES public.variante_produit(id_variante) ON DELETE CASCADE;


--
-- Name: message_chat message_chat_id_conversation_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.message_chat
    ADD CONSTRAINT message_chat_id_conversation_fkey FOREIGN KEY (id_conversation) REFERENCES public.conversation(id_conversation) ON DELETE CASCADE;


--
-- Name: message_contact message_contact_id_client_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.message_contact
    ADD CONSTRAINT message_contact_id_client_fkey FOREIGN KEY (id_client) REFERENCES public.client(id_client);


--
-- Name: panier panier_id_client_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.panier
    ADD CONSTRAINT panier_id_client_fkey FOREIGN KEY (id_client) REFERENCES public.client(id_client) ON DELETE CASCADE;


--
-- Name: panier_variante panier_variante_id_panier_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.panier_variante
    ADD CONSTRAINT panier_variante_id_panier_fkey FOREIGN KEY (id_panier) REFERENCES public.panier(id_panier) ON DELETE CASCADE;


--
-- Name: panier_variante panier_variante_id_variante_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.panier_variante
    ADD CONSTRAINT panier_variante_id_variante_fkey FOREIGN KEY (id_variante) REFERENCES public.variante_produit(id_variante) ON DELETE CASCADE;


--
-- Name: produit produit_id_categorie_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produit
    ADD CONSTRAINT produit_id_categorie_fkey FOREIGN KEY (id_categorie) REFERENCES public.categorie(id_categorie) ON DELETE SET NULL;


--
-- Name: promotion_variante promotion_variante_id_promotion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.promotion_variante
    ADD CONSTRAINT promotion_variante_id_promotion_fkey FOREIGN KEY (id_promotion) REFERENCES public.promotion(id_promotion) ON DELETE CASCADE;


--
-- Name: promotion_variante promotion_variante_id_variante_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.promotion_variante
    ADD CONSTRAINT promotion_variante_id_variante_fkey FOREIGN KEY (id_variante) REFERENCES public.variante_produit(id_variante) ON DELETE CASCADE;


--
-- Name: variante_produit variante_produit_id_produit_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.variante_produit
    ADD CONSTRAINT variante_produit_id_produit_fkey FOREIGN KEY (id_produit) REFERENCES public.produit(id_produit) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict CEzoTfXRG11egxEiuvZzfWntgzrmaUGlWLffXhgKQiCldPvOSGelTBsslx6aoVa

