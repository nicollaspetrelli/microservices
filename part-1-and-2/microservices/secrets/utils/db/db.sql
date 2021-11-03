--
-- PostgreSQL database dump
--

-- Dumped from database version 13.3
-- Dumped by pg_dump version 13.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: secret; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.secret (
    id integer NOT NULL,
    name character varying(50) NOT NULL
);


ALTER TABLE public.secret OWNER TO postgres;

--
-- Name: secret_data; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.secret_data (
    id integer NOT NULL,
    id_secret integer NOT NULL,
    name character varying(50) NOT NULL,
    value character varying(200) NOT NULL,
    plaintext boolean DEFAULT false NOT NULL
);


ALTER TABLE public.secret_data OWNER TO postgres;

--
-- Name: secret_data_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.secret_data_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.secret_data_id_seq OWNER TO postgres;

--
-- Name: secret_data_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.secret_data_id_seq OWNED BY public.secret_data.id;


--
-- Name: secrets_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.secrets_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.secrets_id_seq OWNER TO postgres;

--
-- Name: secrets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.secrets_id_seq OWNED BY public.secret.id;


--
-- Name: secret id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.secret ALTER COLUMN id SET DEFAULT nextval('public.secrets_id_seq'::regclass);


--
-- Name: secret_data id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.secret_data ALTER COLUMN id SET DEFAULT nextval('public.secret_data_id_seq'::regclass);


--
-- Data for Name: secret; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.secret VALUES (1, 'Postgres');
INSERT INTO public.secret VALUES (2, 'RabbitMQ');
INSERT INTO public.secret VALUES (3, 'Máquina de staging');
INSERT INTO public.secret VALUES (4, 'Máquina de produção');


--
-- Data for Name: secret_data; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.secret_data VALUES (2, 1, 'password', 'UVQ+NpVTBdm8fis74HfReYLwjLiLtkhg4hRskE81xqYxihz9CdNZGUQxOv2U', false);
INSERT INTO public.secret_data VALUES (1, 1, 'username', 'postgres', true);
INSERT INTO public.secret_data VALUES (3, 2, 'host', 'rabbitmq.internal', true);
INSERT INTO public.secret_data VALUES (4, 2, 'username', 'user', true);
INSERT INTO public.secret_data VALUES (5, 2, 'password', 'ZFX1o7vY8lQ/kf+vKRtmQ0zWb1djqbVZz9f9keDwVRrNSSmXFM3toEsfarhR', false);
INSERT INTO public.secret_data VALUES (6, 3, 'username', 'staging', true);


--
-- Name: secret_data_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.secret_data_id_seq', 6, true);


--
-- Name: secrets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.secrets_id_seq', 4, true);


--
-- Name: secret_data secret_data_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.secret_data
    ADD CONSTRAINT secret_data_pkey PRIMARY KEY (id);


--
-- Name: secret secrets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.secret
    ADD CONSTRAINT secrets_pkey PRIMARY KEY (id);


--
-- Name: secret_data secret_data_id_secret_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.secret_data
    ADD CONSTRAINT secret_data_id_secret_fkey FOREIGN KEY (id_secret) REFERENCES public.secret(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

