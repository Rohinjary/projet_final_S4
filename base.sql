create table prefixe_valable(
    id int primary key auto_increment,
    operateur_id int not null,
    prefixe varchar(3) not null,
    date_ajout timestamp default current_timestamp,
    foreign key (operateur_id) references operateur(id)
);

create table type_operation(
    id int primary key auto_increment,
    libelle varchar(50) not null
);

create table client(
    numero varchar(10) primary key,
    nom varchar(50) null,
    prenom varchar(50) null,
    date_ajout timestamp default current_timestamp
);

create table bareme_frais(
    id int primary key auto_increment,
    type_operation_id int not null,
    montant_min numeric,
    montant_max numeric,
    montant_frais numeric,
    date_ajout timestamp default current_timestamp,
    date_fin timestamp default null,
    foreign key (type_operation_id) references type_operation(id)
);

create table operation(
    id int primary key auto_increment,
    client_numero varchar(10) not null,
    type_operation_id int not null,
    destinataire_numero varchar(10),
    montant numeric not null,
    frais numeric not null,
    date_operation timestamp default current_timestamp,
    foreign key (client_numero) references client(numero),
    foreign key (type_operation_id) references type_operation(id),
    foreign key (destinataire_numero) references client(numero),
)

CREATE TABLE operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(50) NOT NULL UNIQUE,
    est_principal INTEGER NOT NULL DEFAULT 0,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP
);

create table user(
    id int primary key auto_increment,
    operateur_id int not null,
    password varchar(255) not null,
    date_ajout timestamp default current_timestamp,
    foreign key (operateur_id) references operateur(id)
);

create table commission_operateur(
    id int primary key auto_increment,
    operateur_id int not null,
    pourcentage numeric not null,
    date_ajout timestamp default current_timestamp,
    foreign key (operateur_id) references operateur(id)
);