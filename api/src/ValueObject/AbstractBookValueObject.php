<?php

declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

abstract readonly class AbstractBookValueObject
{
    /**
     * @param int[] $authorsIds
     * @param int[] $subjectsIds
     */
    public function __construct(
        private ?string $title = null,
        private ?string $publisher = null,
        private ?int $edition = null,
        private ?string $publicationYear = null,
        private ?float $amount = null,
        private array $authorsIds = [],
        private array $subjectsIds = [],
    ) {
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function getEdition(): ?int
    {
        return $this->edition;
    }

    public function getPublicationYear(): ?string
    {
        return $this->publicationYear;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @return int[]
     */
    public function getAuthorsIds(): array
    {
        return array_unique($this->authorsIds);
    }

    /**
     * @return int[]
     */
    public function getSubjectsIds(): array
    {
        return array_unique($this->subjectsIds);
    }

    public function validate(): void
    {
        $data = get_object_vars($this);

        $constraint = new Assert\Collection(
            [
                'title' => [
                    new Assert\NotBlank(message: 'Título é obrigatório.'),
                    new Assert\Length(
                        min: 1,
                        max: 40,
                        minMessage: 'Título deve ter pelo menos 1 caractere.',
                        maxMessage: 'Título não pode ultrapassar 40 caracteres.'
                    ),
                    new Assert\Type('string', 'Título deve ser uma string/texto.'),
                ],
                'amount' => [
                    new Assert\NotBlank(message: 'Valor do livro é obrigatório.'),
                    new Assert\PositiveOrZero(message: 'Valor do livro não pode ser negativo.'),
                    new Assert\Type('float', message: 'Valor do livro deve ser um float (exemplo 10.00).'),
                ],
                'publisher' => [
                    new Assert\NotBlank(message: 'Editora é obrigatória.'),
                    new Assert\Length(
                        min: 1,
                        max: 40,
                        minMessage: 'Editora deve ter pelo menos 1 caractere.',
                        maxMessage: 'Editora não pode ultrapassar 40 caracteres.'
                    ),
                    new Assert\Type('string', 'Editora deve ser uma string/texto.'),
                ],
                'edition' => [
                    new Assert\NotBlank(message: 'Edição é obrigatória.'),
                    new Assert\Type('int', 'Edição deve ser um número inteiro.'),
                    new Assert\Positive(message: 'Edição deve ser um número positivo.'),
                ],
                'publicationYear' => [
                    new Assert\NotBlank(message: 'O Ano da Publicação é obrigatória.'),
                    new Assert\Length(exactly: 4, exactMessage: 'Ano da Publicação deve ter 4 dígitos.'),
                    new Assert\Positive(message: 'Ano da Publicação deve ser um número positivo.'),
                ],
                'authorsIds' => [
                    new Assert\NotBlank(message: 'Os autores precisam ser informados.'),
                    new Assert\Count(['min' => 1], minMessage: 'O livro deve estar vinculado a pelo menos 1 autor.'),
                    new Assert\Type('array', 'O livro deve ser um array de IDs.'),
                    new Assert\All([
                        new Assert\Type('int', 'O ID do assunto deve ser um número inteiro.'),
                        new Assert\Positive(message: 'O ID do assunto deve ser um número positivo.'),
                    ])
                ],
                'subjectsIds' => [
                    new Assert\NotBlank(message: 'Os assuntos precisam ser informados.'),
                    new Assert\Count(['min' => 1], minMessage: 'O livro deve estar vinculado a pelo menos 1 assunto.'),
                    new Assert\Type('array', 'O livro deve ser um array de IDs.'),
                    new Assert\All([
                        new Assert\Type('int', 'O ID do assunto deve ser um número inteiro.'),
                        new Assert\Positive(message: 'O ID do assunto deve ser um número positivo.'),
                    ])
                ]
            ]
        );

        $validator = Validation::createValidator();
        $constraints = $validator->validate($data, $constraint);

        if ($constraints->count() === 0) {
            return;
        }

        $messages = [];
        foreach ($constraints as $constraint) {
            $messages[] = sprintf('%s %s', $constraint->getPropertyPath(), $constraint->getMessage());
        }

        if (false === empty($messages)) {
            throw new \DomainException(
                implode(' ', $messages)
            );
        }
    }
}
