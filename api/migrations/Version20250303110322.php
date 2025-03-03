<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250303110322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cria as tabelas Assunto, Autor, Livro e tabelas relacionais';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE Assunto (CodAs INT AUTO_INCREMENT NOT NULL, Descricao VARCHAR(20) NOT NULL COMMENT \'Descrição do assunto\', PRIMARY KEY(CodAs)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Autor (CodAu INT AUTO_INCREMENT NOT NULL, Nome VARCHAR(40) NOT NULL COMMENT \'Nome do Autor\', PRIMARY KEY(CodAu)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Livro (Codl INT AUTO_INCREMENT NOT NULL, Titulo VARCHAR(40) NOT NULL COMMENT \'Título do livro\', Editora VARCHAR(40) NOT NULL COMMENT \'Editora do livro\', Edicao INT UNSIGNED NOT NULL COMMENT \'Edição do livro\', AnoPublicacao VARCHAR(4) NOT NULL COMMENT \'Ano de Publicação do livro\', Valor NUMERIC(10, 2) NOT NULL COMMENT \'Valor (R$) do livro\', PRIMARY KEY(Codl)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Livro_Assunto (Livro_Codl INT NOT NULL, Assunto_CodAs INT NOT NULL, INDEX Livro_Assunto_FKIndex1 (Livro_Codl), INDEX Livro_Assunto_FKIndex2 (Assunto_CodAs), PRIMARY KEY(Livro_Codl, Assunto_CodAs)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Livro_Autor (Livro_Codl INT NOT NULL, Autor_CodAu INT NOT NULL, INDEX Livro_Autor_FKIndex1 (Livro_Codl), INDEX Livro_Autor_FKIndex2 (Autor_CodAu), PRIMARY KEY(Livro_Codl, Autor_CodAu)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Livro_Assunto ADD CONSTRAINT FK_2F01B7434A5AFC39 FOREIGN KEY (Livro_Codl) REFERENCES Livro (Codl)');
        $this->addSql('ALTER TABLE Livro_Assunto ADD CONSTRAINT FK_2F01B74364209C06 FOREIGN KEY (Assunto_CodAs) REFERENCES Assunto (CodAs)');
        $this->addSql('ALTER TABLE Livro_Autor ADD CONSTRAINT FK_412939414A5AFC39 FOREIGN KEY (Livro_Codl) REFERENCES Livro (Codl)');
        $this->addSql('ALTER TABLE Livro_Autor ADD CONSTRAINT FK_41293941B44F3F36 FOREIGN KEY (Autor_CodAu) REFERENCES Autor (CodAu)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE Livro_Assunto DROP FOREIGN KEY FK_2F01B7434A5AFC39');
        $this->addSql('ALTER TABLE Livro_Assunto DROP FOREIGN KEY FK_2F01B74364209C06');
        $this->addSql('ALTER TABLE Livro_Autor DROP FOREIGN KEY FK_412939414A5AFC39');
        $this->addSql('ALTER TABLE Livro_Autor DROP FOREIGN KEY FK_41293941B44F3F36');
        $this->addSql('DROP TABLE Assunto');
        $this->addSql('DROP TABLE Autor');
        $this->addSql('DROP TABLE Livro');
        $this->addSql('DROP TABLE Livro_Assunto');
        $this->addSql('DROP TABLE Livro_Autor');
    }
}
