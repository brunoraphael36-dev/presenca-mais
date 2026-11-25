BEGIN;

CREATE TABLE IF NOT EXISTS "Grupo" (
    id SERIAL PRIMARY KEY,
    descricao VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS "Participante" (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    matricula VARCHAR(50) NOT NULL UNIQUE,
    "grupoId" INTEGER NOT NULL REFERENCES "Grupo"(id),
    idade INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS "Presenca" (
    id SERIAL PRIMARY KEY,
    matricula VARCHAR(50) NOT NULL,
    data DATE NOT NULL,
    presenca SMALLINT NOT NULL CHECK (presenca IN (0, 1, 2)),
    justificativa VARCHAR(255),
    "grupoId" INTEGER REFERENCES "Grupo"(id)
);

CREATE TABLE IF NOT EXISTS "user" (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    auth_key VARCHAR(32) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    password_reset_token VARCHAR(255),
    email VARCHAR(255) NOT NULL UNIQUE,
    status SMALLINT NOT NULL DEFAULT 1,
    created_at BIGINT NOT NULL,
    updated_at BIGINT NOT NULL
);

CREATE TABLE IF NOT EXISTS permission (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS user_permission (
    user_id INTEGER NOT NULL REFERENCES "user"(id) ON DELETE CASCADE,
    permission_id INTEGER NOT NULL REFERENCES permission(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, permission_id)
);

INSERT INTO "Grupo" (id, descricao) VALUES
    (1, 'Grupo Aventura'),
    (2, 'Grupo Exploradores')
ON CONFLICT (id) DO UPDATE SET descricao = EXCLUDED.descricao;

INSERT INTO "Participante" (id, nome, matricula, "grupoId", idade) VALUES
    (1, 'Ana Exemplo', 'DEMO-001', 1, 12),
    (2, 'Bruno Exemplo', 'DEMO-002', 1, 13),
    (3, 'Caio Exemplo', 'DEMO-003', 1, 11),
    (4, 'Diana Exemplo', 'DEMO-004', 2, 14),
    (5, 'Eva Exemplo', 'DEMO-005', 2, 12)
ON CONFLICT (id) DO UPDATE SET
    nome = EXCLUDED.nome,
    matricula = EXCLUDED.matricula,
    "grupoId" = EXCLUDED."grupoId",
    idade = EXCLUDED.idade;

INSERT INTO "Presenca" (id, matricula, data, presenca, justificativa, "grupoId") VALUES
    (1, 'DEMO-001', CURRENT_DATE - 7, 1, NULL, 1),
    (2, 'DEMO-002', CURRENT_DATE - 7, 0, NULL, 1),
    (3, 'DEMO-003', CURRENT_DATE - 7, 2, 'Compromisso familiar', 1),
    (4, 'DEMO-004', CURRENT_DATE - 3, 1, NULL, 2),
    (5, 'DEMO-005', CURRENT_DATE - 3, 1, NULL, 2)
ON CONFLICT (id) DO UPDATE SET
    matricula = EXCLUDED.matricula,
    data = EXCLUDED.data,
    presenca = EXCLUDED.presenca,
    justificativa = EXCLUDED.justificativa,
    "grupoId" = EXCLUDED."grupoId";

INSERT INTO "user" (
    id, username, auth_key, password_hash, email, status, created_at, updated_at
) VALUES (
    1,
    'demo',
    'demo-local-auth-key',
    '$2y$10$9tqaP/TElJcZnNqMwmaArOXIhldotZ5wcBUZ9znZNcnjhHNbKaVGS',
    'demo@example.test',
    1,
    FLOOR(EXTRACT(EPOCH FROM NOW()))::BIGINT,
    FLOOR(EXTRACT(EPOCH FROM NOW()))::BIGINT
)
ON CONFLICT (id) DO UPDATE SET
    username = EXCLUDED.username,
    auth_key = EXCLUDED.auth_key,
    password_hash = EXCLUDED.password_hash,
    email = EXCLUDED.email,
    status = EXCLUDED.status,
    updated_at = EXCLUDED.updated_at;

INSERT INTO permission (id, name) VALUES
    (1, 'presenca.access'),
    (2, 'grupo.access'),
    (3, 'participante.access')
ON CONFLICT (id) DO UPDATE SET name = EXCLUDED.name;

INSERT INTO user_permission (user_id, permission_id) VALUES
    (1, 1),
    (1, 2),
    (1, 3)
ON CONFLICT (user_id, permission_id) DO NOTHING;

SELECT setval(pg_get_serial_sequence('"Grupo"', 'id'), COALESCE((SELECT MAX(id) FROM "Grupo"), 1), true);
SELECT setval(pg_get_serial_sequence('"Participante"', 'id'), COALESCE((SELECT MAX(id) FROM "Participante"), 1), true);
SELECT setval(pg_get_serial_sequence('"Presenca"', 'id'), COALESCE((SELECT MAX(id) FROM "Presenca"), 1), true);
SELECT setval(pg_get_serial_sequence('"user"', 'id'), COALESCE((SELECT MAX(id) FROM "user"), 1), true);
SELECT setval(pg_get_serial_sequence('permission', 'id'), COALESCE((SELECT MAX(id) FROM permission), 1), true);

COMMIT;
