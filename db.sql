-- ============================
-- TABLE PRODUIT
-- ============================
CREATE TABLE produit (
    id_produit INTEGER PRIMARY KEY AUTOINCREMENT,
    designation TEXT NOT NULL,
    prix REAL NOT NULL,
    quantite_stock INTEGER NOT NULL DEFAULT 0
);

-- ============================
-- TABLE CAISSE
-- ============================
CREATE TABLE caisse (
    id_caisse INTEGER PRIMARY KEY AUTOINCREMENT,
    numero TEXT NOT NULL,
    nom_caissier TEXT
);

-- ============================
-- TABLE UTILISATEUR (pour le login - Travaux à faire 4)
-- ============================
CREATE TABLE utilisateur (
    id_utilisateur INTEGER PRIMARY KEY AUTOINCREMENT,
    login TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    nom TEXT
);

-- ============================
-- TABLE ACHAT (en-tête d'achat = un client/passage en caisse)
-- ============================
CREATE TABLE achat (
    id_achat INTEGER PRIMARY KEY AUTOINCREMENT,
    id_caisse INTEGER NOT NULL,
    date_achat TEXT NOT NULL DEFAULT (datetime('now')),
    statut TEXT NOT NULL DEFAULT 'EN_COURS', -- EN_COURS ou CLOTURE
    total REAL DEFAULT 0,
    FOREIGN KEY (id_caisse) REFERENCES caisse(id_caisse)
);

-- ============================
-- TABLE DETAIL_ACHAT (les lignes produits d'un achat)
-- ============================
CREATE TABLE detail_achat (
    id_detail INTEGER PRIMARY KEY AUTOINCREMENT,
    id_achat INTEGER NOT NULL,
    id_produit INTEGER NOT NULL,
    quantite INTEGER NOT NULL,
    prix_unitaire REAL NOT NULL,
    sous_total REAL NOT NULL,
    FOREIGN KEY (id_achat) REFERENCES achat(id_achat),
    FOREIGN KEY (id_produit) REFERENCES produit(id_produit)
);

-- ============================
-- DONNÉES : 5 PRODUITS
-- ============================
INSERT INTO produit (designation, prix, quantite_stock) VALUES
('Riz 1kg', 3500, 100),
('Huile 1L', 8000, 50),
('Sucre 1kg', 4200, 80),
('Savon', 1500, 200),
('Lait en poudre 400g', 12000, 60);

-- ============================
-- DONNÉES : 2 CAISSES
-- ============================
INSERT INTO caisse (numero, nom_caissier) VALUES
('Caisse 1', 'Hery'),
('Caisse 2', 'Mialy');

-- ============================
-- DONNÉES : 1 UTILISATEUR TEST (Travaux à faire 4)
-- ============================
INSERT INTO utilisateur (login, password, nom) VALUES
('admin', 'admin123', 'Administrateur');