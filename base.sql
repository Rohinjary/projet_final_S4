CREATE TABLE operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(100) NOT NULL UNIQUE,
    est_principal INTEGER NOT NULL DEFAULT 0,
    date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE prefixe_valable (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_id INTEGER NOT NULL,
    prefixe VARCHAR(3) NOT NULL UNIQUE,
    date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operateur_id) REFERENCES operateur(id)
);

CREATE TABLE type_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE client (
    numero VARCHAR(10) PRIMARY KEY,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bareme_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    montant_min NUMERIC NOT NULL,
    montant_max NUMERIC NOT NULL,
    montant_frais NUMERIC NOT NULL,
    date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_fin DATETIME,
    FOREIGN KEY (type_operation_id) REFERENCES type_operation(id)
);

CREATE TABLE operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_numero VARCHAR(10) NOT NULL,
    type_operation_id INTEGER NOT NULL,
    destinataire_numero VARCHAR(10),
    montant NUMERIC NOT NULL,
    frais NUMERIC NOT NULL DEFAULT 0,
    date_operation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_numero) REFERENCES client(numero),
    FOREIGN KEY (type_operation_id) REFERENCES type_operation(id),
    FOREIGN KEY (destinataire_numero) REFERENCES client(numero)
);

CREATE TABLE user (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_id INTEGER NOT NULL,
    password VARCHAR(255) NOT NULL,
    date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operateur_id) REFERENCES operateur(id)
);

CREATE TABLE commission_operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_id INTEGER NOT NULL,
    pourcentage NUMERIC NOT NULL CHECK (pourcentage >= 0 AND pourcentage <= 100),
    date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operateur_id) REFERENCES operateur(id)
);

CREATE INDEX idx_prefixe_operateur ON prefixe_valable(operateur_id);
CREATE INDEX idx_operation_date ON operation(date_operation);
CREATE INDEX idx_operation_client ON operation(client_numero);

CREATE TABLE epargne_client(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_numero INTEGER NOT NULL,
    pourcentage NUMERIC,
    date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_numero) REFERENCES client(numero)
)

CREATE TABLE mouvement_epargne(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_numero INTEGER NOT NULL,
    montant_epargne NUMERIC,
    date_epargne DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_numero) REFERENCES client(numero)
)