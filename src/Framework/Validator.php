<?php

namespace Framework;

use DateTime;
use Framework\Database\Table;
use Framework\Validator\ValidatorErrors;
use PDO;
use Psr\Http\Message\UploadedFileInterface;

class Validator
{
    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf'
    ];

    private $params;

    private $errors = [];

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Definit les champs obligatoires
     *
     * @param  mixed $keys les champs requis
     * @return self
     */
    public function required(...$keys): self
    {
        if (is_array($keys[0])) {
            $keys = $keys[0];
        }
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }

        return $this;
    }

    /**
     * Vérifie que le champ n'est pas vied
     *
     * @param  string $keys
     * @return self
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'empty');
            }
        }

        return $this;
    }


    /**
     * Vérifie la taille de la valeur de la clé
     *
     * @param  string $key
     * @param  int $min
     * @param  int $max
     * @return self
     */
    public function length(string $key, ?int $min = null, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (!is_null($min) && !is_null($max) &&
            $length < $min && $length > $max
        ) {
            $this->addError($key, 'betweeLength', [$min, $max]);
        }
        if (!is_null($min) &&  $length < $min) {
            $this->addError($key, 'minLength', [$min]);
        }
        if (!is_null($max) &&  $length > $max) {
            $this->addError($key, 'maxLength', [$max]);
        }

        return $this;
    }

    /**
     * Vérifie que le slug est valide
     *
     * @param  mixed $key
     * @return self
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+?)*$/';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }

    /**
     * Vérifie que le champ est numerique
     *
     * @param  mixed $key
     * @return self
     */
    public function numeric(string $key): self
    {
        $value = $this->getValue($key);
        if (!is_numeric($value)) {
            $this->addError($key, 'numeric');
        }
        return $this;
    }

    /**
     * Vérifie que le fichier a bien été uploader
     *
     * @param  string $key
     * @return self
     */
    public function uploaded(string $key): self
    {
        /** @var UploadedFileInterface */
        $file = $this->getValue($key);
        if ($file === null || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, 'uploaded');
        }
        return $this;
    }
    
    /**
     * Vérifie que l'email soit valide
     *
     * @param  string $key
     * @return self
     */
    public function email(string $key): self
    {
        $value = $this->getValue($key);
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($key, 'email');
        }
        return $this;
    }

    public function confirm(string $key): self
    {
        $value = $this->getValue($key);
        $valueConfirm = $this->getValue($key. '_confirm');
        if ($value !== $valueConfirm) {
            $this->addError($key, 'confirm');
        }
        return $this;
    }

    /**
     * Vérifie le format de fichier
     *
     * @param  string $key
     * @param  array $extensions
     * @return self
     */
    public function extension(string $key, array $extensions): self
    {
        /** @var UploadedFileInterface */
        $file = $this->getValue($key);
        if ($file !== null && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expectType = self::MIME_TYPES[$extension] ?? null;
            if (!in_array($extension, $extensions) || $expectType !== $type) {
                $this->addError($key, 'filetype', [join(',', $extensions)]);
            }
        }
        return $this;
    }

    /**
     * Vérifie que la date est un datetime valide
     *
     * @param  string $key
     * @param  string $format
     * @return self
     */
    public function datetime(string $key, string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($key);
        $date = DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();
        if ($errors['error_count'] > 0 ||  $errors['warning_count'] || $date === false) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }

    public function exist(string $key, string $table, PDO $pdo): self
    {
        $value = $this->getValue($key);
        $statement = $pdo->prepare("SELECT id FROM {$table} WHERE id = ?");
        $statement->execute([$value]);
        if ($statement->fetchColumn() === false) {
            $this->addError($key, 'exist', [$table]);
        }
        return $this;
    }

    /**
     * Vérifie qu'une clée est uniqeue
     *
     * @param  string $key
     * @param  string|Table $table
     * @param  PDO $pdo
     * @param  int|null $exclude
     * @return self
     */
    public function unique(string $key, $table, ?PDO $pdo = null, ?int $exclude = null): self
    {
        if ($table instanceof Table) {
            $pdo = $table->getPdo();
            $table = $table->getTable();
        }
        $value = $this->getValue($key);
        $query = "SELECT id FROM {$table} WHERE $key = ?";
        $params = [$value];
        if ($exclude !== null) {
            $query .= " AND id != ?";
            $params[] = $exclude;
        }
        $statement = $pdo->prepare($query);
        $statement->execute($params);
        if ($statement->fetchColumn() !== false) {
            $this->addError($key, 'unique', [$value]);
        }
        return $this;
    }

    /**
     * Renvoie les erreurs
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Vérifie s'il n'y a pas d'erreurs
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Ajoute une Erreur
     *
     * @param  string $key
     * @param  string $rule
     * @return void
     */
    private function addError(string $key, string $rule, array $attributes = [])
    {
        $this->errors[$key] = new ValidatorErrors($key, $rule, $attributes);
    }

    /**
     * Renvoie la clé ou null
     *
     * @param  string $key
     */
    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }
}
