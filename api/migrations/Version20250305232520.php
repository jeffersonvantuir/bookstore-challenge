<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250305232520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cria a View utilizada para o relatório';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE OR REPLACE VIEW view_report_author
            AS
                 SELECT 
                    DISTINCT 
                    ROW_NUMBER() OVER () AS id,
                    a.CodAu author_id,
                    a.Nome author_name,
                    l.Codl book_id,
                    l.Titulo book_title,
                    l.Editora publisher,
                    l.Edicao edition,
                    l.AnoPublicacao publication_year,
                    l.Valor amount,
                    GROUP_CONCAT(a2.Descricao) subjects
                FROM Autor a 
                LEFT JOIN Livro_Autor la on la.Autor_CodAu = a.CodAu 
                LEFT JOIN Livro l on l.Codl = la.Livro_Codl 
                LEFT JOIN Livro_Assunto la2 on la2.Livro_Codl = l.Codl 
                LEFT JOIN Assunto a2 on a2.CodAs = la2.Assunto_CodAs 
                GROUP BY a.CodAu, a.Nome, l.Codl, l.Titulo, l.Edicao, l.Editora, l.AnoPublicacao, l.Valor
                ORDER BY a.Nome ASC
            ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS VIEW_REPORT_AUTHOR');
    }
}
