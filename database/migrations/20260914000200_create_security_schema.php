<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSecuritySchema extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(<<<'SQL'
CREATE OR REPLACE FUNCTION fn_atualizar_updated_at()
RETURNS trigger AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TABLE seg_usuarios (
    seg_usu_id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    seg_usu_nome varchar(150) NOT NULL,
    seg_usu_login varchar(100) NOT NULL,
    seg_usu_email varchar(150),
    seg_usu_senha_hash varchar(255),
    seg_usu_ativo boolean NOT NULL DEFAULT true,
    created_at timestamp NOT NULL DEFAULT now(),
    updated_at timestamp,
    CONSTRAINT uk_seg_usuarios_login UNIQUE (seg_usu_login)
);

CREATE TABLE seg_grupos (
    seg_gru_id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    seg_gru_codigo varchar(50) NOT NULL,
    seg_gru_nome varchar(100) NOT NULL,
    seg_gru_descricao varchar(250),
    seg_gru_ativo boolean NOT NULL DEFAULT true,
    created_at timestamp NOT NULL DEFAULT now(),
    updated_at timestamp,
    CONSTRAINT uk_seg_grupos_codigo UNIQUE (seg_gru_codigo),
    CONSTRAINT uk_seg_grupos_nome UNIQUE (seg_gru_nome)
);

CREATE TABLE seg_usuarios_grupos (
    seg_usg_id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    seg_usg_usu_id integer NOT NULL REFERENCES seg_usuarios (seg_usu_id),
    seg_usg_gru_id integer NOT NULL REFERENCES seg_grupos (seg_gru_id),
    created_at timestamp NOT NULL DEFAULT now(),
    updated_at timestamp,
    CONSTRAINT uk_seg_usuarios_grupos UNIQUE (seg_usg_usu_id, seg_usg_gru_id)
);

CREATE TABLE seg_tipos_aplicacoes (
    seg_tap_id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    seg_tap_codigo varchar(50) NOT NULL,
    seg_tap_nome varchar(100) NOT NULL,
    seg_tap_descricao varchar(250),
    seg_tap_ativo boolean NOT NULL DEFAULT true,
    created_at timestamp NOT NULL DEFAULT now(),
    updated_at timestamp,
    CONSTRAINT uk_seg_tipos_aplicacoes_codigo UNIQUE (seg_tap_codigo),
    CONSTRAINT uk_seg_tipos_aplicacoes_nome UNIQUE (seg_tap_nome)
);

CREATE TABLE seg_aplicacoes (
    seg_apl_id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    seg_apl_tap_id integer NOT NULL REFERENCES seg_tipos_aplicacoes (seg_tap_id),
    seg_apl_codigo varchar(100) NOT NULL,
    seg_apl_nome varchar(150) NOT NULL,
    seg_apl_descricao varchar(250),
    seg_apl_ativo boolean NOT NULL DEFAULT true,
    created_at timestamp NOT NULL DEFAULT now(),
    updated_at timestamp,
    CONSTRAINT uk_seg_aplicacoes_codigo UNIQUE (seg_apl_codigo)
);

CREATE TABLE seg_permissoes (
    seg_per_id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    seg_per_codigo varchar(50) NOT NULL,
    seg_per_nome varchar(100) NOT NULL,
    seg_per_descricao varchar(250),
    seg_per_ativo boolean NOT NULL DEFAULT true,
    created_at timestamp NOT NULL DEFAULT now(),
    updated_at timestamp,
    CONSTRAINT uk_seg_permissoes_codigo UNIQUE (seg_per_codigo),
    CONSTRAINT uk_seg_permissoes_nome UNIQUE (seg_per_nome)
);

CREATE TABLE seg_tipos_aplicacoes_permissoes (
    seg_tpp_id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    seg_tpp_tap_id integer NOT NULL REFERENCES seg_tipos_aplicacoes (seg_tap_id),
    seg_tpp_per_id integer NOT NULL REFERENCES seg_permissoes (seg_per_id),
    created_at timestamp NOT NULL DEFAULT now(),
    updated_at timestamp,
    CONSTRAINT uk_seg_tipos_aplicacoes_permissoes UNIQUE (seg_tpp_tap_id, seg_tpp_per_id)
);

CREATE TABLE seg_grupos_aplicacoes_permissoes (
    seg_gap_id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    seg_gap_gru_id integer NOT NULL REFERENCES seg_grupos (seg_gru_id),
    seg_gap_apl_id integer NOT NULL REFERENCES seg_aplicacoes (seg_apl_id),
    seg_gap_per_id integer NOT NULL REFERENCES seg_permissoes (seg_per_id),
    created_at timestamp NOT NULL DEFAULT now(),
    updated_at timestamp,
    CONSTRAINT uk_seg_grupos_aplicacoes_permissoes UNIQUE (seg_gap_gru_id, seg_gap_apl_id, seg_gap_per_id)
);

INSERT INTO seg_grupos (seg_gru_codigo, seg_gru_nome, seg_gru_descricao, seg_gru_ativo)
VALUES ('ADMIN', 'Administradores', 'Grupo com acesso irrestrito a todas as funcionalidades do sistema.', true);

CREATE INDEX idx_seg_usuarios_grupos_gru_id ON seg_usuarios_grupos (seg_usg_gru_id);
CREATE INDEX idx_seg_aplicacoes_tap_id ON seg_aplicacoes (seg_apl_tap_id);
CREATE INDEX idx_seg_tipos_aplicacoes_permissoes_per_id ON seg_tipos_aplicacoes_permissoes (seg_tpp_per_id);
CREATE INDEX idx_seg_grupos_aplicacoes_permissoes_apl_id ON seg_grupos_aplicacoes_permissoes (seg_gap_apl_id);
CREATE INDEX idx_seg_grupos_aplicacoes_permissoes_per_id ON seg_grupos_aplicacoes_permissoes (seg_gap_per_id);

CREATE TRIGGER trg_seg_usuarios_updated_at BEFORE UPDATE ON seg_usuarios FOR EACH ROW EXECUTE FUNCTION fn_atualizar_updated_at();
CREATE TRIGGER trg_seg_grupos_updated_at BEFORE UPDATE ON seg_grupos FOR EACH ROW EXECUTE FUNCTION fn_atualizar_updated_at();
CREATE TRIGGER trg_seg_usuarios_grupos_updated_at BEFORE UPDATE ON seg_usuarios_grupos FOR EACH ROW EXECUTE FUNCTION fn_atualizar_updated_at();
CREATE TRIGGER trg_seg_tipos_aplicacoes_updated_at BEFORE UPDATE ON seg_tipos_aplicacoes FOR EACH ROW EXECUTE FUNCTION fn_atualizar_updated_at();
CREATE TRIGGER trg_seg_aplicacoes_updated_at BEFORE UPDATE ON seg_aplicacoes FOR EACH ROW EXECUTE FUNCTION fn_atualizar_updated_at();
CREATE TRIGGER trg_seg_permissoes_updated_at BEFORE UPDATE ON seg_permissoes FOR EACH ROW EXECUTE FUNCTION fn_atualizar_updated_at();
CREATE TRIGGER trg_seg_tipos_aplicacoes_permissoes_updated_at BEFORE UPDATE ON seg_tipos_aplicacoes_permissoes FOR EACH ROW EXECUTE FUNCTION fn_atualizar_updated_at();
CREATE TRIGGER trg_seg_grupos_aplicacoes_permissoes_updated_at BEFORE UPDATE ON seg_grupos_aplicacoes_permissoes FOR EACH ROW EXECUTE FUNCTION fn_atualizar_updated_at();
SQL);
    }

    public function down(): void
    {
        $this->execute(<<<'SQL'
DROP TABLE IF EXISTS seg_grupos_aplicacoes_permissoes;
DROP TABLE IF EXISTS seg_tipos_aplicacoes_permissoes;
DROP TABLE IF EXISTS seg_aplicacoes;
DROP TABLE IF EXISTS seg_tipos_aplicacoes;
DROP TABLE IF EXISTS seg_usuarios_grupos;
DROP TABLE IF EXISTS seg_permissoes;
DROP TABLE IF EXISTS seg_grupos;
DROP TABLE IF EXISTS seg_usuarios;
DROP FUNCTION IF EXISTS fn_atualizar_updated_at();
SQL);
    }
}
