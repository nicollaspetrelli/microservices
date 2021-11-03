CREATE TABLE public.users
(
    id    SERIAL PRIMARY KEY,
    name  character varying(100) NOT NULL,
    email character varying(100) NOT NULL UNIQUE
);
